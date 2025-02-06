<?php

namespace App\Filament\Clusters\Exams\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Exams\Exam;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamTaskScore;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Enums\Exams\ExamAttemptStatus;
use App\Enums\Exams\ExamTraineeStatus;
use App\Models\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\DetachBulkAction;

class TraineesRelationManager extends RelationManager
{
    protected static string $relationship = 'trainees';

    protected static ?string $title = 'Participants';

    protected ?string $heading = 'Trainees allowed to take the exam';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('trainee_alias')
                    ->maxLength(255),
                Select::make('exam_location')
                    ->options(function () {
                            $exam = Exam::first();
                            $locations = $exam->exam_location ?? [];
                            return array_combine($locations, $locations); 
                        }),
                TextInput::make('internal_notes')
                    ->maxLength(255)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('last_name')
            ->searchPlaceholder('Seach (name, email)')
            ->columns([
                TextColumn::make('last_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('first_name')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('activeGroup.group.group_code')
                    ->label('Group'),
                TextInputColumn::make('trainee_alias')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('The code the trainee uses in exam papers, rather than their name. E.g. "126-12"')
                    // https://filamentphp.com/docs/3.x/panels/resources/relation-managers#accessing-the-relationships-owner-record
                    ->hidden(fn ($livewire) => $livewire->getOwnerRecord()->type !== 'exam'),
                SelectColumn::make('exam_location')
                    ->label('Confirmed venue')
                    ->placeholder('No venue')
                    ->sortable()
                    ->options(function ($record) {
                        $examId = $record->pivot->exam_id;
                        $exam = Exam::find($examId);
                        $locations = $exam->exam_locations ?? [];
                        return array_combine($locations, $locations);
                    })
                    ->hidden(fn ($livewire) => $livewire->getOwnerRecord()->type !== 'exam'),
                SelectColumn::make('status')
                    ->label('Attendance status')
                    ->sortable()
                    ->selectablePlaceholder(false)
                    ->options(ExamTraineeStatus::class)
                    ->hidden(fn ($livewire) => $livewire->getOwnerRecord()->type !== 'exam'),
                TextInputColumn::make('notes')
                    ->label('Internal notes')
                    ->toggleable(),
                TextColumn::make('examAttempts.earliest_next_attempt')
                    ->wrap()
                    ->date()
                    ->label('Earliest next attempt')
                    ->hidden(fn ($livewire) => $livewire->getOwnerRecord()->type === 'exam'),
            ])
            ->defaultSort('last_name', 'asc')
            ->filters([
                SelectFilter::make('group_id')
                    ->label('Filter by group')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->isActive()
                                ->whereHas('activeGroups', fn (Builder $query) =>
                                    $query->whereNot('name', 'Kyl mä hoidan')
                                        ->where('groups.id', $data['value'])
                                );
                        }
                    })
                    ->options(fn () =>
                        Group::selectRaw('groups.id, CONCAT(group_types.code, name) AS name, groups.active')
                            ->join('group_types', 'group_types.id', 'groups.group_type_id')
                            ->isActive()
                            ->whereNot('name', 'Kyl mä hoidan')
                            ->orderBy('name', 'ASC')
                            ->get()
                            ->pluck('name', 'id')
                    )
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add trainees')
                    ->modalHeading('Add trainees to this exam')
                    ->multiple()
                    ->recordSelectSearchColumns(['last_name', 'users.email'])
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query->select('trainees.id', 'first_name', 'last_name', 'user_id')
                            // Joining is needed to get the email since it's not in the trainee table
                            ->join('users', 'trainees.user_id', '=', 'users.id')
                            ->where('trainees.active', true)
                            ->addSelect('users.email');
                    })
                    ->recordTitle(fn ($record) => "{$record->last_name}, {$record->first_name} ({$record->email})"),
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    // This lets the staff import scores for trainees who need to repeat one section but nto the whole exam
                    Action::make('ImportScores')
                        ->label('Import old scores')
                        ->color('primary')
                        ->icon('heroicon-o-arrow-down-on-square')
                        ->modalHeading(fn ($record) => "Import old scores for {$record->last_name}, {$record->first_name}" )
                        ->modalSubmitActionLabel('Import scores')
                        ->hidden(fn ($livewire) => $livewire->getOwnerRecord()->type !== 'exam')
                        ->form([
                            Select::make('sourceExamId')
                                ->label('Select source exam')
                                ->helperText('This only lists past exams where the trainee has a recorded attempt and did not pass.')
                                ->options(function ($record) {
                                    $currentExamDate = Exam::find($record->exam_id)->date;
                                
                                    return Exam::where('type', 'exam')
                                        ->where('id', '!=', $record->exam_id)
                                        ->whereHas('attempts', function ($query) use ($record) {
                                            $query->where('trainee_id', $record->trainee_id)
                                                ->whereNot('status', ExamAttemptStatus::PASSED->value);
                                        })
                                        ->where('date', '<', $currentExamDate)
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('sourceSectionId', $state)),
                            Radio::make('sourceSectionId')
                                ->label('Select the section whose scores you want to import')
                                ->inline()
                                ->inlineLabel(false)
                                ->hidden(fn (Get $get) => !$get('sourceExamId'))
                                ->options(function (Get $get) {
                                    $sourceExamId = $get('sourceExamId');
                                    if ($sourceExamId) {
                                        $exam = Exam::with('sections')->find($sourceExamId);
                                        
                                        if ($exam) {
                                            return $exam->sections->pluck('short_name', 'id');
                                        }
                                    }
                                    return [];
                                })
                                ->required(),
                        ])
                        ->action(function ($record, $data) {
                            // dd($record->pivot->exam_id);
                            $examId = $record->pivot->exam_id;
                            $traineeId = $record->trainee_id;

                            $sourceExamId = $data['sourceExamId'];
                            $sourceSectionId = $data['sourceSectionId'];
                            
                            $exam = Exam::with('sections')->find($examId);
                            $examDate = $exam->date;
                            $sourceExam = Exam::with('sections.tasks')->find($sourceExamId);
                            $tasks = $sourceExam->sections->find($sourceSectionId)->tasks->pluck('id')->toArray();

                            $pastAttempt = ExamAttempt::where('trainee_id', $traineeId)
                                ->where('exam_id', $sourceExamId)
                                ->first();
                
                            $taskScores = ExamTaskScore::whereIn('exam_task_id', $tasks)
                                ->where('trainee_id', $traineeId)
                                ->get();

                            // Prevent importing scores after the exam as this could overwrite whatever the graders are entering
                            if ($examDate) {
                                $examDateCarbon = Carbon::parse($examDate);
                            
                                if ($examDateCarbon->isToday()) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Cannot import scores on the day of the exam anymore.')
                                        ->danger()
                                        ->send();
                                    return;
                                } elseif ($examDateCarbon->isPast()) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Cannot import scores. The exam already took place.')
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }
                   
                            $examAttempt = ExamAttempt::updateOrCreate([
                                'trainee_id' => $traineeId,
                                'exam_id' => $examId,
                                'instructor_id' => $pastAttempt->instructor_id,
                            ],
                            [
                                'status' => ExamAttemptStatus::PENDING->value,
                                'is_published' => false,
                                'date' => $examDate,
                            ]);
                    
                            foreach ($taskScores as $taskScore) {
                                ExamTaskScore::updateOrCreate(
                                    [
                                        'exam_task_id' => $taskScore->exam_task_id,
                                        'trainee_id' => $traineeId,
                                        'exam_attempt_id' => $examAttempt->id,
                                    ],
                                    [
                                        'instructor_id' => $taskScore->instructor_id,
                                        'score' => $taskScore->score,
                                    ]
                                );
                            }

                            Notification::make()
                                ->title('Success')
                                ->body('Old scores imported successfully. Check the results list.')
                                ->success()
                                ->send();

                            // Leaving a papertrail...
                            $currentUser = Auth::user();
                            $currentUserId = $currentUser->id;
                            $currentUserName = $currentUser->name;

                            Log::info("Imported scores for trainee {$record->user->email} (trainee id $traineeId) from exam ID $sourceExamId to exam ID $examId. Imported by user $currentUserName (id: $currentUserId).");
                        }),

                    DetachAction::make()
                        ->label('Remove trainee')
                        ->modalHeading('Remove trainee?')
                        ->modalDescription(fn ($record) => "This removes $record->first_name $record->last_name from the exam. They will not be listed in the roster, and if this is a test or an assessment, the system prevents instructors from assigning a grade to this trainee for this particular exam. If the trainee has existing grades or proficiencies for this exam, they will not be deleted.")
                        ->modalSubmitActionLabel('Yes, remove the trainee'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    DetachBulkAction::make()
                        ->label('Remove selected trainees')
                ]),
            ]);
    }
}

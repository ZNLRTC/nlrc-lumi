<?php

namespace App\Filament\Clusters\Exams\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamTrainee;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Enums\Exams\ExamAttemptStatus;
use App\Enums\Exams\ExamTraineeStatus;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    protected static ?string $title = 'Results';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Outcome of the exam')
                    ->options(ExamAttemptStatus::class)
                    ->selectablePlaceholder(false)
                    ->disabled(fn ($record) => $record->is_published),
                Toggle::make('is_published')
                    ->label('Result published')
                    ->helperText('Turn this on if you want to publish the results to the trainee. You cannot change the passed/not passed setting after publishing. If you publish results with the trainee passed, the trainee will be marked as proficient in the level of the exam.'),
                DateTimePicker::make('date')
                    ->label('Date taken')
                    ->native(false)
                    ->required()
                    ->helperText('The date the trainee took the exam. This is used in certificates, so make sure this is right.'),
                DatePicker::make('earliest_next_attempt')
                    ->helperText('This is the earliest date the trainee can take the exam again. The system will not allow an instructor to assign a grade for the trainee in this exam before this date.')
                    ->native(false)
                    ->afterOrEqual('date')
                    ->hidden(fn ($record) => $record->exam->type == 'exam'),
                Textarea::make('feedback')
                    ->label('Instructor\'s feedback')
                    ->helperText('Do not edit this unless the instructor asks you to change it.')
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->hidden(fn ($record) => $record->exam->type == 'exam'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Seach (name, email)')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('trainee.last_name')
                    ->label('Last name')
                    ->wrap()
                    ->verticallyAlignStart()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('trainee.first_name')
                    ->label('First name')
                    ->wrap()
                    ->verticallyAlignStart()
                    ->searchable(),
                TextColumn::make('trainee.user.email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->searchable()
                    ->verticallyAlignStart(),
                ViewColumn::make('scores')
                    ->label('Scores (click to show details, click on individual scores to edit)')
                    ->verticallyAlignStart()
                    ->view('filament.custom.exam-results-column'),
                SelectColumn::make('status')
                    ->label('Outcome')
                    ->options(ExamAttemptStatus::class)
                    ->selectablePlaceholder(false)
                    ->disabled(function ($record) {
                        $examTraineeStatus = ExamTrainee::where('trainee_id', $record->trainee_id)
                            ->where('exam_id', $record->exam_id)
                            ->value('status');
                    
                        if ($examTraineeStatus === null) {
                            return $record->is_published;
                        }
                    
                        $bothAbsent = $examTraineeStatus->value === ExamTraineeStatus::ABSENT->value && $record->status->value === ExamAttemptStatus::ABSENT->value;
                    
                        return $bothAbsent || $record->is_published;
                    })
                    ->verticallyAlignStart(),
                TextColumn::make('feedback')
                    ->label('Instructor\'s feedback')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->verticallyAlignStart(),
                ToggleColumn::make('is_published')
                    ->label('Result published')
                    ->wrapHeader()
                    ->verticallyAlignStart(),
                TextColumn::make('date')
                    ->label('Date taken')
                    ->sortable()
                    ->verticallyAlignStart()
                    ->wrap()
                    ->wrapHeader()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('earliest_next_attempt')
                    ->label('Earliest next attempt')
                    ->sortable()
                    ->verticallyAlignStart()
                    ->wrap()
                    ->wrapHeader()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->label('Publish selected')
                        ->action(function (Collection $records) {
                            $records->each->update(['is_published' => true]);
                        })
                        ->icon('heroicon-o-arrow-up-on-square')
                        ->requiresConfirmation()
                        ->modalDescription('This will publish the results to the selected trainees. You cannot change the passed/not passed setting after publishing. Do you want to continue?')
                        ->modalSubmitActionLabel('Yes, publish the results')
                        ->color('success'),

                    BulkAction::make('mark-as-passed')
                        ->label('Mark selected as passed')
                        ->action(function (Collection $records) {
                            $records->filter(function ($record) {
                                return !$record->is_published;
                            })->each->update(['status' => ExamAttemptStatus::PASSED->value]);
                        })
                        ->icon('heroicon-s-check-circle')
                        ->requiresConfirmation()
                        ->modalDescription('This marks the selected trainees as having passed the exam. You can change this as long you have not published the results. This does ignores any trainees whose results have been published. Do you want to continue?')
                        ->modalSubmitActionLabel('Yes, mark them as passed'),
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->can('bulkDelete', ExamAttempt::class)),
                ]),
            ])
            // This prevents the row action from happening, or otherwise the edit modal pops up every time you try to expand score details
            ->recordAction(null);
    }
}

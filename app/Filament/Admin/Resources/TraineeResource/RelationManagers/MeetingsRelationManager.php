<?php

namespace App\Filament\Admin\Resources\TraineeResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Courses\Unit;
use App\Models\Meetings\Meeting;
use Filament\Actions\CreateAction;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use App\Models\Meetings\MeetingStatus;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetingTrainees';

    protected static ?string $title = 'Meeting history';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Course, unit, and meeting')
                    ->description('Choose the course, then the unit, and finally the meeting.')
                    ->columns(3)
                    ->schema([
                        Select::make('courses')
                            // The getRecordOwner() method is not available here cos of the static form method so this workaround is from Filament docs
                            ->label('Course')
                            ->options(function (RelationManager $livewire): array {
                                return $livewire->getOwnerRecord()->activeGroup->group->courses
                                    ->pluck('name', 'id')
                                    ->toArray();
                                })
                            ->live(),
                        Select::make('units')
                            ->label('Unit')
                            ->options(fn (Get $get): Collection => Unit::query()
                                ->where('course_id', $get('courses'))
                                ->pluck('name', 'id'))
                                ->live(),
                        Select::make('meeting_id')
                            ->label('Meeting type')
                            ->options(fn (Get $get): Collection => Meeting::query()
                                ->where('unit_id', $get('units'))
                                ->pluck('description', 'id'))
                            ->required(),
                            // ->hiddenOn('edit'),
                    ])
                    ->hiddenOn('edit'),
                
                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->required(),
                        Radio::make('meeting_status_id')
                            ->label('Outcome')
                            ->options(MeetingStatus::pluck('name', 'id')->toArray())
                            ->required(),
                        Textarea::make('feedback')
                            ->label('Feedback for the trainee')
                            ->minLength(5)
                            ->maxLength(500)
                            ->required(),
                        Textarea::make('internal_notes')
                            ->label('Notes, not visible to trainee')
                            ->minLength(5)
                            ->maxLength(500),
                        Select::make('instructor_id')
                            ->label('Instructor')
                            ->options(
                                User::whereHas('role', function ($query) {
                                    $query->where('name', 'Instructor');
                                })
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->required(),
                    ])
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('meeting_id')
            ->emptyStateHeading('No meetings')
            ->emptyStateDescription('Maybe you filtered them all out? Or this trainee just started recently?')
            ->description('This lists all meetings the trainee has had.')
            ->columns([
                    TextColumn::make('meeting.unit.name')
                        ->sortable(),
                    TextColumn::make('meeting.description')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Meeting type'),
                    TextColumn::make('meetingStatus.name')
                        ->label('Outcome')
                        ->badge()
                        ->color(fn (string $state): string => match ($state){
                            'Completed' => 'success',
                            'Incomplete' => 'danger',
                            'No-show' => 'gray',
                        })
                        ->sortable(),
                    TextColumn::make('date')
                        ->date('M j, Y')
                        ->sortable(),
                    TextColumn::make('instructor.name'),
                    ViewColumn::make('Feedback and notes')
                        ->view('filament.custom.feedback-column'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('only_show_completed_units')
                    ->query(fn (Builder $query) =>
                        $query->where('trainee_id', $this->getOwnerRecord()->id)
                            ->where('meeting_status_id', 1)
                    )
                    ->toggle(),
                Filter::make('filterOutCompletedUnits')
                    ->query(function (Builder $query) {
                        $traineeId = $this->getOwnerRecord()->id;
                        return $query->whereNotIn('meeting_id', function($query) use ($traineeId) {
                            $query->select('meeting_id')
                                ->from('meeting_trainee')
                                ->where('meeting_status_id', 1)
                                ->where('trainee_id', $traineeId);
                        });
                    })
                    ->toggle()
                    ->label('Only show incomplete units'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add a new meeting')
                    ->modalHeading('Add a new meeting')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Saved')
                            ->body('The meeting was saved successfully.'),
                    )
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit the meeting'),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Delete the meeting'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

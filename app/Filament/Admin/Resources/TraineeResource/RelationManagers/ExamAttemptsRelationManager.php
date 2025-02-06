<?php

namespace App\Filament\Admin\Resources\TraineeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Exams\ExamTrainee;
use Filament\Tables\Grouping\Group;
use App\Enums\Exams\ExamAttemptStatus;
use App\Enums\Exams\ExamTraineeStatus;
use Faker\Provider\ar_EG\Text;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ExamAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'examAttempts';

    protected static ?string $title = 'Exam history';

    protected static ?string $recordTitleAttribute = 'exam.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('earliest_next_attempt')
                    ->label('Earliest next attempt')
                    ->timezone('Asia/Manila')
                    ->minDate(now()->subDays(1))
                    ->required()
                    ->displayFormat('m-d-Y'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('This lists the trainee\'s attempts in tests, assessments, and exams.')
            ->recordTitleAttribute('id')
            // ->defaultGroup('exam.type')
            ->groups([
                Group::make('exam.type')
                    ->label('Exam type')
            ])
            ->columns([
                TextColumn::make('exam.name')
                    ->sortable()
                    ->verticallyAlignStart(),
                TextColumn::make('exam.type')
                    ->label('Exam type')
                    ->verticallyAlignStart()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->label('Date taken')
                    ->sortable()
                    ->verticallyAlignStart(),
                ViewColumn::make('scores')
                    ->label('Scores (click to show details)')
                    ->verticallyAlignStart()
                    ->view('filament.custom.exam-results-column')
                    ->verticallyAlignStart(),
                SelectColumn::make('status')
                    ->label('Outcome')
                    ->options(ExamAttemptStatus::class)
                    ->selectablePlaceholder(false)
                    ->verticallyAlignStart()
                    ->disabled(fn ($record) => $record->is_published),
                ToggleColumn::make('is_published')
                    ->label('Result published')
                    ->verticallyAlignStart()
                    ->wrapHeader(),
                TextColumn::make('feedback')
                    ->label('Instructor\'s feedback')
                    ->verticallyAlignStart()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('internal_notes')
                    ->label('Internal notes')
                    ->verticallyAlignStart()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('earliest_next_attempt')
                    ->label('Earliest next attempt')
                    ->wrapHeader()
                    ->date()
                    ->verticallyAlignStart(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Next attempt')
                    ->modalHeading('Change next attempt')
                    ->modalDescription(fn ($record) => "Set the earliest date the trainee can take this type of {$record->exam->type} again.")
                    ->hidden(fn ($record) => $record->exam->type === 'exam'),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            // This prevents the row action from happening, or otherwise the edit modal pops up every time you try to expand score details
            ->recordAction(null);
    }
}

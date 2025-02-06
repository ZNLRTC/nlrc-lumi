<?php

namespace App\Filament\Admin\Resources\TraineeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Exams\Exam;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Enums\Exams\ExamTraineeStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'examTrainees';

    protected static ?string $title = 'Exam permissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_id')
                    ->label('Select exam')
                    ->options(
                        Exam::where('date', '>', Carbon::now())
                            ->orWhereNull('date')
                            ->pluck('name', 'id')
                    )
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Exam permissions')
            ->description('If a test, an assessment, or an exam is listed here, the trainee is allowed to take it. This allows instructors to search for and grade the trainee. There might be other restrictions in place, such as a cooldown period after a failed attempt. You can manage those in the exam history below. Deleting an entry here will not affect the trainee\'s exam history. It just prevents them from taking the exam.')
            ->emptyStateHeading('No permissions')
            ->emptyStateDescription('This trainee is not allowed to take any tests, assessments, or exams.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Add new permission')
                    ->button()
            ])
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('exam.name')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Attendance status')
                    ->tooltip('This has no effect on assessments and tests. It is only relevant for exams.'),
                TextColumn::make('date')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add new permission')
                    ->modalHeading('Add new permission')
                    ->modaldescription('This will allow the trainee to take the exam. Instructors will be able to search for the the trainee\'s name and will be able to grade them.')
                    ->modalSubmitActionLabel('Add permission')
                    ->createAnother(false),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove permission')
                    ->modalHeading('Remove permission?')
                    ->modalDescription('This will prevent the instructors from assigning grades to this trainee for this exam. Their earlier attempts will not be affected. Do you want to remove the permission?')
                    ->modalSubmitActionLabel('Yes, remove permission'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

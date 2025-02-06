<?php

namespace App\Filament\Clusters\Exams\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Exams\ExamTask;
use App\Filament\Clusters\Exams;
use Filament\Resources\Resource;
use App\Models\Exams\ExamSection;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Exams\Resources\ExamSectionResource\Pages;
use App\Filament\Clusters\Exams\Resources\ExamSectionResource\RelationManagers;

class ExamSectionResource extends Resource
{
    protected static ?string $model = ExamSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Exams::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('passing_percentage')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
                Select::make('tasks.id')
                    ->multiple()
                    ->relationship(name: 'tasks', titleAttribute: 'name')
                    ->options(ExamTask::all()->pluck('name', 'id')->toArray())
                    ->label('Tasks that this section contains')
                    ->helperText('Tasks are individual tasks or parts of the exam. They are usually graded by a single instructor. Tasks can be shared between sections. Assessments only have one section, which should contain all tasks in the assessment.')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('passing_percentage')
                    ->numeric()
                    ->suffix('%')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamSections::route('/'),
            'create' => Pages\CreateExamSection::route('/create'),
            'edit' => Pages\EditExamSection::route('/{record}/edit'),
        ];
    }
}

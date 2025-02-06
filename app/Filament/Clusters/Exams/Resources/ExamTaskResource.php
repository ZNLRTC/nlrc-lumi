<?php

namespace App\Filament\Clusters\Exams\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Exams\ExamTask;
use Faker\Provider\ar_EG\Text;
use App\Filament\Clusters\Exams;
use Filament\Resources\Resource;
use App\Models\Exams\ExamSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Exams\Resources\ExamTaskResource\Pages;
use App\Filament\Clusters\Exams\Resources\ExamTaskResource\RelationManagers;

class ExamTaskResource extends Resource
{
    protected static ?string $model = ExamTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Exams::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('description'),
                        Select::make('sections.id')
                            ->multiple()
                            ->columnSpanFull()
                            ->label('Sections that use this task')
                            ->helperText('This defines which sections use this task. Sections are collections of tasks that exams and assessments have. Sections are scored individually. For exams, the sections are the written and the oral section. Assessments only consist of one section, which contains all tasks in the assessment.')
                            ->relationship(name: 'sections', titleAttribute: 'name')
                            ->options(ExamSection::all()->pluck('name', 'id')->toArray())
                            ->required(),
                    ]),

                Section::make('Scoring')
                    ->columns(3)
                    ->schema([
                        TextInput::make('max_score')
                            ->numeric()
                            ->maxValue(999)
                            ->required()
                            ->gt('min_score'),
                        TextInput::make('min_score')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('passing_score')
                            ->hidden(fn (Get $get): bool => ! $get('mandatory_to_pass'))
                            ->numeric()
                            ->gt('min_score')
                            ->lte('max_score'),
                        Checkbox::make('mandatory_to_pass')
                            ->columnSpan(2)
                            ->helperText('If this is checked, the trainee is unable to pass the section unless they pass this task, regardless of their score in other tasks. If the exam or the assessment only has one section, the trainee is unable to pass the exam if they fail this task.')
                            ->live(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('max_score'),
                TextColumn::make('passing_score'),
                CheckboxColumn::make('mandatory_to_pass')
                    ->disabled(),
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
            'index' => Pages\ListExamTasks::route('/'),
            'create' => Pages\CreateExamTask::route('/create'),
            'edit' => Pages\EditExamTask::route('/{record}/edit'),
        ];
    }
}

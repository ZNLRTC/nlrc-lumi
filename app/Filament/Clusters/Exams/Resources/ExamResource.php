<?php

namespace App\Filament\Clusters\Exams\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Exams\Exam;
use Filament\Tables\Table;
use Faker\Provider\ar_EG\Text;
use App\Filament\Clusters\Exams;
use Filament\Resources\Resource;
use App\Models\Exams\ExamSection;
use App\Models\Exams\Proficiency;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Filament\Clusters\Exams\Resources\ExamResource\Pages;
use App\Filament\Clusters\Exams\Resources\ExamResource\RelationManagers;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Actions\ActionGroup;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Exams::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Exam name')
                            ->required()
                            ->maxLength(255),
                        Select::make('proficiency_id')
                            ->label('Level whose proficiency this exam tests')
                            ->required()
                            ->relationship(name: 'proficiency', titleAttribute: 'name')
                            ->options(Proficiency::all()->pluck('name', 'id')->toArray()),
                        Select::make('type')
                            ->required()
                            ->helperText('This defines the grading view instructors get to see. It does not affect the exam content.')
                            ->live()
                            ->options([
                                'exam' => 'Exam',
                                'assessment' => 'Assessment',
                                'test' => 'Test',
                            ])
                            // This doesn't remove the date if the type is changed after the date already has been set since the field will be hidden and not sent to the server
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state !== 'Exam') {
                                    $set('date', null);
                                }
                            }),
                        DatePicker::make('date')
                            ->hidden(fn (Get $get) => ($get('type') !== 'exam'))
                            ->label('Exam date'),
                        TagsInput::make('exam_locations')
                            ->label('Exam locations')
                            ->placeholder('Add exam locations')
                            ->helperText('Add physical locations where this exam will be held. Press enter after each entry. Double click the field to see suggestions.')
                            ->suggestions([
                                'Quezon City',
                                'Baguio',
                            ])
                            ->hidden(fn (Get $get) => ($get('type') !== 'exam')),

                    ]),
                Section::make('Content')
                    ->columns(1)
                    ->schema([
                        Select::make('sections.id')
                            ->multiple()
                            ->relationship(name: 'sections', titleAttribute: 'name')
                            ->options(ExamSection::all()->pluck('name', 'id')->toArray())
                            ->helpertext('Select the sections that this exam will have. Sections contain tasks, which dictate the content of the exam. Default assessments and unit 1–5 test only have one section, named after the exam.'),
                    ]),

                Section::make('Grading')
                    ->description('Regardless of the settings here, instructors can only see the exam if its date was less than a week ago.')
                    ->columns(2)
                    ->schema([
                        Checkbox::make('any_instructor_can_grade')
                            ->label('Any instructor can assign grades for this')
                            ->helperText('Ticking this will override the allowed instructors list if there was one.')
                            ->live()
                            ->default(false),
                        Select::make('allowed_instructors')
                            ->label('Instructors allowed to grade this')
                            ->multiple()
                            ->options(User::whereHas('role', function($query) {
                                $query->whereIn('name', ['Instructor', 'Editing instructor']);
                                })->pluck('name', 'id'))
                            ->hidden(fn (Get $get): bool => $get('any_instructor_can_grade')),
                        TextInput::make('exam_paper_url')
                            ->label('URL of the exam papers on Google Drive')
                            ->columnSpanFull()
                            ->hidden(fn (Get $get) => ($get('type') !== 'exam')),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
            ->persistSortInSession()
            ->searchPlaceholder('Search by exam name')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'exam' => 'Exam',
                        'assessment' => 'Assessment',
                        'test' => 'Test',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    // I can't get the pivot table replication to work with the built-in replicate action so this is what we're rolling with --Mikko
                    Action::make('Duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Exam $record) => "Make a blank copy of $record->name?")
                        ->modalDescription(fn (Exam $record) => "This makes a blank duplicate of $record->name with the same sections and tasks but without student data or scores. This is not a backup tool. Do you want to make a blank duplicate?")
                        ->modalSubmitActionLabel('Yes, make a duplicate')
                        ->action(function (Exam $originalExam) {
                            $newExam = $originalExam->replicate();
                            $newExam->name = "$originalExam->name (blank copy)";
                            $newExam->save();
                    
                            $newExamId = $newExam->id;
                    
                            foreach ($originalExam->sections as $section) {
                                $pivotData = $section->pivot->toArray();
                                $pivotData['exam_id'] = $newExamId;
                    
                                $newExam->sections()->attach($section->id, $pivotData);
                            }
                    }),
                    Tables\Actions\EditAction::make()
                        ->color('primary'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TraineesRelationManager::class,
            RelationManagers\AttemptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}

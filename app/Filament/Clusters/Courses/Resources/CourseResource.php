<?php

namespace App\Filament\Clusters\Courses\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Courses\Unit;
use App\Models\Courses\Topic;
use App\Models\Courses\Course;
use Filament\Resources\Resource;
use App\Filament\Clusters\Courses;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Courses\Resources\CourseResource\Pages;
use App\Filament\Clusters\Courses\Resources\CourseResource\RelationManagers;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $cluster = Courses::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Course information for trainees')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Course description')
                            ->rows(5),
                    ]),

                Section::make('Internal information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('internal_name')
                            ->maxLength(255)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->live(onBlur: true),
                        TextInput::make('slug')
                            ->helperText('The slug is used to generate the URL for the course, e.g. https://nlrc.ph/courses/slug. Must be at least 3 characters long and may only contain letters, numbers, and dashes.')
                            ->required()
                            ->unique(table: Course::class, ignoreRecord: true)
                            ->minLength(3)
                            ->maxLength(100)
                            ->regex('/^[a-z0-9-]+$/i')
                            ->validationMessages([
                                'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                'unique' => 'This slug exists already. The slug must be unique across all courses at NLRC.'
                            ]),
                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Units')
                    ->schema([
                        Repeater::make('units')
                            ->relationship()
                            ->orderColumn('sort')
                            ->collapsed()
                            ->addActionLabel('Add a new unit')
                            ->reorderableWithButtons()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading('Delete this unit?')
                                    ->modalDescription('Are you sure you want to delete topic? This also deletes related meetings and assignments. You cannot undo this.')
                                    ->modalSubmitActionLabel('Yes, delete unit')
                                )
                            ->schema([
                                Section::make('Unit information for trainees')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')
                                            ->label('Unit description')
                                            ->maxLength(65353)
                                            ->rows(5),
                                    ]),

                                Section::make('Internal information')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('internal_name')
                                            ->maxLength(255)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                            ->live(onBlur: true),
                                        TextInput::make('slug')
                                            ->helperText('The slug is used to generate the URL for the unit, e.g. https://nlrc.ph/courses/best-course/slug')
                                            ->required()
                                            ->unique(table: Unit::class, ignoreRecord: true)
                                            ->minLength(3)
                                            ->maxLength(100)
                                            ->regex('/^[a-z0-9-]+$/i')
                                            ->validationMessages([
                                                'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                                'unique' => 'This slug exists already. The slug must be unique across all courses at NLRC.'
                                            ]),
                                        Textarea::make('internal_description')
                                            ->label('Internal notes')
                                            ->rows(2),
                                    ]),

                                    Section::make('Content')
                                        ->columns(1)
                                        ->schema([
                                            Repeater::make('topics')
                                                ->relationship()
                                                ->columns(3)
                                                ->orderColumn('sort')
                                                ->collapsed()
                                                ->addActionLabel('Add a new topic')
                                                ->reorderableWithButtons()
                                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                                ->deleteAction(
                                                    fn (Action $action) => $action
                                                        ->requiresConfirmation()
                                                        ->modalHeading('Delete this topic?')
                                                        ->modalDescription('Are you sure you want to delete topic? You cannot undo this unless you have a local backup of the contents.')
                                                        ->modalSubmitActionLabel('Yes, delete topic')
                                                    )
                                                ->schema([
                                                    TextInput::make('title')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                                        ->live(onBlur: true),
                                                    TextInput::make('description')
                                                        ->label('Subheading')
                                                        ->maxLength(255),
                                                    TextInput::make('slug')
                                                        ->helperText('The slug will be part of the URL in the course index, e.g. https://nlrc.ph/courses/course-1/unit-1#slug')
                                                        ->required()
                                                        ->unique(ignoreRecord: true)
                                                        ->minLength(3)
                                                        ->maxLength(100)
                                                        ->regex('/^[a-z0-9-]+$/i')
                                                        ->validationMessages([
                                                            'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                                            'unique' => 'This slug exists alrealdy. The slug must be unique across all courses at NLRC.'
                                                        ]),
                                                    Textarea::make('content')
                                                        ->required()
                                                        ->rows(20)
                                                        ->extraAttributes(['class' => 'font-mono'])
                                                        ->label('HTML content')
                                                        ->columnSpanFull(),
                                                ]),
                                    ]),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('internal_name')
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('internal_notes'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

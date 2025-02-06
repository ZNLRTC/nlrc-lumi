<?php

namespace App\Filament\Clusters\Courses\Resources;

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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Courses\Resources\UnitResource\Pages;
use App\Filament\Clusters\Courses\Resources\UnitResource\RelationManagers\AssignmentsRelationManager;
use App\Filament\Clusters\Courses\Resources\UnitResource\RelationManagers\MeetingsRelationManager;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Courses::class;

    public static function form(Form $form): Form
    {
        return $form
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
                        Select::make('course_id')
                            ->label('The course this unit is a part of')
                            ->selectablePlaceholder(false)
                            ->options(Course::all()->pluck('name', 'id')->toArray())
                            ->required(),
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
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->columns(1)
                    ->schema([
                        Repeater::make('topics')
                            ->relationship()
                            ->columns(3)
                            ->orderColumn('sort')
                            ->collapsible()
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
                                    ->unique(table: Topic::class, ignoreRecord: true)
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->regex('/^[a-z0-9-]+$/i')
                                    ->validationMessages([
                                        'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                        'unique' => 'This slug exists alrealdy. The slug must be unique across all courses at NLRC.'
                                    ]),
                                TextArea::make('content')
                                    ->required()
                                    ->rows(20)
                                    ->extraAttributes(['class' => 'font-mono'])
                                    ->label('HTML content')
                                    ->columnSpanFull(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('internal_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Is a part of'),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable(),
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
            AssignmentsRelationManager::class,
            MeetingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            // 'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}

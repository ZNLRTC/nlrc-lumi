<?php

namespace App\Filament\Clusters\Planner\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Enums\Planner\ContentType;
use App\Filament\Clusters\Planner;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Models\Planner\PlannerCurriculum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Planner\Resources\CurriculumResource\Pages;
use App\Filament\Clusters\Planner\Resources\CurriculumResource\RelationManagers;

class CurriculumResource extends Resource
{
    protected static ?string $model = PlannerCurriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Curriculum';

    protected static ?string $cluster = Planner::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Enter the name of the curriculum'),
                    ]),
                Section::make()
                    ->schema([
                        Repeater::make('curriculumContents')
                            ->columns(2)
                            ->relationship()
                            ->label('Curriculum contents')
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->collapsed()
                            ->addActionLabel('Add more weeks')
                            ->orderColumn('sort')
                            ->itemLabel(fn (array $state): ?string => isset($state['sort']) ? 'Week ' . $state['sort'] : null)
                            ->schema([
                                Select::make('content_type')
                                    ->label('Content type')
                                    ->helperText('Select the type of content this week has.')
                                    ->live()
                                    ->selectablePlaceholder(false)
                                    ->options(ContentType::class)
                                    ->default(ContentType::DEFAULT->value)
                                    ->required(),
                                Section::make('Units and/or meetings')
                                    ->description('Choose units and/or meetings for this week. These are shown to the trainees in their schedule summary. Usually, each week has one unit to study and one meeting.')
                                    ->hidden(fn (Get $get): bool => !in_array($get('content_type'), [
                                        ContentType::DEFAULT->value,
                                        ContentType::UNIT_ONLY->value,
                                        ContentType::MEETING_ONLY->value,
                                    ]))
                                    ->columns(2)
                                    ->schema([
                                        Select::make('units')
                                            ->relationship(titleAttribute: 'internal_name')
                                            ->label('Units')
                                            ->multiple()
                                            ->hidden(fn (Get $get): bool => !in_array($get('content_type'), [
                                                ContentType::DEFAULT->value,
                                                ContentType::UNIT_ONLY->value,
                                            ])),
                                        Select::make('meetings')
                                            ->relationship(titleAttribute: 'description')
                                            ->label('Meetings')
                                            ->multiple()
                                            ->hidden(fn (Get $get): bool => !in_array($get('content_type'), [
                                                ContentType::DEFAULT->value,
                                                ContentType::MEETING_ONLY->value,
                                            ])),
                                    ]),
                                Section::make('Custom content')
                                    ->description('You may specify custom text that is shown to the trainees in their schedule summary. It may be shown as the only content, or you can show it in addition to unit and meeting listing. You must toggle the "Show custom content" switch to enable this.')
                                    ->hidden(fn (Get $get): bool => !in_array($get('content_type'), [
                                        ContentType::DEFAULT->value,
                                        ContentType::UNIT_ONLY->value,
                                        ContentType::MEETING_ONLY->value,
                                        ContentType::CUSTOM_CONTENT->value,
                                    ]))
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('show_custom_content')
                                            ->inline(false)
                                            ->label('Show custom content')
                                            ->helperText('You must enable this to show custom content alongside meetings and/or units. If the week\'s content is set to "Custom content", this setting does nothing, and the custom text in the next field is shown regardless.'),
                                        MarkdownEditor::make('custom_content')
                                            ->label('Text shown to trainees')
                                            ->placeholder('Enter the custom content for this week')
                                            ->toolbarButtons([
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'undo',
                                            ]),
                                    ])
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
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
            'index' => Pages\ListCurricula::route('/'),
            'create' => Pages\CreateCurriculum::route('/create'),
            'edit' => Pages\EditCurriculum::route('/{record}/edit'),
        ];
    }
}

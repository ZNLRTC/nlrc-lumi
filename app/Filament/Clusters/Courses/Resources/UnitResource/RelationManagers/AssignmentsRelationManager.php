<?php

namespace App\Filament\Clusters\Courses\Resources\UnitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Name')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(debounce: 500)
                            ->maxLength(255)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') === Str::slug($old)) {
                                    $set('slug', Str::slug($state));
                                }

                                if (($get('internal_name') ?? '') === $old) {
                                    $set('internal_name', $state);
                                }
                            }),
                        TextInput::make('internal_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('The slug is used to generate the URL for this assignment, e.g. (https://lumi.nlrc.ph/courses/main/unit-1/slug). It should be unique across all assignments and contain only letters, numbers, and dashes.')
                            ->regex('/^[a-z0-9-]+$/i')
                            ->validationMessages([
                                'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                'unique' => 'This slug exists already. The slug must be unique across all assignments on this platform.'
                            ]),
                        ]),
                Section::make('Description')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description')
                            ->label('Task description')
                            ->nullable()
                            ->rows(3),
                        Textarea::make('internal_notes')
                            ->nullable()
                            ->rows(3),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

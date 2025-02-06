<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DocumentResource\Pages;
use App\Filament\Admin\Resources\DocumentResource\RelationManagers;
use App\Models\Documents\Document;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationGroup = 'Document';

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(128),
                TextInput::make('internal_name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash', 'lowercase'])
                    ->maxLength(64),
                Textarea::make('description')
                    ->rows(4)
                    ->placeholder('Write any description here that will be shown to the trainees when they upload documents.')
                    ->columnSpanFull()
                    ->helperText('Notes visible to the trainees.'),
                Textarea::make('internal_notes')
                    ->rows(4)
                    ->placeholder('Write any notes here that will only be shown to admins.')
                    ->columnSpanFull()
                    ->helperText('Notes only visible to the admins.')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('internal_name')
                    ->searchable(),
                TextColumn::make('description')
                    ->words(5),
                TextColumn::make('internal_notes')
                    ->words(5),
                TextColumn::make('documentCount'),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle('Document deleted')
                    ->visible(function ($record): bool {
                        // Ensures that there are no more uploaded documents by trainees before the delete button shows up
                        $visible = $record->documentCount == 0;

                        return $visible;
                })
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}

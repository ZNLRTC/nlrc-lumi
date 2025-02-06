<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Models\KnowledgeBase\Category;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Clusters\KnowledgeBase;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource\Pages;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource\RelationManagers;

class KnowledgebaseCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = KnowledgeBase::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }
                    
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->label('Slug')
                    ->helperText('The slug is used to generate the URL for the category, e.g. https://nlrc.ph/help/slug/article-name')
                    ->minLength(3)
                    ->maxLength(100)
                    ->unique(table: Category::class, ignoreRecord: true)
                    ->regex('/^[a-z0-9-]+$/i')
                    ->validationMessages([
                        'regex' => 'The slug may only contain letters, numbers, and dashes.',
                        'unique' => 'This slug exists already. The slug must be unique across all categories.'
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Categories are used to group articles together. You can only delete a category if it has no articles.')
            ->columns([
                TextColumn::make('name')->label('Name'),
                TextColumn::make('slug')->label('Slug'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListKnowledgebaseCategories::route('/'),
            'create' => Pages\CreateKnowledgebaseCategory::route('/create'),
            'edit' => Pages\EditKnowledgebaseCategory::route('/{record}/edit'),
        ];
    }
}

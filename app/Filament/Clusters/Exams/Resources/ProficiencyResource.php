<?php

namespace App\Filament\Clusters\Exams\Resources;

use App\Filament\Clusters\Exams;
use App\Filament\Clusters\Exams\Resources\ProficiencyResource\Pages;
use App\Filament\Clusters\Exams\Resources\ProficiencyResource\RelationManagers;
use App\Models\Exams\Proficiency;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProficiencyResource extends Resource
{
    protected static ?string $model = Proficiency::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $cluster = Exams::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('description')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
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
            'index' => Pages\ListProficiencies::route('/'),
            'create' => Pages\CreateProficiency::route('/create'),
            'edit' => Pages\EditProficiency::route('/{record}/edit'),
        ];
    }
}

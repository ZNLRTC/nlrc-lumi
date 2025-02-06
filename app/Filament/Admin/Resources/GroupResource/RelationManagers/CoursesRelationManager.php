<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('This controls which courses are available for this group. It allows group members to access the courses and submit tasks in them. You do not need to list the KMH course here, as it is always available.')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Add access to a course')
                    ->multiple()
                    ->modalHeading('Add access to a course')
                    ->modalSubmitActionLabel('Add access')
                    ->attachAnother(false)
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->modalHeading('Remove access to the course')
                    ->label('Remove access'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->modalHeading('Remove access to selected courses')
                        ->label('Remove access'),
                ]),
            ]);
    }
}

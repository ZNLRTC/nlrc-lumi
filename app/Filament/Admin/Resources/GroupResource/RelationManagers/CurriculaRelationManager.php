<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Planner\PlannerCurriculum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ToggleColumn;

class CurriculaRelationManager extends RelationManager
{
    protected static string $relationship = 'curricula';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('This controls which curricula are available for this group in the schedule planner. It has no effect on trainees\'s course access. You can mark curricula as inactive to keep them on this list for record-keeping purposes.')
            ->recordTitleAttribute('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('name')
                    ->label('Curriculum name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make()
                //     ->label('Add Curriculum'),
                Tables\Actions\AttachAction::make()
                    ->label('Add curriculum to this group')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->label('Remove'),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

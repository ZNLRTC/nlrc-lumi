<?php

namespace App\Filament\Admin\Resources\TraineeResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TraineesRelationManager extends RelationManager
{
    protected static string $relationship = 'trainee_flags';

    protected static ?string $title = 'Flagged Trainees List';

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
            ->emptyStateHeading('There are no trainees with this flag.')
            ->columns([
                IconColumn::make('trainee.active')
                    ->label('Is trainee active?')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-s-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('trainee.last_name')
                    ->label('Last Name')
                    ->searchable(),
                TextColumn::make('trainee.first_name')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('trainee.user.email')
                    ->label('Email')
                    ->copyable()
                    ->copyMessage('Email copied to clipboard')
                    ->searchable(),
                TextColumn::make('trainee.activeGroup.group.group_code')
                    ->label('Group')
                    ->searchable(),
                TextColumn::make('trainee.date_deployment')
                    ->date()
                    ->label('Deployment Date'),
                TextColumn::make('trainee.agency.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('active')
                    ->label('Is trainee\'s latest flag?')
                    ->alignCenter()
                    ->tooltip('If the trainee was tagged with this flag recently')
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-o-x-circle',
                        1 => 'heroicon-s-check-circle'
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'danger',
                        1 => 'success'
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->words(5)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('internal_notes')
                    ->words(5)
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->groups([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}

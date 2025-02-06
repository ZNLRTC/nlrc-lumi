<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FlagResource\Pages;
use App\Filament\Admin\Resources\TraineeResource\RelationManagers\TraineesRelationManager;
use App\Models\Flag\Flag;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlagResource extends Resource
{
    protected static ?string $model = Flag::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $activeNavigationIcon = 'heroicon-s-flag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('flag_type_id')
                    ->relationship('flagType', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->required()
                    ->rows(4),
                Toggle::make('visible_to_trainee')
                    ->onColor('success')
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flagType.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                IconColumn::make('visible_to_trainee')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    }),
                IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('flaggedTraineesCount')
            ])
            ->filters([
                SelectFilter::make('flag_type_id')
                    ->relationship('flagType', 'name')
                    ->label('Flag type'),
                Filter::make('visible_to_trainee')
                    ->label('Only show flags that are visible to trainees')
                    ->query(fn (Builder $query): Builder => $query->where('visible_to_trainee', 1)),
                Filter::make('active')
                    ->default()
                    ->label('Only show active flags')
                    ->query(fn (Builder $query): Builder => $query->isActive())
            ])
            ->actions([
                Action::make('set_active')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Set flag as active')
                    ->requiresConfirmation()
                    ->modalHeading('Set flag as active?')
                    ->modalDescription('Are you sure you would like to set this flag to active?')
                    ->hidden(fn (Flag $record): bool => $record->active == 1)
                    ->action(function (Flag $record) {
                        $record->active = 1;

                        $record->save();
                    }),
                Action::make('set_inactive')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Set flag as inactive')
                    ->requiresConfirmation()
                    ->modalHeading('Set flag as inactive?')
                    ->modalDescription('Are you sure you would like to set this flag to inactive? When adding new flags to trainees, this will not show up as an option until set back to active')
                    ->hidden(fn (Flag $record): bool => $record->active == 0)
                    ->action(function (Flag $record) {
                        $record->active = 0;

                        $record->save();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TraineesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlags::route('/'),
            'create' => Pages\CreateFlag::route('/create'),
            'edit' => Pages\EditFlag::route('/{record}/edit'),
        ];
    }
}

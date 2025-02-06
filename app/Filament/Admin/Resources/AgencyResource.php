<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AgencyResource\Pages;
use App\Filament\Admin\Resources\AgencyResource\Pages\EditAgency;
use App\Filament\Admin\Resources\AgencyResource\Pages\ManageAgencyDocuments;
use App\Models\Agencies\Agency;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgencyResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $activeNavigationIcon = 'heroicon-s-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(column: 'name', ignoreRecord: true),
                Textarea::make('description')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('documentsCount'),
                IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-s-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
                TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime('M d, Y h:i:s A')
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Filter::make('active')
                    ->default()
                    ->label('Only show active agencies')
                    ->query(fn (Builder $query): Builder => $query->isActive())
            ])
            ->actions([
                EditAction::make(),
                Action::make('manage')
                    ->icon('heroicon-o-document')
                    ->color('success')
                    ->url(fn (Agency $record, $livewire): string => $livewire->getResource()::getUrl('documents', ['record' => $record]))
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Agency Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                InfolistGroup::make([
                                    TextEntry::make('name'),
                                    TextEntry::make('description')
                                ]),
                                InfolistGroup::make([
                                    TextEntry::make('created_at')
                                        ->badge()
                                        ->date()
                                        ->color('success'),
                                    TextEntry::make('updated_at')
                                        ->badge()
                                        ->date()
                                        ->color('warning')
                                ])
                            ])
                    ])
            ]);
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            ManageAgencyDocuments::class,
            EditAgency::class
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
            'index' => Pages\ListAgencies::route('/'),
            'create' => Pages\CreateAgency::route('/create'),
            'edit' => EditAgency::route('/{record}/edit'),
            'documents' => ManageAgencyDocuments::route('/{record}/documents')
        ];
    }
}

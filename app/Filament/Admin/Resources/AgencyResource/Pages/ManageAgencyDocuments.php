<?php

namespace App\Filament\Admin\Resources\AgencyResource\Pages;

use App\Filament\Admin\Resources\AgencyResource;
use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Documents\Document;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ManageAgencyDocuments extends ManageRelatedRecords
{
    protected static string $resource = AgencyResource::class;

    protected static string $relationship = 'documents';

    protected static ?string $title = 'Agency Documents';

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document';

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('document_id')
                    ->label('Document')
                    ->searchable()
                    ->required()
                    ->options(function (): ?Collection {
                        $documents_for_agency = AgencyDocumentRequired::where('agency_id', $this->getOwnerRecord()->id)->get()
                            ->pluck('document_id', 'id');

                        $documents = Document::select(['id', 'name'])
                            ->whereNotIn('id', $documents_for_agency->values())
                            ->orderBy('name', 'ASC')
                            ->get()
                            ->pluck('name', 'id');

                        return $documents;
                    })
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('document.name')
                    ->searchable()
                    ->label('Document'),
                TextColumn::make('document.internal_notes')
                    ->words(15)
                    ->label('Internal notes'),
                TextColumn::make('documentCountForAgency')
                    ->label('Uploaded documents count')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add new document')
                    ->modalHeading('Add new document')
                    ->createAnother(false)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('New document added')
                            ->body('This document can now be uploaded by trainees under this agency')
                    )
            ])
            ->actions([
                // TODO: TO ADD LOGIC TO ONLY SHOW IF THERE ARE NO DOCUMENTS UPLOADED FROM THIS AGENCY
                DeleteAction::make()
                    ->visible(fn (AgencyDocumentRequired $record): bool => $record->documentCountForAgency == 0)
            ])
            ->bulkActions([
                //
            ]);
    }
}

<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource\Pages;

use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgebaseCategory extends EditRecord
{
    protected static string $resource = KnowledgebaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading('Delete category and all related articles?')
                ->modalDescription('Are you sure you want to delete this? Deleting the category will also delete all articles associated with it.'),
        ];
    }

    function getRedirectUrl(): ?string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}

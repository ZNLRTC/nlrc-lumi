<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource\Pages;

use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgebaseCategory extends CreateRecord
{
    protected static string $resource = KnowledgebaseCategoryResource::class;

    protected static bool $canCreateAnother = false;

    function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

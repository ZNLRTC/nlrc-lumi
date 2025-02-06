<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\Pages;

use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgebaseArticle extends CreateRecord
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected static bool $canCreateAnother = false;

    function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

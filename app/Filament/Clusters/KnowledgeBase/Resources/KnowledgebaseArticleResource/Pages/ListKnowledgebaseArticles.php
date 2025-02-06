<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\Pages;

use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgebaseArticles extends ListRecords
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

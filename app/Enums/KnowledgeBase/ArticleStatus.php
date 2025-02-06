<?php
namespace App\Enums\KnowledgeBase;

use Filament\Support\Contracts\HasLabel;

enum ArticleStatus: string implements HasLabel
{
    case DRAFT = 'Draft';
    case PUBLISHED = 'Published';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
        };
    }
}
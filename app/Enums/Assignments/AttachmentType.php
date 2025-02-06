<?php
namespace App\Enums\Assignments;

use Filament\Support\Contracts\HasLabel;

enum AttachmentType: string implements HasLabel
{
    case AUDIO = 'audio';
    case IMAGE = 'image';
    case PDF = 'pdf';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUDIO => 'Audio',
            self::IMAGE => 'Image',
            self::PDF => 'PDF',
        };
    }
}
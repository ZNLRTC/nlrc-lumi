<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentTraineesStatus: string implements HasColor, HasLabel
{
    case APPROVED = 'Approved';
    case PENDING_CHECKING = 'Pending checking';
    case RE_UPLOAD_NEEDED = 'Re-upload needed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::PENDING_CHECKING => 'Pending checking',
            self::RE_UPLOAD_NEEDED => 'Re-upload needed'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::APPROVED => 'success',
            self::PENDING_CHECKING => 'warning',
            self::RE_UPLOAD_NEEDED => 'danger'
        };
    }

    public static function textColor(DocumentTraineesStatus $label): ?string
    {
        return match ($label) {
            self::APPROVED => 'text-green-500 dark:text-green-300',
            self::PENDING_CHECKING => 'text-orange-500 dark:text-orange-300',
            self::RE_UPLOAD_NEEDED => 'text-red-500 dark:text-red-300'
        };
    }
}

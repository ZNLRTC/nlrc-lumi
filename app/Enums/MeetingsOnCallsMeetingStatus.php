<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

// TODO: On-going is currently unused.
// To be decided
enum MeetingsOnCallsMeetingStatus: string implements HasColor, HasLabel
{
    case CANCELLED = 'Cancelled';
    case COMPLETED = 'Completed';
    case ONGOING = 'On-going';
    case PENDING = 'Pending';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::ONGOING => 'On-going',
            self::PENDING => 'Pending'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
            self::ONGOING => 'warning',
            self::PENDING => 'info'
        };
    }
}

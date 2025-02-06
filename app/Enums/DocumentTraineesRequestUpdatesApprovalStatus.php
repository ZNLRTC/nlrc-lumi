<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentTraineesRequestUpdatesApprovalStatus: string implements HasColor, HasLabel
{
    case APPROVED = 'Approved';
    case DISAPPROVED = 'Disapproved';
    case PENDING_APPROVAL = 'Pending approval';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::DISAPPROVED => 'Disapproved',
            self::PENDING_APPROVAL => 'Pending approval'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::APPROVED => 'success',
            self::DISAPPROVED => 'danger',
            self::PENDING_APPROVAL => 'warning'
        };
    }
}

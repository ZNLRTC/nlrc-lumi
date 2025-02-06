<?php

namespace App\Enums\Planner;

use Filament\Support\Contracts\HasLabel;

enum ContentType: string implements HasLabel
{
    case DEFAULT = 'default';
    case MEETING_ONLY = 'meeting_only';
    case UNIT_ONLY = 'unit_only';
    case BREAK_WEEK = 'brush-up_week';
    case NONE = 'none';
    case CUSTOM_CONTENT = 'custom_content';

    public function getLabel(): string
    {
        return match ($this) {
            self::DEFAULT => 'Default (unit and meeting)',
            self::MEETING_ONLY => 'Meeting only',
            self::UNIT_ONLY => 'Unit only',
            self::BREAK_WEEK => 'Brush-up week',
            self::NONE => 'None (end of schedule)',
            self::CUSTOM_CONTENT => 'Custom content',
        };
    }
}

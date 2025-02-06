<?php
namespace App\Enums\Assignments;

use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasLabel
{
    case COMPLETED = 'completed';
    case INCOMPLETE = 'incomplete';
    case NOT_CHECKED = 'not checked';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPLETED => 'Completed',
            self::INCOMPLETE => 'Incomplete',
            self::NOT_CHECKED => 'Not checked',
        };
    }
}
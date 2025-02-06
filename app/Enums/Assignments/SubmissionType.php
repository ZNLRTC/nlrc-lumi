<?php
namespace App\Enums\Assignments;

use Filament\Support\Contracts\HasLabel;

enum SubmissionType: string implements HasLabel
{
    case TEXT = 'text';
    case FILE = 'file';
    case NONE = 'none';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::FILE => 'File',
            self::NONE => 'None',
        };
    }
}
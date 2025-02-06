<?php
namespace App\Enums\Exams;

use Filament\Support\Contracts\HasLabel;

enum ExamAttemptStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case ABSENT = 'absent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PASSED => 'Passed',
            self::FAILED => 'Failed',
            self::ABSENT => 'Absent',
        };
    }
}
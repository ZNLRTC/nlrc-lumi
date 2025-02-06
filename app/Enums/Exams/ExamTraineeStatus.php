<?php
namespace App\Enums\Exams;

use Filament\Support\Contracts\HasLabel;

enum ExamTraineeStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case ATTENDING = 'attending';
    case NOT_ATTENDING = 'not attending';
    case ABSENT = 'absent';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ATTENDING => 'Attending',
            self::NOT_ATTENDING => 'Not attending',
            self::ABSENT => 'Absent',
        };
    }
}
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TraineesWorkExperience: int implements HasLabel
{
    case NO_WORK_EXPERIENCE = 0;
    case LESS_THAN_ONE_YEAR = 1;
    case ONE_TO_THREE_YEARS = 2;
    case MORE_THAN_THREE_YEARS = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NO_WORK_EXPERIENCE => 'No work experience',
            self::LESS_THAN_ONE_YEAR => 'Less than 1 year',
            self::ONE_TO_THREE_YEARS => '1 - 3 years',
            self::MORE_THAN_THREE_YEARS => 'More than 3 years'
        };
    }

    public static function formLabel(?TraineesWorkExperience $label): ?string
    {
        return match ($label) {
            NULL => '',
            self::NO_WORK_EXPERIENCE => 'No work experience',
            self::LESS_THAN_ONE_YEAR => 'Less than 1 year',
            self::ONE_TO_THREE_YEARS => '1 - 3 years',
            self::MORE_THAN_THREE_YEARS => 'More than 3 years'
        };
    }

    public static function getEquivalentValueOfEnum(string $label): ?int
    {
        // NOTE: We are calling strtolower to capture instances like "nO wOrk experience" or anything similar
        $work_experiences = [];

        foreach (TraineesWorkExperience::cases() as $work_experience) {
            array_push($work_experiences, strtolower(TraineesWorkExperience::formLabel($work_experience)));
        }

        $index_of_enum_value = array_search(strtolower(trim($label)), $work_experiences);

        if ($index_of_enum_value) {
            return $index_of_enum_value;
        } else {
            return false;
        }
    }
}

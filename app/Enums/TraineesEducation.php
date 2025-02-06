<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TraineesEducation: int implements HasLabel
{
    case CAREGIVER = 0;
    case CHEF = 1;
    case NURSE = 2;
    case REGISTERED_NURSE = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CAREGIVER => 'Caregiver',
            self::CHEF => 'Chef',
            self::NURSE => 'Nurse',
            self::REGISTERED_NURSE => 'Registered Nurse'
        };
    }

    public static function formLabel(?TraineesEducation $label): ?string
    {
        return match ($label) {
            NULL => '',
            self::CAREGIVER => 'Caregiver',
            self::CHEF => 'Chef',
            self::NURSE => 'Nurse',
            self::REGISTERED_NURSE => 'Registered Nurse'
        };
    }

    public static function getEquivalentValueOfEnum(string $label): ?int
    {
        // NOTE: We are calling strtolower to capture instances like "cAReGiVEr" or anything similar
        $educations = [];

        foreach (TraineesEducation::cases() as $education) {
            array_push($educations, strtolower(TraineesEducation::formLabel($education)));
        }

        $index_of_enum_value = array_search(strtolower(trim($label)), $educations);

        if ($index_of_enum_value) {
            return $index_of_enum_value;
        } else {
            return false;
        }
    }
}

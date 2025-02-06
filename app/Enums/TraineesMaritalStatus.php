<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TraineesMaritalStatus: int implements HasLabel
{
    case SINGLE = 0;
    case MARRIED = 1;
    case DIVORCED = 2;
    case WIDOWED = 3;
    case SEPARATED = 4;
    case SEPARATED_BUT_LEGALLY_MARRIED = 5;
    case OTHERS = 6;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::DIVORCED => 'Divorced',
            self::WIDOWED => 'Widowed',
            self::SEPARATED => 'Separated',
            self::SEPARATED_BUT_LEGALLY_MARRIED => 'Separated but Legally Married',
            self::OTHERS => 'Others'
        };
    }

    public static function formLabel(?TraineesMaritalStatus $label): ?string
    {
        return match ($label) {
            NULL => '',
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::DIVORCED => 'Divorced',
            self::WIDOWED => 'Widowed',
            self::SEPARATED => 'Separated',
            self::SEPARATED_BUT_LEGALLY_MARRIED => 'Separated but Legally Married',
            self::OTHERS => 'Others'
        };
    }

    public static function getEquivalentValueOfEnum(string $label): ?int
    {
        // NOTE: We are calling strtolower to capture instances like "sIngLE" or anything similar
        $marital_statuses = [];

        foreach (TraineesMaritalStatus::cases() as $marital_status) {
            array_push($marital_statuses, strtolower(TraineesMaritalStatus::formLabel($marital_status)));
        }

        $index_of_enum_value = array_search(strtolower(trim($label)), $marital_statuses);

        if ($index_of_enum_value) {
            return $index_of_enum_value;
        } else {
            return false;
        }
    }
}

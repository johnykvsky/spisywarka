<?php

namespace App\Traits;

use Spatie\Enum\Enum;

trait FormEnumChoices
{
    /**
     * @param array $enumValues
     * @return array
    */
    private function getFormChoicesFromEnum(array $enumValues): array
    {
        $result = [];

        foreach ($enumValues as $value) {
            $result[ucfirst($value)] = $value;
        }

        return $result;
    }
}

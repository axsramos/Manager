<?php

namespace App\Helpers;

class RequiredFields
{
    public static function run(array $requiredFields, array $dataEntry): bool
    {
        $checkFields = 0;

        foreach ($requiredFields as $field) {
            if (isset($dataEntry[$field])) {
                $checkFields++;
            }
        }

        return ($checkFields == count($requiredFields) ? true : false);
    }
}

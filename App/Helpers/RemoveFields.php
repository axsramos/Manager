<?php

namespace App\Helpers;

class RemoveFields
{
    public static function run(array $removeFields, array $records): array
    {

        $rows = count($records);

        if ($rows) {
            foreach ($removeFields as $field) {
                for ($i = 0; $i < $rows; $i++) {
                    foreach ($records[$i] as $key => $value) {
                        if ($field == $key) {
                            unset($records[$i][$key]);
                        }
                    }
                }
            }
        }

        return $records;
    }
}

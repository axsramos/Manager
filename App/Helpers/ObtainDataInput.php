<?php

namespace App\Helpers;

class ObtainDataInput
{
    public static function run(array $fields = [], bool $api = false): array
    {
        $dataEntry = array();

        if ($api) {
            /**
             * API - Raw JSON
             */
            $dataContent = json_decode(file_get_contents('php://input'));

            foreach ($fields as $field) {
                if (isset($dataContent->$field)) {
                    $dataEntry[$field] = htmlspecialchars($dataContent->$field);
                }
            }
        } else {
            /**
             * WEB - form-data
             */
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $dataEntry[$field] = htmlspecialchars($_POST[$field]);
                }
            }
        }

        return $dataEntry;
    }
}

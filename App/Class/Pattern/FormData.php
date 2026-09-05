<?php

namespace App\Class\Pattern;

class FormData
{
    public static function standard(array $messages = [], array $records = [], array $dataEntry = []): array
    {
        return array(
            'Messages' => $messages,
            'Rows' => count($records),
            'Input' => $dataEntry,
            'Output' => $records,
        );
    }

    public static function secFields(array $fields, $dataEntry = []): array
    {
        $data = array();

        foreach ($fields as $field) {
            $value = '';
            if (isset($dataEntry[$field])) {
                $value = $dataEntry[$field];
            }
            $data[$field] = $value;
        }

        return $data;
    }
}

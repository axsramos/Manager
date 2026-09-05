<?php

namespace App\Class\Pattern;

class FormatData
{
    public static function transformData(string $pattern, string|null $date): string
    {
        $dateReturn = (is_null($date) ? '' : $date);

        /**
         * ToDo: https://www.w3schools.com/php/func_date_create_from_format.asp
         */
        if (!empty($date)) {
            switch ($pattern) {
                case 'OnlyDate':
                    return date('d/m/Y', strtotime($date));
                    break;
                case 'OnlyTime':
                    return date('H:i:s', strtotime($date));
                    break;
                case 'DateTimePt':
                    return date('d/m/Y H:i:s', strtotime($date));
                    break;
                case 'ToDatabase':
                    return date('Y-m-d H:i:s', strtotime($date));
                    break;
                default:
                    $dateReturn = date($pattern, strtotime($date));
                    break;
            }
        }

        return $dateReturn;
    }

    public static function transformSelectionSN(string $data, bool $longDescription = true): string
    {
        $value = substr(strtoupper($data), 0, 1);
        
        if (empty($data)) {
            $value = 'N';
        }
        
        if ($value == 'Y') {
            $value = 'S';
        }

        if ($longDescription) {
            $dataReturn = ($value == 'S' ? 'Sim' : 'Não');
        } else {
            $dataReturn = ($value == 'S' ? 'S' : 'N');
        }

        return $dataReturn;
    }

    public static function transformToMoney(float $value, bool $simbol = true): string
    {
        $prefix = '';

        if ($simbol) {
            $prefix = 'R$';
        }

        return $prefix . number_format($value, 2, ',', '.');
    }

    public static function transformToMoney4DB(string $value, int $decimal = 2): string
    {
        $transform = preg_replace("/[^0-9,]/", "", $value);
        $transform = str_replace(",", ".", $transform);
        $floattransform = (float) $transform;

        $result = number_format($floattransform, $decimal, '.', '');
        
        return $result;
    }
}

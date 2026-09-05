<?php

namespace App\Helpers;

class DataContentInFiles
{
    public static function getDataContent(string $path): string
    {
        $dataContent = '';
        
        if (self::checkFileExists($path)) {
            $dataContent = file_get_contents($path);
        }
        
        return $dataContent;
    }

    public static function setDataContent(string $path, string $dataContent): void
    {
        file_put_contents($path, $dataContent);
    }

    public static function checkFileExists(string $path): bool
    {
        if (file_exists($path)) {
            return true;
        } else {
            return false;
        }
    }

}

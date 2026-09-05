<?php

namespace App\Core;

class RequestToken
{
    public static function run()
    {
        $headers = null;
        $token = '';

        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    $token = $matches[1];
                }
            }
        }

        return $token;
    }
}

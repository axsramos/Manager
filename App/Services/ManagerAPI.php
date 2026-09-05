<?php

namespace App\Services;

use App\Core\Config;

class ManagerAPI
{
    public static function JWTRequestLogin(string $token, string $post): string
    {
        die('-stop-JWTRequestLogin');
        // header('Content-Type: application/json');

        try {
            $ch = curl_init(Config::$API_MANAGER_URL . '/Login');
            $post = json_encode($post);
            $authorization = "Authorization: Bearer " . $token;

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $result = curl_exec($ch);

            curl_close($ch);
        } catch (\Throwable $th) {
            $result = $th->getMessage();
        }

        return json_decode($result);
    }

    public static function JWTRequest(string $api_program, string $method = 'get', array $parms = array(), $post = ''): string
    {
        die('-stop-JWTRequest');
        $parameters = '';

        for ($i = 0; $i < count($parms); $i++) {
            $parameters = $parameters . '/' . $parms[$i];
        }

        try {
            $url = Config::$API_MANAGER_URL . '/' . $api_program . $parameters;
            $ch = curl_init(Config::$API_MANAGER_URL . '/' . $api_program . $parameters);
            $dataContent = $post; // json_encode($post);
            $authorization = "Authorization: Bearer " . Config::$API_MANAGER_TOKEN;

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataContent);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $result = curl_exec($ch);

            curl_close($ch);
        } catch (\Throwable $th) {
            $result = $th->getMessage();
        }

        return json_decode($result);
    }
}

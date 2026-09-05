<?php

namespace App\Core;

class GenerateKey
{
    private const KEY_BYTES = 24;

    public static function generateKey(): string
    {
        $randomBytes = random_bytes(self::KEY_BYTES);

        // Base64 URL-safe (RFC 4648), sem padding: 32 caracteres para 24 bytes.
        return rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
    }

    public static function generateKeyWithRange(string $prefix = '', string $sufix = '', string $range = ''): string
    {
        if (empty($range)) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $characters = $range;
        }

        $charactersLength = strlen($characters);
        $randomString = '';

        $length = 32 - (strlen($prefix) + strlen($sufix));
        if ($length < 0) {
            $length = 0;
        }

        for ($i = 0; $i < $length; $i++) {
            if ($i === 0) {
                $randomString .= $prefix;
            }
            if ($i === $length - (1 + strlen($sufix))) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
                $randomString .= $sufix;
                break;
            }
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

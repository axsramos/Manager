<?php

namespace App\Helpers;

class ObtainBrowserName
{
    public static function getBrowserName(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $browserName = "Desconhecido";

        // Lista de navegadores e suas respectivas assinaturas na string User-Agent
        // A ordem é importante, pois alguns user agents podem conter strings de outros.
        // Por exemplo, Edge muitas vezes inclui "Chrome" e "Safari".
        $browsers = [
            '/msie/i'       => 'Internet Explorer',
            '/trident/i'    => 'Internet Explorer', // IE11
            '/firefox/i'    => 'Firefox',
            '/safari/i'     => 'Safari',
            '/chrome/i'     => 'Chrome',
            '/edge/i'       => 'Edge',          // Edge Legacy (EdgeHTML)
            '/edg/i'        => 'Edge',          // Edge Chromium
            '/opera/i'      => 'Opera',
            '/opr/i'        => 'Opera',         // Opera mais recente
            '/mobile/i'     => 'Mobile Browser' // Genérico para navegadores mobile se outros não baterem
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $browserName = $value;
                // Tratamento especial para Chrome e Safari, pois "Chrome" pode conter "Safari"
                // e Edge Chromium contém "Chrome" e "Edg".
                // Se encontrarmos Edge ou Chrome, paramos para evitar que seja identificado como Safari incorretamente.
                if ($value === 'Edge' || $value === 'Chrome') {
                    break;
                }
                // Se for Safari e também tiver Chrome (comum em user agents do Chrome), priorizamos Chrome.
                // Mas se for Edge, Edge já foi tratado.
                if ($value === 'Safari' && preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
                    // Não faz nada aqui, pois o loop continuará e pegará o Chrome
                    continue;
                }
                // Se for Safari e não tiver Chrome, então é Safari mesmo.
                if ($value === 'Safari' && !preg_match('/chrome/i', $userAgent)) {
                    break;
                }
            }
        }

        // Refinamento para Edge: se for Edge, não deve ser Chrome ou Safari
        if ($browserName === 'Chrome' && (preg_match('/edg/i', $userAgent))) {
            $browserName = 'Edge';
        } elseif ($browserName === 'Safari' && (preg_match('/edg/i', $userAgent) || preg_match('/chrome/i', $userAgent))) {
            // Se foi identificado como Safari mas é Edge ou Chrome, precisa corrigir
            if (preg_match('/edg/i', $userAgent)) {
                $browserName = 'Edge';
            } elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
                $browserName = 'Chrome';
            }
        }


        return $browserName;
    }

    public static function getLongBrowserName(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        return $userAgent;
    }
}

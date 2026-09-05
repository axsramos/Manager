<?php

namespace App\Helpers;

class ObtainClientInfo
{
    /**
     * Obtém o endereço IP do cliente.
     *
     * @return string O endereço IP do cliente ou 'Unknown' se não puder ser determinado.
     */
    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        return $ip;
    }

    /**
     * Tenta obter o nome do host (hostname) do cliente através de DNS reverso.
     * Pode retornar o próprio IP se o hostname não puder ser resolvido.
     *
     * @return string O hostname do cliente, o IP se não resolvido, ou 'Unknown'.
     */
    public static function getClientHostname(): string
    {
        $clientIp = self::getClientIp();
        if ($clientIp !== 'Unknown') {
            // Adicionar um timeout para gethostbyaddr pode ser uma boa prática
            // em produção para evitar longos delays, mas não há um parâmetro direto.
            // A lentidão aqui depende da configuração do servidor DNS e da rede.
            $hostname = gethostbyaddr($clientIp);
            if ($hostname === false || $hostname === $clientIp) {
                return $clientIp; // Retorna o IP se não conseguir resolver ou se for igual ao IP
            }
            return $hostname;
        }
        return 'Unknown';
    }

    /**
     * Obtém o nome de usuário do cliente, se disponível através de REMOTE_USER.
     * Isso geralmente só funciona em ambientes específicos (ex: intranet com autenticação Basic/Digest
     * ou autenticação integrada do Windows no IIS).
     * Para a maioria das aplicações web na internet, isso retornará uma string vazia ou null.
     *
     * @return string O nome de usuário remoto ou uma string vazia.
     */
    public static function getRemoteUser(): string
    {
        return $_SERVER['REMOTE_USER'] ?? '';
    }
}

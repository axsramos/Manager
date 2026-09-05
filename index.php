<?php

if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $requestPath = is_string($requestPath) ? $requestPath : '/';

    $requestedFile = realpath(
        __DIR__ . DIRECTORY_SEPARATOR . ltrim(rawurldecode($requestPath), '/\\')
    );

    if (
        $requestedFile !== false
        && is_file($requestedFile)
        && str_starts_with($requestedFile, realpath(__DIR__) . DIRECTORY_SEPARATOR)
    ) {
        return false;
    }
}

require __DIR__ . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Application;

Config::getInstance();

$app = new Application();
<?php

declare(strict_types=1);

define('ROOT_PATH', __DIR__);
ini_set('display_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Prague');

set_error_handler(function (int $severity, string $message, string $filename, int $lineno): bool {
    if (error_reporting() === 0) {
        return false;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
    return true;
});

require_once __DIR__ . '/../vendor/autoload.php';

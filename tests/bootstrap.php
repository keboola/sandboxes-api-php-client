<?php
define('ROOT_PATH', __DIR__);
ini_set('display_errors', true);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Prague');

set_error_handler('exceptions_error_handler');
function exceptions_error_handler($severity, $message, $filename, $lineno)
{
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

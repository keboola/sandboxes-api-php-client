<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Keboola\ManageApi\Client as ManageClient;
use Keboola\Sandboxes\Api\Exception\ClientException;
use Keboola\StorageApi\Client as StorageClient;

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

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv->load();
}
$dotenv->required(['API_URL', 'KBC_MANAGE_TOKEN', 'KBC_STORAGE_TOKEN', 'KBC_URL']);

$tokenEnvs = [
    'KBC_STORAGE_TOKEN',
];

foreach ($tokenEnvs as $tokenEnv) {
    $storageClient = new StorageClient([
        'url' => (string) getenv('KBC_URL'),
        'token' => (string) getenv($tokenEnv),
    ]);

    try {
        /** @var array{
         *    'description': string,
         *    'id': string,
         *    'owner': array{
         *      'name': string,
         *      'id': string
         *    }
         * } $tokenInfo
         */
        $tokenInfo = $storageClient->verifyToken();
    } catch (ClientException $e) {
        throw new RuntimeException(sprintf(
            'Failed to verify "%s", check ENV variables: %s',
            $tokenEnv,
            $e->getMessage(),
        ), 0, $e);
    }

    printf(
        'Authorized as "%s (%s)" to project "%s (%s)" at "%s" stack.' . "\n",
        $tokenInfo['description'],
        $tokenInfo['id'],
        $tokenInfo['owner']['name'],
        $tokenInfo['owner']['id'],
        $storageClient->getApiUrl(),
    );
}

$tokenEnvs = [
    'KBC_MANAGE_TOKEN',
];

foreach ($tokenEnvs as $tokenEnv) {
    $manageClient = new ManageClient([
        'url' => (string) getenv('KBC_URL'),
        'token' => (string) getenv($tokenEnv),
    ]);

    try {
        /** @var array{
         *    'description': string,
         *    'id': string,
         *    'creator': array{
         *      'name': string,
         *      'id': string
         *    }
         * } $tokenInfo
         */
        $tokenInfo = $manageClient->verifyToken();
    } catch (Throwable $e) {
        throw new RuntimeException(sprintf(
            'Failed to verify "%s", check ENV variables: %s',
            $tokenEnv,
            $e->getMessage(),
        ), 0, $e);
    }

    printf(
        'Authorized as "%s (%s)" to project "%s (%s)" at "%s" stack.' . "\n",
        $tokenInfo['description'],
        $tokenInfo['id'],
        $tokenInfo['creator']['name'],
        $tokenInfo['creator']['id'],
        $storageClient->getApiUrl(),
    );
}

require_once __DIR__ . '/../vendor/autoload.php';

<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class ManageClient extends AbstractClient
{
    public function __construct(
        string $apiUrl,
        string $token,
        ?array $options = []
    ) {
        $options['headers'] = ['X-KBC-ManageApiToken' => $token];
        parent::__construct($apiUrl, $options);
    }

    public function listExpired(): array
    {
        return array_map(function ($s) {
            return new Sandbox($s);
        }, $this->sendRequest(new Request('GET', 'manage/list/expired')));
    }

    public function get(string $id): Sandbox
    {
        return new Sandbox(
            $this->sendRequest(new Request('GET', "manage/{$id}"))
        );
    }

    public function deactivate(string $id): void
    {
        try {
            $this->sendRequest(new Request('DELETE', "manage/{$id}"));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error deactivating sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }
}

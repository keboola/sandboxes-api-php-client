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

    public function delete(string $projectId, string $sandboxId): void
    {
        try {
            $this->sendRequest(new Request('DELETE', "manage/{$projectId}/{$sandboxId}"));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error deleting sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }
}

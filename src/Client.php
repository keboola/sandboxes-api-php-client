<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class Client extends AbstractClient
{
    public function __construct(
        string $apiUrl,
        string $storageToken,
        ?array $options = []
    ) {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }
        $options['headers']['X-StorageApi-Token'] = $storageToken;
        parent::__construct($apiUrl, $options);
    }

    public function create(Sandbox $sandbox): Sandbox
    {
        $jobData = \GuzzleHttp\json_encode($sandbox->toApiRequest());
        $request = new Request('POST', 'sandboxes', [], $jobData);
        try {
            return Sandbox::fromArray($this->sendRequest($request));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error creating sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }

    public function update(Sandbox $sandbox): Sandbox
    {
        $jobData = \GuzzleHttp\json_encode($sandbox->toApiRequest());
        $request = new Request('PUT', "sandboxes/{$sandbox->getId()}", [], $jobData);
        try {
            return Sandbox::fromArray($this->sendRequest($request));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error updating sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }

    public function deactivate(string $id): Sandbox
    {
        try {
            return Sandbox::fromArray($this->sendRequest(new Request('POST', "sandboxes/{$id}/deactivate", [], '{}')));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error deactivating sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }

    public function activate(string $id): Sandbox
    {
        try {
            return Sandbox::fromArray($this->sendRequest(new Request('POST', "sandboxes/{$id}/activate", [], '{}')));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error activating sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }

    public function delete(string $id): Sandbox
    {
        try {
            return Sandbox::fromArray($this->sendRequest(new Request('DELETE', "sandboxes/{$id}")));
        } catch (GuzzleException $guzzleException) {
            throw new Exception('Error deleting sandbox', $guzzleException->getCode(), $guzzleException);
        }
    }

    public function get(string $id): Sandbox
    {
        return Sandbox::fromArray(
            $this->sendRequest(new Request('GET', "sandboxes/{$id}"))
        );
    }

    // @TODO pagination
    public function list(): array
    {
        return array_map(function ($s) {
            return Sandbox::fromArray($s);
        }, $this->sendRequest(new Request('GET', 'sandboxes')));
    }

    public function getProject(): Project
    {
        return Project::fromArray(
            $this->sendRequest(new Request('GET', 'projects/current'))
        );
    }
}

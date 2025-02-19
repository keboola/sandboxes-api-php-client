<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\json_encode;

class Client extends AbstractClient
{
    public function __construct(
        string $apiUrl,
        string $storageToken,
        ?array $options = [],
    ) {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }
        $options['headers']['X-StorageApi-Token'] = $storageToken;
        parent::__construct($apiUrl, $options);
    }

    public function create(Sandbox $sandbox): Sandbox
    {
        $jobData = json_encode($sandbox->toApiRequest());
        $request = new Request('POST', 'sandboxes', [], $jobData);
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function update(Sandbox $sandbox): Sandbox
    {
        $jobData = json_encode($sandbox->toApiRequest());
        $request = new Request('PATCH', "sandboxes/{$sandbox->getId()}", [], $jobData);
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function deactivate(string $id, bool $skipBillingReport = false): Sandbox
    {
        $query = [];

        if ($skipBillingReport) {
            $query['skipBillingReport'] = 'true';
        }

        $request = new Request(
            'POST',
            sprintf('sandboxes/%s/deactivate?%s', $id, http_build_query($query)),
            [],
            '{}',
        );
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function activate(string $id): Sandbox
    {
        $request = new Request('POST', "sandboxes/{$id}/activate", [], '{}');
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function delete(string $id, bool $skipBillingReport = false): Sandbox
    {
        $query = [];

        if ($skipBillingReport) {
            $query['skipBillingReport'] = 'true';
        }

        $request = new Request(
            'DELETE',
            sprintf('sandboxes/%s?%s', $id, http_build_query($query)),
        );

        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function get(string $id): Sandbox
    {
        $request = new Request('GET', "sandboxes/{$id}");
        return Sandbox::fromArray($this->sendRequest($request));
    }

    // @TODO pagination
    public function list(?ListOptions $options = null): array
    {
        if ($options === null) {
            $options = ListOptions::create();
        }

        $url = 'sandboxes?' . http_build_query($options->export());

        return array_map(function ($s) {
            return Sandbox::fromArray($s);
        }, $this->sendRequest(new Request('GET', $url)));
    }

    public function getProject(): Project
    {
        return Project::fromArray(
            $this->sendRequest(new Request('GET', 'projects/current')),
        );
    }

    public function getPersistentStorage(): PersistentStorage
    {
        $result = $this->sendRequest(new Request('GET', 'projects/current/persistentStorage'));

        return PersistentStorage::fromArray($result);
    }
}

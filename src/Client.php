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
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function update(Sandbox $sandbox): Sandbox
    {
        $jobData = \GuzzleHttp\json_encode($sandbox->toApiRequest());
        $request = new Request('PUT', "sandboxes/{$sandbox->getId()}", [], $jobData);
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function deactivate(string $id): Sandbox
    {
        $request = new Request('POST', "sandboxes/{$id}/deactivate", [], '{}');
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function activate(string $id): Sandbox
    {
        $request = new Request('POST', "sandboxes/{$id}/activate", [], '{}');
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function delete(string $id): Sandbox
    {
        $request = new Request('DELETE', "sandboxes/{$id}");
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function get(string $id): Sandbox
    {
        $request = new Request('GET', "sandboxes/{$id}");
        return Sandbox::fromArray($this->sendRequest($request));
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

    public function createMLflowDeployment(MLflowDeployment $deployment): MLflowDeployment
    {
        $jobData = \GuzzleHttp\json_encode($deployment->toApiRequest());
        $request = new Request('POST', 'mlflow/deployments', [], $jobData);
        return MLflowDeployment::fromArray($this->sendRequest($request));
    }

    public function updateMLflowDeployment(MLflowDeployment $deployment): MLflowDeployment
    {
        $jobData = \GuzzleHttp\json_encode($deployment->toApiRequest());
        $request = new Request('PUT', "mlflow/deployments/{$deployment->getId()}", [], $jobData);
        return MLflowDeployment::fromArray($this->sendRequest($request));
    }

    public function getMLflowDeployment(string $id): MLflowDeployment
    {
        return MLflowDeployment::fromArray(
            $this->sendRequest(new Request('GET', "mlflow/deployments/{$id}"))
        );
    }

    public function deleteMLflowDeployment(string $id): void
    {
        $this->sendRequest(new Request('DELETE', "mlflow/deployments/{$id}"));
    }
}

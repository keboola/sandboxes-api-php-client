<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use GuzzleHttp\Psr7\Request;

class ManageClient extends AbstractClient
{
    public function __construct(
        string $apiUrl,
        string $token,
        ?array $options = []
    ) {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }
        $options['headers']['X-KBC-ManageApiToken'] = $token;
        parent::__construct($apiUrl, $options);
    }

    public function listExpired(): array
    {
        return array_map(function ($s) {
            return Sandbox::fromArray($s);
        }, $this->sendRequest(new Request('GET', 'manage/list/expired')));
    }

    public function get(string $id): Sandbox
    {
        return Sandbox::fromArray(
            $this->sendRequest(new Request('GET', "manage/{$id}"))
        );
    }

    public function deactivate(string $id): void
    {
        $this->sendRequest(new Request('DELETE', "manage/{$id}"));
    }

    public function getProject(string $id): Project
    {
        return Project::fromArray(
            $this->sendRequest(new Request('GET', "manage/projects/{$id}"))
        );
    }

    public function updateProject(Project $project): Project
    {
        $jobData = \GuzzleHttp\json_encode($project->toApiRequest());
        $request = new Request('PATCH', "manage/projects/{$project->getId()}", [], $jobData);
        return Project::fromArray($this->sendRequest($request));
    }
}

<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\json_encode;

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

    /**
     * @return Sandbox[]
     *
     * @TODO pagination
     */
    public function listProjectSandboxes(string $projectId): array
    {
        $response = $this->sendRequest(new Request(
            'GET',
            sprintf('manage/projects/%s/sandboxes', urlencode($projectId))
        ));

        return array_map(fn (array $s) => Sandbox::fromArray($s), $response);
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

    public function updateSandbox(Sandbox $sandbox): Sandbox
    {
        $body = json_encode($sandbox->toApiRequest());
        $request = new Request('PATCH', "manage/{$sandbox->getId()}", [], $body);
        return Sandbox::fromArray($this->sendRequest($request));
    }

    public function deactivate(string $id): void
    {
        $this->sendRequest(new Request('POST', "manage/{$id}/deactivate", [], '{}'));
    }

    /**
     * @return iterable<Project>
     */
    public function listProjects(?ListProjectsOptions $options): iterable
    {
        $query = $options ? $options->toQueryParameters() : [];
        $nextPageToken = '';

        do {
            $query['nextPageToken'] = $nextPageToken;
            $response = $this->sendRequest(new Request('GET', '/manage/projects?'.http_build_query($query)));

            foreach ($response['data'] ?? [] as $projectData) {
                yield Project::fromArray($projectData);
            }

            $nextPageToken = $response['nextPageToken'] ?? '';
        } while (!empty($nextPageToken));
    }

    public function getProject(string $id): Project
    {
        return Project::fromArray(
            $this->sendRequest(new Request('GET', "manage/projects/{$id}"))
        );
    }

    public function updateProject(Project $project): Project
    {
        $body = json_encode((object) $project->toApiRequest());
        $request = new Request('PATCH', "manage/projects/{$project->getId()}", [], $body);
        return Project::fromArray($this->sendRequest($request));
    }

    public function notifyBranchDeleted(string $branchId): void
    {
        $this->sendRequest(
            new Request(
                'POST',
                'notify/branch-deleted',
                [],
                json_encode(['branchId' => $branchId], JSON_THROW_ON_ERROR)
            )
        );
    }
}

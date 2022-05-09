<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Keboola\Sandboxes\Api\ListProjectsOptions;
use Keboola\Sandboxes\Api\ManageClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ManageClientTest extends TestCase
{
    public function testListProjectSandboxes(): void
    {
        $guzzleHandler = HandlerStack::create(new MockHandler([
            function (RequestInterface $request): ResponseInterface {
                self::assertSame('GET', $request->getMethod());
                self::assertSame('manage-token', $request->getHeaderLine('X-KBC-ManageApiToken'));
                self::assertSame('/manage/projects/project1/sandboxes', $request->getUri()->getPath());

                return new Response(200, ['X-Foo' => 'Bar'], (string) json_encode([
                    [
                        'id' => '111',
                        'projectId' => 'project1',
                        'tokenId' => 'token-id',
                        'type' => 'python',
                        'active' => true,
                        'createdTimestamp' => '2021-12-12T12:00:00Z',
                    ],
                    [
                        'id' => '222',
                        'projectId' => 'project1',
                        'tokenId' => 'token-id',
                        'type' => 'python',
                        'active' => true,
                        'createdTimestamp' => '2021-12-12T12:00:00Z',
                    ],
                ], JSON_THROW_ON_ERROR));
            },
        ]));

        $manageClient = new ManageClient(
            (string) getenv('API_URL'),
            'manage-token',
            ['handler' => $guzzleHandler]
        );

        $sandboxes = $manageClient->listProjectSandboxes('project1');
        self::assertCount(2, $sandboxes);
        self::assertSame('111', $sandboxes[0]->getId());
        self::assertSame('222', $sandboxes[1]->getId());
    }

    public function testListProjects(): void
    {
        $guzzleHandler = HandlerStack::create(new MockHandler([
            function (RequestInterface $request): ResponseInterface {
                self::assertSame('GET', $request->getMethod());
                self::assertSame('manage-token', $request->getHeaderLine('X-KBC-ManageApiToken'));
                self::assertSame('/manage/projects', $request->getUri()->getPath());
                self::assertSame('hasPersistentStorage=1&nextPageToken=', $request->getUri()->getQuery());

                return new Response(200, [], (string) json_encode([
                    'data' => [
                        [
                            'id' => '111',
                            'projectId' => 'project1',
                            'tokenId' => 'token-id',
                            'type' => 'python',
                            'active' => true,
                            'persistentStorage' => [
                                'ready' => true,
                            ],
                            'createdTimestamp' => '2021-12-12T12:00:00Z',
                        ],
                        [
                            'id' => '222',
                            'projectId' => 'project1',
                            'tokenId' => 'token-id',
                            'type' => 'python',
                            'active' => true,
                            'persistentStorage' => [
                                'ready' => true,
                            ],
                            'createdTimestamp' => '2021-12-12T12:00:00Z',
                        ],
                    ],
                    'nextPageToken' => 'next-page-1234',
                ], JSON_THROW_ON_ERROR));
            },

            function (RequestInterface $request): ResponseInterface {
                self::assertSame('GET', $request->getMethod());
                self::assertSame('manage-token', $request->getHeaderLine('X-KBC-ManageApiToken'));
                self::assertSame('/manage/projects', $request->getUri()->getPath());
                self::assertSame('hasPersistentStorage=1&nextPageToken=next-page-1234', $request->getUri()->getQuery());

                return new Response(200, [], (string) json_encode([
                    'data' => [
                        [
                            'id' => '333',
                            'projectId' => 'project3',
                            'tokenId' => 'token-id',
                            'type' => 'python',
                            'active' => true,
                            'persistentStorage' => [
                                'ready' => true,
                            ],
                            'createdTimestamp' => '2021-12-12T12:00:00Z',
                        ],
                    ],
                    'nextPageToken' => null,
                ], JSON_THROW_ON_ERROR));
            },
        ]));

        $manageClient = new ManageClient(
            (string) getenv('API_URL'),
            'manage-token',
            ['handler' => $guzzleHandler]
        );

        $projects = $manageClient->listProjects(ListProjectsOptions::create()->setHasPersistentStorage(true));
        $projects = [...$projects];
        self::assertCount(3, $projects);
        self::assertSame('111', $projects[0]->getId());
        self::assertSame('222', $projects[1]->getId());
        self::assertSame('333', $projects[2]->getId());
    }
}

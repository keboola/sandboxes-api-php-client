<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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
                ]));
            },
        ]));

        $manageClient = new ManageClient(
            (string) getenv('API_URL'),
            (string) getenv('KBC_MANAGE_TOKEN'),
            ['handler' => $guzzleHandler]
        );

        $sandboxes = $manageClient->listProjectSandboxes('project1');
        self::assertCount(2, $sandboxes);
        self::assertSame('111', $sandboxes[0]->getId());
        self::assertSame('222', $sandboxes[1]->getId());
    }
}

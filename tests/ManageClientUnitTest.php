<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Keboola\Sandboxes\Api\ManageClient;
use Keboola\Sandboxes\Api\Project;
use Keboola\Sandboxes\Api\Sandbox;
use PHPUnit\Framework\TestCase;

class ManageClientUnitTest extends TestCase
{
    private function makeSandboxJson(string $id): string
    {
        return (string) json_encode([
            'id' => $id,
            'projectId' => 'proj1',
            'tokenId' => 'tok1',
            'type' => 'python',
            'active' => true,
            'createdTimestamp' => '2024-01-01 00:00:00',
        ]);
    }

    public function testListProjectSandboxesSinglePage(): void
    {
        $requestsLog = [];

        $body = '[' . $this->makeSandboxJson('sandbox-1') . ',' . $this->makeSandboxJson('sandbox-2') . ']';
        $mockedResponses = [
            new Response(200, [], $body),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new ManageClient('http://sandboxes-api', 'token', [
            'handler' => $handlerStack,
        ]);

        $sandboxes = $client->listProjectSandboxes('my-project');

        self::assertCount(2, $sandboxes);
        self::assertContainsOnlyInstancesOf(Sandbox::class, $sandboxes);
        self::assertSame('sandbox-1', $sandboxes[0]->getId());
        self::assertSame('sandbox-2', $sandboxes[1]->getId());

        self::assertCount(1, $requestsLog);
        $request = $requestsLog[0]['request'];
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/manage/projects/my-project/sandboxes', $request->getUri()->getPath());
        self::assertSame('', $request->getUri()->getQuery());
    }

    public function testListProjectSandboxesPagination(): void
    {
        $requestsLog = [];

        $mockedResponses = [
            new Response(200, [
                'Link' => 'http://wrong-host/manage/list?nextPageToken=token-abc',
            ], '[' . $this->makeSandboxJson('sandbox-1') . ']'),
            new Response(200, [], '[' . $this->makeSandboxJson('sandbox-2') . ']'),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new ManageClient('http://sandboxes-api', 'token', [
            'handler' => $handlerStack,
        ]);

        $sandboxes = $client->listProjectSandboxes('my-project');

        self::assertCount(2, $sandboxes);
        self::assertSame('sandbox-1', $sandboxes[0]->getId());
        self::assertSame('sandbox-2', $sandboxes[1]->getId());

        self::assertCount(2, $requestsLog);

        $firstRequest = $requestsLog[0]['request'];
        self::assertSame('/manage/projects/my-project/sandboxes', $firstRequest->getUri()->getPath());
        self::assertSame('', $firstRequest->getUri()->getQuery());

        $secondRequest = $requestsLog[1]['request'];
        self::assertSame('/manage/projects/my-project/sandboxes', $secondRequest->getUri()->getPath());
        self::assertSame('nextPageToken=token-abc', $secondRequest->getUri()->getQuery());
    }

    public function testUpdateEmptyProject(): void
    {
        $requestsLog = [];

        $mockedResponses = [
            new Response(200, [], '{"id":"foo", "createdTimestamp":"2020-06-01 12:00:00"}'),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new ManageClient('http://sandboxes-api', 'token', [
            'handler' => $handlerStack,
        ]);

        $project = (new Project())->setId('foo');

        $client->updateProject($project);

        self::assertCount(1, $requestsLog);

        $request = $requestsLog[0]['request'];
        self::assertInstanceOf(Request::class, $request);

        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('http://sandboxes-api/manage/projects/foo', Uri::composeComponents(
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $request->getUri()->getPath(),
            $request->getUri()->getQuery(),
            $request->getUri()->getFragment(),
        ));
        self::assertSame('{}', (string) $request->getBody());
    }

    public static function provideDeactivateTestData(): iterable
    {
        yield 'simple call' => [
            'sandboxId' => '123',
            'skipBillingReport' => false,
            'expectedPath' => '/manage/123/deactivate',
        ];

        yield 'with skipBillingReport' => [
            'sandboxId' => '123',
            'skipBillingReport' => true,
            'expectedPath' => '/manage/123/deactivate?skipBillingReport=true',
        ];
    }

    /** @dataProvider provideDeactivateTestData */
    public function testDeactivate(string $sandboxId, bool $skipBillingReport, string $expectedPath): void
    {
        $requestsLog = [];

        $mockedResponses = [
            new Response(200),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new ManageClient('http://sandboxes-api', 'token', [
            'handler' => $handlerStack,
        ]);

        $client->deactivate($sandboxId, $skipBillingReport);

        self::assertCount(1, $requestsLog);

        $request = $requestsLog[0]['request'];
        self::assertInstanceOf(Request::class, $request);

        self::assertSame('POST', $request->getMethod());
        self::assertSame($expectedPath, Uri::composeComponents(
            '',
            '',
            $request->getUri()->getPath(),
            $request->getUri()->getQuery(),
            '',
        ));
        self::assertSame('{}', (string) $request->getBody());
    }

    public static function provideDeleteTestData(): iterable
    {
        yield 'simple call' => [
            'sandboxId' => '123',
            'skipBillingReport' => false,
            'expectedPath' => '/manage/123',
        ];

        yield 'with skipBillingReport' => [
            'sandboxId' => '123',
            'skipBillingReport' => true,
            'expectedPath' => '/manage/123?skipBillingReport=true',
        ];
    }

    /** @dataProvider provideDeleteTestData */
    public function testDelete(string $sandboxId, bool $skipBillingReport, string $expectedPath): void
    {
        $requestsLog = [];

        $mockedResponses = [
            new Response(200),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new ManageClient('http://sandboxes-api', 'token', [
            'handler' => $handlerStack,
        ]);

        $client->deleteSandbox($sandboxId, $skipBillingReport);

        self::assertCount(1, $requestsLog);

        $request = $requestsLog[0]['request'];
        self::assertInstanceOf(Request::class, $request);

        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($expectedPath, Uri::composeComponents(
            '',
            '',
            $request->getUri()->getPath(),
            $request->getUri()->getQuery(),
            '',
        ));
        self::assertSame('', (string) $request->getBody());
    }
}

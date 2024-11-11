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
use PHPUnit\Framework\TestCase;

class ManageClientUnitTest extends TestCase
{
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

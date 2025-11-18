<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Keboola\Sandboxes\Api\Client;
use Keboola\Sandboxes\Api\ListOptions;
use Keboola\Sandboxes\Api\Sandbox;
use PHPUnit\Framework\TestCase;

class ClientUnitTest extends TestCase
{
    public static function provideListOptionsTestData(): iterable
    {
        yield 'no options' => [
            null,
            '/sandboxes',
        ];

        yield 'empty options' => [
            ListOptions::create(),
            '/sandboxes',
        ];

        yield 'branchId filter' => [
            ListOptions::create()->setBranchId('1234'),
            '/sandboxes?branchId=1234',
        ];
    }

    /** @dataProvider provideListOptionsTestData */
    public function testList(?ListOptions $options, string $expectedPath): void
    {
        $requestsLog = [];

        $mockedResponses = [
            new Response(200, [], '[]'),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new Client((string) getenv('API_URL'), (string) getenv('KBC_STORAGE_TOKEN'), [
            'handler' => $handlerStack,
        ]);

        $client->list($options);

        self::assertCount(1, $requestsLog);

        $request = $requestsLog[0]['request'];
        self::assertInstanceOf(Request::class, $request);

        self::assertSame($expectedPath, Uri::composeComponents(
            '',
            '',
            $request->getUri()->getPath(),
            $request->getUri()->getQuery(),
            '',
        ));
    }

    public static function provideDeactivateTestData(): iterable
    {
        yield 'simple call' => [
            'sandboxId' => '123',
            'skipBillingReport' => false,
            'expectedPath' => '/sandboxes/123/deactivate',
        ];

        yield 'with skipBillingReport' => [
            'sandboxId' => '123',
            'skipBillingReport' => true,
            'expectedPath' => '/sandboxes/123/deactivate?skipBillingReport=true',
        ];
    }

    /** @dataProvider provideDeactivateTestData */
    public function testDeactivate(string $sandboxId, bool $skipBillingReport, string $expectedPath): void
    {
        $requestsLog = [];
        $responseData = [
            'id' => $sandboxId,
            'projectId' => 'project-id',
            'tokenId' => 'token-id',
            'type' => 'python',
            'active' => false,
            'createdTimestamp' => '2021-01-01T00:00:00+00:00',
        ];

        $mockedResponses = [
            new Response(200, [], (string) json_encode($responseData)),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new Client((string) getenv('API_URL'), (string) getenv('KBC_STORAGE_TOKEN'), [
            'handler' => $handlerStack,
        ]);

        $result = $client->deactivate($sandboxId, $skipBillingReport);

        self::assertEquals(Sandbox::fromArray($responseData), $result);

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
            'expectedPath' => '/sandboxes/123',
        ];

        yield 'with skipBillingReport' => [
            'sandboxId' => '123',
            'skipBillingReport' => true,
            'expectedPath' => '/sandboxes/123?skipBillingReport=true',
        ];
    }

    /** @dataProvider provideDeleteTestData */
    public function testDelete(string $sandboxId, bool $skipBillingReport, string $expectedPath): void
    {
        $requestsLog = [];
        $responseData = [
            'id' => $sandboxId,
            'projectId' => 'project-id',
            'tokenId' => 'token-id',
            'type' => 'python',
            'active' => false,
            'createdTimestamp' => '2021-01-01T00:00:00+00:00',
        ];

        $mockedResponses = [
            new Response(200, [], (string) json_encode($responseData)),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new Client((string) getenv('API_URL'), (string) getenv('KBC_STORAGE_TOKEN'), [
            'handler' => $handlerStack,
        ]);

        $result = $client->delete($sandboxId, $skipBillingReport);

        self::assertEquals(Sandbox::fromArray($responseData), $result);

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

    public function testUpdateAutosaveTimestamp(): void
    {
        $requestsLog = [];
        $sandboxId = '123';
        $timestamp = '2025-01-18T10:30:00+00:00';
        $responseData = [
            'id' => $sandboxId,
            'projectId' => 'project-id',
            'tokenId' => 'token-id',
            'type' => 'python',
            'active' => true,
            'createdTimestamp' => '2021-01-01T00:00:00+00:00',
            'lastAutosaveTimestamp' => $timestamp,
        ];

        $mockedResponses = [
            new Response(200, [], (string) json_encode($responseData)),
        ];

        $handlerStack = HandlerStack::create(new MockHandler($mockedResponses));
        $handlerStack->push(Middleware::history($requestsLog));

        $client = new Client((string) getenv('API_URL'), (string) getenv('KBC_STORAGE_TOKEN'), [
            'handler' => $handlerStack,
        ]);

        $result = $client->updateAutosaveTimestamp($sandboxId, $timestamp);

        self::assertEquals(Sandbox::fromArray($responseData), $result);
        self::assertEquals($timestamp, $result->getLastAutosaveTimestamp());

        self::assertCount(1, $requestsLog);

        $request = $requestsLog[0]['request'];
        self::assertInstanceOf(Request::class, $request);

        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/sandboxes/123', Uri::composeComponents(
            '',
            '',
            $request->getUri()->getPath(),
            $request->getUri()->getQuery(),
            '',
        ));

        $requestBody = json_decode((string) $request->getBody(), true);
        self::assertIsArray($requestBody);
        self::assertArrayHasKey('lastAutosaveTimestamp', $requestBody);
        self::assertSame($timestamp, $requestBody['lastAutosaveTimestamp']);
    }
}

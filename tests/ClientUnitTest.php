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
use PHPUnit\Framework\TestCase;

class ClientUnitTest extends TestCase
{
    /**
     * @dataProvider provideListOptionsTestData
     */
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
            ''
        ));
    }

    public function provideListOptionsTestData(): iterable
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
}

<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Keboola\Sandboxes\Api\ListProjectsOptions;
use PHPUnit\Framework\TestCase;

class ListProjectsOptionsTest extends TestCase
{
    /**
     * @dataProvider provideToQueryParametersTestData
     */
    public function testToQueryParameters(ListProjectsOptions $options, array $expectedResult): void
    {
        self::assertSame($expectedResult, $options->toQueryParameters());
    }

    public function provideToQueryParametersTestData(): iterable
    {
        yield 'empty' => [
            ListProjectsOptions::create(),
            [],
        ];

        yield 'hasPersistentStorage: true' => [
            ListProjectsOptions::create()->setHasPersistentStorage(true),
            ['hasPersistentStorage' => true],
        ];

        yield 'hasPersistentStorage: false' => [
            ListProjectsOptions::create()->setHasPersistentStorage(false),
            ['hasPersistentStorage' => false],
        ];
    }

    public function testHasPersistentStorage(): void
    {
        $options = ListProjectsOptions::create();
        self::assertNull($options->getHasPersistentStorage());

        $options->setHasPersistentStorage(true);
        self::assertTrue($options->getHasPersistentStorage());

        $options->setHasPersistentStorage(false);
        self::assertFalse($options->getHasPersistentStorage());

        $options->clearHasPersistentStorage();
        self::assertNull($options->getHasPersistentStorage());
    }
}

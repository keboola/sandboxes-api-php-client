<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Keboola\Sandboxes\Api\ListOptions;
use PHPUnit\Framework\TestCase;

class ListOptionsTest extends TestCase
{
    /**
     * @dataProvider provideExportTestData
     */
    public function testExport(ListOptions $options, array $expectedExportData): void
    {
        self::assertSame($expectedExportData, $options->export());
    }

    public function provideExportTestData(): iterable
    {
        yield 'empty' => [
            ListOptions::create(),
            [],
        ];

        yield 'with branchId' => [
            ListOptions::create()->setBranchId('1234'),
            [
                'branchId' => '1234',
            ],
        ];
    }
}

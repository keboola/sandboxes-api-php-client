<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Generator;
use Keboola\Sandboxes\Api\PersistentStorage;
use PHPUnit\Framework\TestCase;

class PersistentStorageTest extends TestCase
{
    /** @dataProvider fromArrayProvider */
    public function testFromArray(array $data, ?bool $expectedValue): void
    {
        $persistentStorage = PersistentStorage::fromArray($data);
        self::assertSame($persistentStorage->isReady(), $expectedValue);
    }

    /** @dataProvider toArrayProvider */
    public function testToArray(PersistentStorage $persistentStorage, array $expectedValue): void
    {
        self::assertSame($persistentStorage->toArray(), $expectedValue);
    }

    public function testSetIsReady(): void
    {
        $persistentStorage = new PersistentStorage();

        $persistentStorage->setReady(true);
        self::assertTrue($persistentStorage->isReady());

        $persistentStorage->setReady(false);
        self::assertFalse($persistentStorage->isReady());

        $persistentStorage->setReady(null);
        self::assertNull($persistentStorage->isReady());
    }

    public function fromArrayProvider(): Generator
    {
        yield 'readyTrue' => [
            ['ready' => true],
            true,
        ];
        yield 'readyFalse' => [
            ['ready' => false],
            false,
        ];
        yield 'readyNull' => [
            ['ready' => null],
            null,
        ];
    }

    public function toArrayProvider(): Generator
    {
        yield 'readyTrue' => [
            (new PersistentStorage())->setReady(true),
            ['ready' => true],
        ];
        yield 'readyFalse' => [
            (new PersistentStorage())->setReady(false),
            ['ready' => false],
        ];
        yield 'readyNull' => [
            (new PersistentStorage())->setReady(null),
            ['ready' => null],
        ];
    }
}

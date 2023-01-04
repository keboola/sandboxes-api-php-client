<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use DateTime;
use Keboola\Sandboxes\Api\Sandbox;
use PHPUnit\Framework\TestCase;

class SandboxTest extends TestCase
{
    public function testPasswordNullable(): void
    {
        $sandbox = new Sandbox();
        $nullPassword = $sandbox->getPassword();
        self::assertNull($nullPassword);

        $sandbox = Sandbox::fromArray([
            'id' => 1,
            'projectId' => '123',
            'tokenId' => '3453',
            'type' => 'python',
            'active' => true,
            'createdTimestamp' => (new DateTime())->format('c'),
        ]);
        $nullPassword = $sandbox->getPassword();
        self::assertEmpty($nullPassword);
    }
}

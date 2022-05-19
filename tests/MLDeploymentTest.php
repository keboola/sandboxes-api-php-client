<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Generator;
use Keboola\Sandboxes\Api\Exception\InvalidArgumentException;
use Keboola\Sandboxes\Api\MLDeployment;
use Keboola\Sandboxes\Api\PersistentStorage;
use PHPUnit\Framework\TestCase;

class MLDeploymentTest extends TestCase
{
    /** @dataProvider fromArrayProvider */
    public function testFromArray(array $data, MLDeployment $expectedValue): void
    {
        $mlDeployment = MLDeployment::fromArray($data);
        self::assertEquals($expectedValue, $mlDeployment);
    }

    public function fromArrayProvider(): Generator
    {
        yield 'minimum' => [
            [
                'id' => '123',
                'projectId' => '123',
                'tokenId' => '123',
            ],
            (new MLDeployment())
                ->setId('123')
                ->setProjectId('123')
                ->setTokenId('123')
                ->setModelName('')
                ->setModelVersion('')
                ->setModelStage('')
                ->setUrl('')
                ->setError('')
                ->setCreatedTimestamp('')
                ->setUpdatedTimestamp(''),
        ];

        yield 'fullData' => [
            [
                'id' => '123',
                'projectId' => '123',
                'tokenId' => '123',
                'modelName' => 'modelName',
                'modelVersion' => 'modelVersion',
                'modelStage' => 'production',
                'trackingTokenId' => '12345',
                'url' => 'url',
                'error' => 'error',
                'createdTimestamp' => '20222-05-27T12:12:12',
                'updatedTimestamp' => '20222-05-27T12:12:12',
            ],
            (new MLDeployment())
                ->setId('123')
                ->setProjectId('123')
                ->setTokenId('123')
                ->setModelName('modelName')
                ->setModelVersion('modelVersion')
                ->setModelStage('production')
                ->setTrackingTokenId('12345')
                ->setUrl('url')
                ->setError('error')
                ->setCreatedTimestamp('20222-05-27T12:12:12')
                ->setUpdatedTimestamp('20222-05-27T12:12:12'),
        ];
    }

    /** @dataProvider toApiRequestProvider */
    public function testToApiRequest(MLDeployment $mlDeployment, array $expectedValue): void
    {
        self::assertSame($expectedValue, $mlDeployment->toApiRequest());
    }

    public function toApiRequestProvider(): Generator
    {
        yield 'empty' => [
            (new MLDeployment())
                ->setId('123')
                ->setProjectId('123')
                ->setTokenId('123'),
            [],
        ];

        yield 'withSomeValues' => [
            (new MLDeployment())
                ->setId('123')
                ->setProjectId('123')
                ->setTokenId('123')
                ->setModelName('modelName')
                ->setModelVersion('modelVersion')
                ->setModelStage('production')
                ->setUrl('url')
                ->setTrackingTokenId('12345')
                ->setError('error')
                ->setCreatedTimestamp('20222-05-27T12:12:12')
                ->setUpdatedTimestamp('20222-05-27T12:12:12'),
            [
                'modelName' => 'modelName',
                'modelVersion' => 'modelVersion',
                'modelStage' => 'production',
                'trackingTokenId' => '12345',
                'url' => 'url',
                'error' => 'error',
            ],
        ];
    }

    public function testSetTrackingTokenId(): void
    {
        $mlDeployment = (new MLDeployment())
            ->setId('123')
            ->setProjectId('123')
            ->setTokenId('123');
        self::assertArrayNotHasKey('trackingTokenId', $mlDeployment->toArray());

        $mlDeployment->setTrackingTokenId('token');
        self::assertEquals('token', $mlDeployment->getTrackingTokenId());

        $mlDeployment->clearTackingTokenId();
        self::assertEquals('', $mlDeployment->getTrackingTokenId());

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            'Cannot set the trackingTokenId to an empty value, use the clearTrackingTokenId method instead'
        );
        $mlDeployment->setTrackingTokenId('');
    }
}

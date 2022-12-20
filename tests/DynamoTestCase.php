<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use PHPUnit\Framework\TestCase;

class DynamoTestCase extends TestCase
{
    protected function setUp(): void
    {
        $dynamo = new DynamoDbClient([
            'endpoint' => getenv('DYNAMO_ENDPOINT'),
            'region' => 'us-east-1',
            'retries' => 3,
            'version' => '2012-08-10',
        ]);

        try {
            $dynamo->createTable([
                'TableName' => (string) getenv('DYNAMO_TABLE_SANDBOXES'),
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'projectId',
                        'AttributeType' => 'S',
                    ],
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'projectId',
                        'KeyType' => 'HASH',
                    ],
                    [
                        'AttributeName' => 'id',
                        'KeyType' => 'RANGE',
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 1,
                    'WriteCapacityUnits' => 1,
                ],
            ]);

            $dynamo->createTable([
                'TableName' => (string) getenv('DYNAMO_TABLE_RUNS'),
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'sandboxId',
                        'AttributeType' => 'S',
                    ],
                    [
                        'AttributeName' => 'startTimestamp',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'sandboxId',
                        'KeyType' => 'HASH',
                    ],
                    [
                        'AttributeName' => 'startTimestamp',
                        'KeyType' => 'RANGE',
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 1,
                    'WriteCapacityUnits' => 1,
                ],
            ]);

            $dynamo->createTable([
                'TableName' => (string) getenv('DYNAMO_TABLE_PROJECTS'),
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'id',
                        'KeyType' => 'HASH',
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 1,
                    'WriteCapacityUnits' => 1,
                ],
            ]);

            $dynamo->createTable([
                'TableName' => (string) getenv('DYNAMO_TABLE_ML_DEPLOYMENTS'),
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'projectId',
                        'AttributeType' => 'S',
                    ],
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'projectId',
                        'KeyType' => 'HASH',
                    ],
                    [
                        'AttributeName' => 'id',
                        'KeyType' => 'RANGE',
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 1,
                    'WriteCapacityUnits' => 1,
                ],
            ]);
        } catch (DynamoDbException $e) {
        }
    }
}

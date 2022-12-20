<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Keboola\Sandboxes\Api\Client;
use Keboola\Sandboxes\Api\ManageClient;
use Keboola\Sandboxes\Api\Sandbox;
use Keboola\Sandboxes\Api\SandboxCredentials;
use Keboola\StorageApi\Client as StorageClient;
use Keboola\StorageApi\Components;
use Keboola\StorageApi\Options\Components\Configuration;
use PHPUnit\Framework\TestCase;

class ClientFunctionalAWSTest extends TestCase
{
    protected string $configurationId;
    protected Components $componentsClient;
    protected Client $client;
    protected ManageClient $manageClient;

    protected function setUp(): void
    {
        $storageClient = new StorageClient([
            'url' => getenv('KBC_URL'),
            'token' => getenv('KBC_STORAGE_TOKEN'),
        ]);
        $this->componentsClient = new Components($storageClient);
        $this->configurationId = (string) rand(1000, 9999999);
        $this->componentsClient->addConfiguration(
            (new Configuration())
                ->setComponentId('transformation')
                ->setConfigurationId($this->configurationId)
                ->setName($this->configurationId)
        );

        $apiUrl = (string) getenv('API_URL');
        $storageToken = (string) getenv('KBC_STORAGE_TOKEN');
        $manageToken = (string) getenv('KBC_MANAGE_TOKEN');
        $this->client = new Client($apiUrl, $storageToken);
        $this->manageClient = new ManageClient($apiUrl, $manageToken);

        $dynamo = new DynamoDbClient([
            'endpoint' => getenv('DYNAMO_ENDPOINT'),
            'region' => 'us-east-1',
            'retries' => 3,
            'version' => '2012-08-10',
        ]);

        try {
            $dynamo->createTable([
                'TableName' => getenv('DYNAMO_TABLE_SANDBOXES'),
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

    public function testSandboxWithCredentials(): void
    {
        $credentialsData = [
            'type' => 'service_account',
            'project_id' => '23432',
            'private_key_id' => '324',
            'client_email' => '234',
            'client_id' => '2342',
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509.com',
            'private_key' => '-----BEGIN PRIVATE KEY-----key-----END PRIVATE KEY-----',
        ];

        // 1. Create
        $sandbox = (new Sandbox())
            ->setType('python')
            ->setConfigurationId($this->configurationId)
            ->setPhysicalId('physicalId')
            ->setHost('host')
            ->setActive(false)
            ->setWorkspaceDetails(['connection' => [
                'database' => 'test-database',
                'schema' => 'test-schema',
                'warehouse' => 'test-warehouse',
            ]])
            ->setCredentials(SandboxCredentials::fromArray($credentialsData))
        ;
        $response = $this->client->create($sandbox);
        $this->assertNotEmpty($response->getId());
        $credentials = $response->getCredentials();
        self::assertNotNull($credentials);
        self::assertSandboxCredentials($credentials, $credentialsData);

        $sandboxId = $response->getId();
        $sandbox->setId($sandboxId);

        // 2. Get
        $response = $this->client->get($sandboxId);
        self::assertNotEmpty($response);
        self::assertNotEmpty($response->getId());
        self::assertFalse($response->getActive());
        self::assertEquals(
            [
                'connection' => [
                    'database' => 'test-database',
                    'schema' => 'test-schema',
                    'warehouse' => 'test-warehouse',
                ],
            ],
            $response->getWorkspaceDetails()
        );
        $credentials = $response->getCredentials();
        self::assertNotNull($credentials);
        self::assertSandboxCredentials($credentials, $credentialsData);

        // 3. Update
        $credentialsData['client_id'] = '123456789';
        $sandbox->setCredentials(SandboxCredentials::fromArray($credentialsData));
        $sandbox->setActive(true);
        $sandbox->setStagingWorkspaceId('768');
        $sandbox->setStagingWorkspaceType('bigquery');
        $this->client->update($sandbox);
        $response = $this->client->get($sandboxId);
        $credentials = $response->getCredentials();
        self::assertNotNull($credentials);
        self::assertSandboxCredentials($credentials, $credentialsData);
        self::assertTrue($response->getActive());
        self::assertEquals('768', $response->getStagingWorkspaceId());
        self::assertEquals('bigquery', $response->getStagingWorkspaceType());

        // 3b. Manage Update
        $credentialsData['client_id'] = '99999';
        $sandbox->setCredentials(SandboxCredentials::fromArray($credentialsData));
        $this->manageClient->updateSandbox($sandbox);
        $response = $this->client->get($sandboxId);
        $credentials = $response->getCredentials();
        self::assertNotNull($credentials);
        self::assertSandboxCredentials($credentials, $credentialsData);

        // 4. List
        $foundInList = false;
        $response = $this->client->list();
        foreach ($response as $s) {
            if ($sandboxId === $s->getId()) {
                $foundInList = true;
                break;
            }
        }
        $this->assertTrue($foundInList);
    }

    private static function assertSandboxCredentials(SandboxCredentials $credentials, array $expected): void
    {
        $data = $credentials->toArray();

        self::assertEquals($expected['type'], $data['type']);
        self::assertEquals($expected['project_id'], $data['project_id']);
        self::assertEquals($expected['private_key_id'], $data['private_key_id']);
        self::assertEquals($expected['client_email'], $data['client_email']);
        self::assertEquals($expected['client_id'], $data['client_id']);
        self::assertEquals($expected['auth_uri'], $data['auth_uri']);
        self::assertEquals($expected['token_uri'], $data['token_uri']);
        self::assertEquals(
            $expected['auth_provider_x509_cert_url'],
            $data['auth_provider_x509_cert_url']
        );
        self::assertEquals(
            $expected['client_x509_cert_url'],
            $data['client_x509_cert_url']
        );
        self::assertNotEmpty($data['private_key']);
    }
}

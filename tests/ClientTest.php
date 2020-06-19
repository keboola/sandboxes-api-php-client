<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Keboola\Sandboxes\Api\Client;
use Keboola\Sandboxes\Api\ManageClient;
use Keboola\Sandboxes\Api\Sandbox;
use Keboola\StorageApi\Components;
use Keboola\StorageApi\Options\Components\Configuration;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    protected string $configurationId;
    protected Components $componentsClient;

    protected function setUp(): void
    {
        $storageClient = new \Keboola\StorageApi\Client([
            'url' => getenv('KBC_URL'),
            'token' => getenv('KBC_STORAGE_TOKEN'),
        ]);
        $this->componentsClient = new Components($storageClient);
        $this->configurationId = (string) rand(1000, 9999);
        $this->componentsClient->addConfiguration(
            (new Configuration())
                ->setComponentId('transformation')
                ->setConfigurationId($this->configurationId)
                ->setName($this->configurationId)
        );
    }

    public function testClient(): void
    {
        $apiUrl = (string) getenv('API_URL');
        $storageToken = (string) getenv('KBC_STORAGE_TOKEN');
        $manageToken = (string) getenv('KBC_MANAGE_TOKEN');

        $client = new Client($apiUrl, $storageToken);
        $manageClient = new ManageClient($apiUrl, $manageToken);

        // 1. Create
        $sandbox = (new Sandbox())
            ->setType('python')
            ->setConfigurationId($this->configurationId)
            ->setPhysicalId('physicalId')
            ->setHost('host')
            ->setPassword('pass')
            ->setActive(false);
        $response = $client->create($sandbox);
        $this->assertNotEmpty($response->getId());

        $sandboxId = $response->getId();
        $sandbox->setId($sandboxId);

        // 2. Get
        $response = $client->get($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertNotEmpty($response->getId());
        $this->assertFalse($response->getActive());

        // 3. Update
        $sandbox->setPassword('new_pass');
        $sandbox->setActive(true);
        $client->update($sandbox);
        $response = $client->get($sandboxId);
        $this->assertEquals('new_pass', $response->getPassword());
        $this->assertTrue($response->getActive());

        // 4. List
        $foundInList = false;
        $response = $client->list();
        foreach ($response as $s) {
            if ($sandboxId === $s->getId()) {
                $foundInList = true;
                break;
            }
        }
        $this->assertTrue($foundInList);

        // 5. Deactivate
        $client->deactivate($sandboxId);

        // 6. Get and check if deactivated
        $response = $client->get($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertFalse($response->getActive());
        $this->assertEmpty($response->getDeletedTimestamp());

        // 7. Find in list of expired sandboxes
        $foundInList = false;
        $response = $manageClient->listExpired();
        foreach ($response as $r) {
            if ($r->getId() === $sandboxId) {
                $foundInList = true;
            }
        }
        $this->assertTrue($foundInList);

        // 8. Delete
        $manageClient->delete($sandboxId);

        // 9. Manage get and check if deleted
        $response = $manageClient->get($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertNotEmpty($response->getDeletedTimestamp());
    }

    protected function tearDown(): void
    {
        $this->componentsClient->deleteConfiguration('transformation', $this->configurationId);
    }
}

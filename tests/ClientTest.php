<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Keboola\Sandboxes\Api\Client;
use Keboola\Sandboxes\Api\Exception\ClientException;
use Keboola\Sandboxes\Api\MLDeployment;
use Keboola\Sandboxes\Api\ManageClient;
use Keboola\Sandboxes\Api\Project;
use Keboola\Sandboxes\Api\Sandbox;
use Keboola\StorageApi\Components;
use Keboola\StorageApi\Options\Components\Configuration;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    protected string $configurationId;
    protected Components $componentsClient;
    protected Client $client;
    protected ManageClient $manageClient;

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

        $apiUrl = (string) getenv('API_URL');
        $storageToken = (string) getenv('KBC_STORAGE_TOKEN');
        $manageToken = (string) getenv('KBC_MANAGE_TOKEN');
        $this->client = new Client($apiUrl, $storageToken);
        $this->manageClient = new ManageClient($apiUrl, $manageToken);
    }

    public function testClient(): void
    {
        // 1. Create
        $sandbox = (new Sandbox())
            ->setType('python')
            ->setConfigurationId($this->configurationId)
            ->setPhysicalId('physicalId')
            ->setHost('host')
            ->setPassword('pass')
            ->setActive(false)
            ->setWorkspaceDetails(['connection' => [
                'database' => 'test-database',
                'schema' => 'test-schema',
            ]])
            ->setDatabricksSparkVersion('Spark-1')
            ->setDatabricksNodeType('Node-2')
            ->setDatabricksNumberOfNodes(3);
        $response = $this->client->create($sandbox);
        $this->assertNotEmpty($response->getId());

        $sandboxId = $response->getId();
        $sandbox->setId($sandboxId);

        // 2. Get
        $response = $this->client->get($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertNotEmpty($response->getId());
        $this->assertFalse($response->getActive());
        $this->assertEquals(
            [
                'connection' => ['database' => 'test-database', 'schema' => 'test-schema'],
            ],
            $response->getWorkspaceDetails()
        );
        $this->assertEquals('Spark-1', $response->getDatabricksSparkVersion());
        $this->assertEquals('Node-2', $response->getDatabricksNodeType());
        $this->assertEquals(3, $response->getDatabricksNumberOfNodes());

        // 3. Update
        $sandbox->setPassword('new_pass');
        $sandbox->setActive(true);
        $sandbox->setStagingWorkspaceId('768');
        $sandbox->setStagingWorkspaceType('synapse');
        $this->client->update($sandbox);
        $response = $this->client->get($sandboxId);
        $this->assertEquals('new_pass', $response->getPassword());
        $this->assertTrue($response->getActive());
        $this->assertEquals('768', $response->getStagingWorkspaceId());
        $this->assertEquals('synapse', $response->getStagingWorkspaceType());

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

        // 5. Deactivate
        $response = $this->client->deactivate($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertFalse($response->getActive());
        $this->assertEmpty($response->getDeletedTimestamp());

        // 6. Activate
        $response = $this->client->activate($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertTrue($response->getActive());

        // 7. Set to be expired
        $sandbox->setExpirationTimestamp(date('c', strtotime('-1 day')));
        $this->client->update($sandbox);

        // 8. Find in list of expired sandboxes
        $foundInList = false;
        $response = $this->manageClient->listExpired();
        foreach ($response as $r) {
            if ($r->getId() === $sandboxId) {
                $foundInList = true;
            }
        }
        $this->assertTrue($foundInList);

        // 9. Manage deactivate
        $this->manageClient->deactivate($sandboxId);

        // 10. Manage get and check if deactivated
        $response = $this->manageClient->get($sandboxId);
        $this->assertNotEmpty($response);
        $this->assertFalse($response->getActive());

        // 11. Delete
        $response = $this->client->delete($sandboxId);
        $this->assertNotEmpty($response->getDeletedTimestamp());
    }

    public function testProject(): void
    {
        $projectId = explode('-', (string) getenv('KBC_STORAGE_TOKEN'))[0];
        $project = (new Project())
            ->setId($projectId)
            ->setMLflowUri('/mlflow')
            ->setMLflowAbsSas('/abs');
        $result = $this->manageClient->updateProject($project);
        $this->assertEquals('/mlflow', $result->getMlflowUri());
        $this->assertEquals('/abs', $result->getMlflowAbsSas());

        $result = $this->manageClient->getProject($projectId);
        $this->assertEquals('/mlflow', $result->getMlflowUri());
        $this->assertEquals('/abs', $result->getMlflowAbsSas());

        $result = $this->client->getProject();
        $this->assertEquals('/mlflow', $result->getMlflowUri());
        $this->assertEquals('/abs', $result->getMlflowAbsSas());

        $project->setMLflowUri(null);
        $result = $this->manageClient->updateProject($project);
        $this->assertEquals(null, $result->getMlflowUri());
        $this->assertEquals('/abs', $result->getMlflowAbsSas());
    }

    public function testMLDeployment(): void
    {
        $tokenParts = explode('-', (string) getenv('KBC_STORAGE_TOKEN'));
        $projectId = $tokenParts[0];
        $tokenId = $tokenParts[1];
        $deployment = (new MLDeployment())
            ->setModelName('mlflow-model')
            ->setModelVersion('4')
            ->setModelStage('Production');
        $createdDeployment = $this->client->createMLDeployment($deployment);
        $this->assertNotEmpty($createdDeployment->getId());
        $this->assertNotEmpty($createdDeployment->getCreatedTimestamp());
        $this->assertEquals('mlflow-model', $createdDeployment->getModelName());
        $this->assertEquals('4', $createdDeployment->getModelVersion());
        $this->assertEquals('Production', $createdDeployment->getModelStage());
        $this->assertEquals($projectId, $createdDeployment->getProjectId());
        $this->assertEquals($tokenId, $createdDeployment->getTokenId());
        $this->assertEmpty($createdDeployment->getUrl());
        $this->assertEmpty($createdDeployment->getError());

        $getDeployment = $this->client->getMLDeployment($createdDeployment->getId());
        $this->assertEquals('mlflow-model', $getDeployment->getModelName());
        $this->assertEquals('4', $getDeployment->getModelVersion());
        $this->assertEquals('Production', $getDeployment->getModelStage());
        $this->assertEmpty($getDeployment->getUrl());
        $this->assertEmpty($getDeployment->getError());

        $listDeployments = $this->client->listMLDeployments();
        $this->assertGreaterThan(0, $listDeployments);
        $this->assertInstanceOf(MLDeployment::class, $listDeployments[0]);

        $createdDeployment->setUrl('/path/to/model');
        $createdDeployment->setError('App Error');
        $createdDeployment->setModelVersion('5');
        $createdDeployment->setModelStage('Staging');
        $updatedDeployment = $this->client->updateMLDeployment($createdDeployment);
        $this->assertEquals('/path/to/model', $updatedDeployment->getUrl());
        $this->assertEquals('App Error', $updatedDeployment->getError());
        $this->assertEquals('5', $updatedDeployment->getModelVersion());
        $this->assertEquals('Staging', $updatedDeployment->getModelStage());

        $getDeployment = $this->client->updateMLDeployment($createdDeployment);
        $this->assertEquals('/path/to/model', $getDeployment->getUrl());
        $this->assertEquals('App Error', $getDeployment->getError());
        $this->assertEquals('5', $getDeployment->getModelVersion());
        $this->assertEquals('Staging', $getDeployment->getModelStage());

        $this->client->deleteMLDeployment($createdDeployment->getId());
        try {
            $this->client->getMLDeployment($createdDeployment->getId());
            $this->fail();
        } catch (ClientException $e) {
            // Good
        }
    }

    protected function tearDown(): void
    {
        $this->componentsClient->deleteConfiguration('transformation', $this->configurationId);
    }
}

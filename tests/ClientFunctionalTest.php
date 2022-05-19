<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use Keboola\Sandboxes\Api\Client;
use Keboola\Sandboxes\Api\Exception\ClientException;
use Keboola\Sandboxes\Api\ListOptions;
use Keboola\Sandboxes\Api\ManageClient;
use Keboola\Sandboxes\Api\MLDeployment;
use Keboola\Sandboxes\Api\PersistentStorage;
use Keboola\Sandboxes\Api\Project;
use Keboola\Sandboxes\Api\Sandbox;
use Keboola\Sandboxes\Api\SandboxSizeParameters;
use Keboola\StorageApi\Client as StorageClient;
use Keboola\StorageApi\Components;
use Keboola\StorageApi\Options\Components\Configuration;
use PHPUnit\Framework\TestCase;

class ClientFunctionalTest extends TestCase
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
            ->setDatabricksNumberOfNodes(3)
            ->setDatabricksClusterId('12345');
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
        $this->assertEquals('12345', $response->getDatabricksClusterId());

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

        // 3b. Manage Update
        $sandbox->setStagingWorkspaceId('890');
        $this->manageClient->updateSandbox($sandbox);
        $response = $this->client->get($sandboxId);
        $this->assertEquals('890', $response->getStagingWorkspaceId());
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

    public function testCreateMinimalSandbox(): void
    {
        $sandbox = (new Sandbox())
            ->setActive(true)
            ->setType('python')
        ;

        $response = $this->client->create($sandbox);
        self::assertTrue($response->getActive());
        self::assertSame('python', $response->getType());
        self::assertNull($response->getBranchId());
        self::assertEmpty($response->getSize());
        self::assertNull($response->getSizeParameters());
        self::assertNotEmpty($response->getId());
        self::assertNotEmpty($response->getProjectId());
        self::assertNotEmpty($response->getTokenId());
        self::assertNotEmpty($response->getCreatedTimestamp());
        self::assertNotEmpty($response->getUpdatedTimestamp());
    }

    public function testCreateSandboxWithBranch(): void
    {
        $sandbox = (new Sandbox())
            ->setActive(true)
            ->setType('python')
            ->setBranchId('1234')
        ;

        $response = $this->client->create($sandbox);
        self::assertTrue($response->getActive());
        self::assertSame('python', $response->getType());
        self::assertSame('1234', $response->getBranchId());
        self::assertNotEmpty($response->getId());
        self::assertNotEmpty($response->getProjectId());
        self::assertNotEmpty($response->getTokenId());
        self::assertNotEmpty($response->getCreatedTimestamp());
        self::assertNotEmpty($response->getUpdatedTimestamp());
    }

    public function testSandboxesOnBranch(): void
    {
        $createdSandbox = $this->client->create(
            (new Sandbox())
                ->setActive(true)
                ->setType('python')
                ->setBranchId('1234')
        );

        $noBranchResponse = $this->client->list();
        self::assertNotContains(
            $createdSandbox->getId(),
            array_map(fn(Sandbox $s) => $s->getId(), $noBranchResponse)
        );

        $branchResponse = $this->client->list(ListOptions::create()->setBranchId('1234'));
        self::assertContains(
            $createdSandbox->getId(),
            array_map(fn(Sandbox $s) => $s->getId(), $branchResponse)
        );

        $notifyClient = new ManageClient(
            (string) getenv('API_URL'),
            (string) getenv('KBC_MANAGE_NOTIFY_TOKEN')
        );
        $notifyClient->notifyBranchDeleted('1234');
        // sleeping here because this request is asyncronous so we wait for the records to be deleted
        sleep(3);
        $branchResponse = $this->client->list(ListOptions::create()->setBranchId('1234'));
        self::assertNotContains(
            $createdSandbox->getId(),
            array_map(fn(Sandbox $s) => $s->getId(), $branchResponse)
        );
    }

    public function testCreateSandboxWithSize(): void
    {
        $sandbox = (new Sandbox())
            ->setActive(true)
            ->setType('python')
            ->setSize(Sandbox::CONTAINER_SIZE_SMALL)
            ->setSizeParameters(SandboxSizeParameters::create()->setStorageSizeGB(128))
        ;

        $response = $this->client->create($sandbox);
        self::assertTrue($response->getActive());
        self::assertSame('python', $response->getType());
        self::assertSame(Sandbox::CONTAINER_SIZE_SMALL, $response->getSize());
        self::assertNotNull($response->getSizeParameters());
        self::assertSame(128, $response->getSizeParameters()->getStorageSizeGB());
        self::assertNotEmpty($response->getId());
        self::assertNotEmpty($response->getProjectId());
        self::assertNotEmpty($response->getTokenId());
        self::assertNotEmpty($response->getCreatedTimestamp());
        self::assertNotEmpty($response->getUpdatedTimestamp());
    }

    public function testAddPersistentStorageToExistingSandbox(): void
    {
        $sandbox = (new Sandbox())
            ->setActive(true)
            ->setType('python')
        ;

        $response = $this->client->create($sandbox);
        self::assertNull($response->getPersistentStoragePvcName());
        self::assertNull($response->getPersistentStorageK8sManifest());

        $response->setPersistentStoragePvcName('foo-pvc');
        $response->setPersistentStorageK8sManifest('k8s-manifest');
        $response = $this->client->update($response);

        self::assertSame('foo-pvc', $response->getPersistentStoragePvcName());
        self::assertSame('k8s-manifest', $response->getPersistentStorageK8sManifest());
    }

    public function testRemovePersistentStorageFromExistingSandbox(): void
    {
        $sandbox = (new Sandbox())
            ->setActive(true)
            ->setType('python')
            ->setPersistentStoragePvcName('foo-pvc')
            ->setPersistentStorageK8sManifest('k8s-manifest')
        ;

        $response = $this->client->create($sandbox);
        self::assertSame('foo-pvc', $response->getPersistentStoragePvcName());
        self::assertSame('k8s-manifest', $response->getPersistentStorageK8sManifest());

        $response->removePersistentStoragePvcName();
        $response->removePersistentStorageK8sManifest();
        $response = $this->client->update($response);

        self::assertNull($response->getPersistentStoragePvcName());
        self::assertNull($response->getPersistentStorageK8sManifest());
    }

    public function testProject(): void
    {
        $projectId = explode('-', (string) getenv('KBC_STORAGE_TOKEN'))[0];
        $project = (new Project())
            ->setId($projectId)
            ->setMlflowUri('/mlflow')
            ->setMlflowRequiresAuth(true)
            ->setMlflowAbsSas('/abs')
            ->setMlflowServerVersion('1.2.3');
        $result = $this->manageClient->updateProject($project);
        self::assertSame('/mlflow', $result->getMlflowUri());
        self::assertTrue($project->getMlflowRequiresAuth());
        self::assertSame('/abs', $result->getMlflowAbsSas());
        self::assertSame(
            'BlobEndpoint=https://abs-account.blob.core.windows.net/;SharedAccessSignature=/abs',
            $result->getMlflowAbsConnectionString()
        );
        self::assertSame('1.2.3', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());

        $result = $this->manageClient->getProject($projectId);
        self::assertSame('/mlflow', $result->getMlflowUri());
        self::assertTrue($project->getMlflowRequiresAuth());
        self::assertSame('/abs', $result->getMlflowAbsSas());
        self::assertSame(
            'BlobEndpoint=https://abs-account.blob.core.windows.net/;SharedAccessSignature=/abs',
            $result->getMlflowAbsConnectionString()
        );
        self::assertSame('1.2.3', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());

        $result = $this->client->getProject();
        self::assertSame('/mlflow', $result->getMlflowUri());
        self::assertTrue($project->getMlflowRequiresAuth());
        self::assertSame('/abs', $result->getMlflowAbsSas());
        self::assertSame(
            'BlobEndpoint=https://abs-account.blob.core.windows.net/;SharedAccessSignature=/abs',
            $result->getMlflowAbsConnectionString()
        );
        self::assertSame('1.2.3', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());

        $project->setMlflowUri(null);
        $result = $this->manageClient->updateProject($project);
        self::assertSame('', $result->getMlflowUri());
        self::assertTrue($project->getMlflowRequiresAuth());
        self::assertSame('/abs', $result->getMlflowAbsSas());
        self::assertSame(
            'BlobEndpoint=https://abs-account.blob.core.windows.net/;SharedAccessSignature=/abs',
            $result->getMlflowAbsConnectionString()
        );

        $project->setMlflowRequiresAuth(false);
        $result = $this->manageClient->updateProject($project);
        self::assertSame('', $result->getMlflowUri());
        self::assertFalse($project->getMlflowRequiresAuth());
        self::assertSame('/abs', $result->getMlflowAbsSas());
        self::assertSame(
            'BlobEndpoint=https://abs-account.blob.core.windows.net/;SharedAccessSignature=/abs',
            $result->getMlflowAbsConnectionString()
        );

        $project->setMlflowAbsSas(null);
        $result = $this->manageClient->updateProject($project);
        self::assertSame('', $result->getMlflowUri());
        self::assertFalse($project->getMlflowRequiresAuth());
        self::assertSame('', $result->getMlflowAbsSas());
        self::assertSame('', $result->getMlflowAbsConnectionString());

        $project->setMlflowServerVersion(null);
        $result = $this->manageClient->updateProject($project);
        self::assertFalse($project->getMlflowRequiresAuth());
        self::assertSame('', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());

        $result = $this->client->updateProjectServerVersion($projectId, '1.2.4');
        self::assertFalse($project->getMlflowRequiresAuth());
        self::assertSame('1.2.4', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());

        $result = $this->client->updateProjectServerVersion($projectId, null);
        self::assertFalse($project->getMlflowRequiresAuth());
        self::assertSame('', $result->getMlflowServerVersion());
        self::assertNotEmpty($result->getMlflowServerVersionLatest());
    }

    public function testProjectPersistentStorage(): void
    {
        $projectId = explode('-', (string) getenv('KBC_STORAGE_TOKEN'))[0];
        $project = (new Project())
            ->setId($projectId)
            ->setMlflowUri('/mlflow')
            ->setMlflowAbsSas('/abs')
            ->setMlflowServerVersion('1.2.3')
        ;
        // init - persistent storage is null (no instance of PersistentStorage)
        self::assertNull($project->getPersistentStorage());

        // setup persistent storage
        $project->setPersistentStorage(
            PersistentStorage::create()
                ->setReady(true)
                ->setK8sStorageClassName('storage-class')
        );
        $result = $this->manageClient->updateProject($project);
        $persistentStorage = $result->getPersistentStorage();
        self::assertNotNull($persistentStorage);
        self::assertTrue($persistentStorage->isReady());
        self::assertSame('storage-class', $persistentStorage->getK8sStorageClassName());

        // check current values
        $persistentStorage = $this->client->getPersistentStorage();
        self::assertNotNull($persistentStorage);
        self::assertTrue($persistentStorage->isReady());
        self::assertSame('storage-class', $persistentStorage->getK8sStorageClassName());

        // still has persistentStorage
        $project2 = (new Project())
            ->setId($projectId)
            ->setMlflowServerVersion('1.2.4')
        ;
        $result = $this->manageClient->updateProject($project2);
        self::assertSame('1.2.4', $result->getMlflowServerVersion());
        $persistentStorage = $result->getPersistentStorage();
        self::assertNotNull($persistentStorage);
        self::assertTrue($persistentStorage->isReady());
        self::assertSame('storage-class', $persistentStorage->getK8sStorageClassName());

        // set storage not ready
        $project->setPersistentStorage(
            PersistentStorage::create()
                ->setReady(false)
        );
        $persistentStorage = $this->manageClient->updateProject($project)->getPersistentStorage();
        self::assertNotNull($persistentStorage);
        self::assertFalse($persistentStorage->isReady());
        self::assertSame('storage-class', $persistentStorage->getK8sStorageClassName());

        $persistentStorage = $this->client->getPersistentStorage();
        self::assertFalse($persistentStorage->isReady());
        self::assertSame('storage-class', $persistentStorage->getK8sStorageClassName());

        // remove persistent storage
        $project->setPersistentStorage(
            PersistentStorage::create()
                ->setReady(null)
                ->setK8sStorageClassName(null)
        );
        $persistentStorage = $this->manageClient->updateProject($project)->getPersistentStorage();
        self::assertNotNull($persistentStorage);
        self::assertNull($persistentStorage->isReady());
        self::assertNull($persistentStorage->getK8sStorageClassName());

        $persistentStorage = $this->client->getPersistentStorage();
        self::assertNull($persistentStorage->isReady());
        self::assertNull($persistentStorage->getK8sStorageClassName());
    }

    public function testMLDeployment(): void
    {
        $tokenParts = explode('-', (string) getenv('KBC_STORAGE_TOKEN'));
        $projectId = $tokenParts[0];
        $tokenId = $tokenParts[1];
        $trackingTokenId = '12345';
        $deployment = (new MLDeployment())
            ->setModelName('mlflow-model')
            ->setModelVersion('4')
            ->setModelStage('Production')
            ->setTrackingTokenId('12345')
        ;
        $createdDeployment = $this->client->createMLDeployment($deployment);
        $this->assertNotEmpty($createdDeployment->getId());
        $this->assertNotEmpty($createdDeployment->getCreatedTimestamp());
        $this->assertEquals('mlflow-model', $createdDeployment->getModelName());
        $this->assertEquals('4', $createdDeployment->getModelVersion());
        $this->assertEquals('Production', $createdDeployment->getModelStage());
        $this->assertEquals($projectId, $createdDeployment->getProjectId());
        $this->assertEquals($tokenId, $createdDeployment->getTokenId());
        $this->assertEquals($trackingTokenId, $createdDeployment->getTrackingTokenId());
        $this->assertEmpty($createdDeployment->getUrl());
        $this->assertEmpty($createdDeployment->getError());

        $getDeployment = $this->client->getMLDeployment($createdDeployment->getId());
        $this->assertEquals('mlflow-model', $getDeployment->getModelName());
        $this->assertEquals('4', $getDeployment->getModelVersion());
        $this->assertEquals('Production', $getDeployment->getModelStage());
        $this->assertEquals($trackingTokenId, $createdDeployment->getTrackingTokenId());
        $this->assertEmpty($getDeployment->getUrl());
        $this->assertEmpty($getDeployment->getError());

        $listDeployments = $this->client->listMLDeployments();
        $this->assertGreaterThan(0, $listDeployments);
        $this->assertInstanceOf(MLDeployment::class, $listDeployments[0]);

        $createdDeployment->setUrl('/path/to/model');
        $createdDeployment->setError('App Error');
        $createdDeployment->setModelVersion('5');
        $createdDeployment->setModelStage('Staging');
        $createdDeployment->clearTackingTokenId();
        $updatedDeployment = $this->client->updateMLDeployment($createdDeployment);
        $this->assertEquals('/path/to/model', $updatedDeployment->getUrl());
        $this->assertEquals('App Error', $updatedDeployment->getError());
        $this->assertEquals('5', $updatedDeployment->getModelVersion());
        $this->assertEquals('Staging', $updatedDeployment->getModelStage());
        $this->assertEquals('', $createdDeployment->getTrackingTokenId());

        $getDeployment = $this->client->updateMLDeployment($createdDeployment);
        $this->assertEquals('/path/to/model', $getDeployment->getUrl());
        $this->assertEquals('App Error', $getDeployment->getError());
        $this->assertEquals('5', $getDeployment->getModelVersion());
        $this->assertEquals('Staging', $getDeployment->getModelStage());
        $this->assertArrayNotHasKey('trackingTokenId', $getDeployment->toArray());

        $this->client->deleteMLDeployment($createdDeployment->getId());

        $this->expectExceptionCode(404);
        $this->client->getMLDeployment($createdDeployment->getId());
    }

    protected function tearDown(): void
    {
        $this->componentsClient->deleteConfiguration('transformation', $this->configurationId);
    }
}

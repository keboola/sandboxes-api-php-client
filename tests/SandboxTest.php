<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api\Tests;

use DateTimeImmutable;
use Keboola\Sandboxes\Api\Sandbox;
use Keboola\Sandboxes\Api\SandboxSizeParameters;
use PHPUnit\Framework\TestCase;

class SandboxTest extends TestCase
{
    public function testGetters(): void
    {
        $sandbox = Sandbox::fromArray([
            'id' => 'id',
            'projectId' => 'project-id',
            'tokenId' => 'token-id',
            'type' => 'python',
            'active' => true,
            'shared' => true,
            'createdTimestamp' => '2024-02-02 12:00:00',
            'updatedTimestamp' => '2024-02-02 14:00:00',
            'deletedTimestamp' => '2024-02-02 16:00:00',
            'branchId' => 'branch-id',
            'configurationId' => 'configuration-id',
            'physicalId' => 'physical-id',
            'size' => 'small',
            'sizeParameters' => [
                'storageSize_GB' => 10,
            ],
            'imageVersion' => 'image-version',
            'packages' => ['foo', 'bar'],
            'expirationTimestamp' => '2024-02-02 18:00:00',
            'expirationAfterHours' => 1,
            'user' => 'user',
            'password' => 'password',
            'host' => 'host',
            'url' => 'url',
            'stagingWorkspaceId' => 'staging-workspace-id',
            'stagingWorkspaceType' => 'staging-workspace-type',
            'workspaceDetails' => ['foo' => 'bar'],
            'lastAutosaveTimestamp' => '2024-02-02 20:00:00',
            'autosaveTokenId' => 'autosave-token-id',
            'databricks' => [
                'sparkVersion' => 'databricks-spark-version',
                'nodeType' => 'databricks-node-type',
                'numberOfNodes' => 5,
                'clusterId' => 'databricks-cluster-id',
            ],
            'persistentStorage' => [
                'pvcName' => 'pvc-name',
                'k8sManifest' => 'pvc-manifest',
            ],
        ]);

        self::assertSame('id', $sandbox->getId());
        self::assertSame('project-id', $sandbox->getProjectId());
        self::assertSame('small', $sandbox->getSize());
        self::assertTrue($sandbox->getActive());
        self::assertTrue($sandbox->getShared());
        self::assertSame('python', $sandbox->getType());
        self::assertSame('user', $sandbox->getUser());
        self::assertSame('password', $sandbox->getPassword());
        self::assertSame('host', $sandbox->getHost());
        self::assertSame('url', $sandbox->getUrl());
        self::assertSame('branch-id', $sandbox->getBranchId());
        self::assertSame('configuration-id', $sandbox->getConfigurationId());
        self::assertSame('physical-id', $sandbox->getPhysicalId());
        self::assertSame('image-version', $sandbox->getImageVersion());
        self::assertSame(['foo', 'bar'], $sandbox->getPackages());
        self::assertSame('2024-02-02 12:00:00', $sandbox->getCreatedTimestamp());
        self::assertSame('2024-02-02 14:00:00', $sandbox->getUpdatedTimestamp());
        self::assertSame('2024-02-02 18:00:00', $sandbox->getExpirationTimestamp());
        self::assertSame(1, $sandbox->getExpirationAfterHours());
        self::assertSame('2024-02-02 16:00:00', $sandbox->getDeletedTimestamp());
        self::assertSame('staging-workspace-id', $sandbox->getStagingWorkspaceId());
        self::assertSame('staging-workspace-type', $sandbox->getStagingWorkspaceType());
        self::assertSame(['foo' => 'bar'], $sandbox->getWorkspaceDetails());
        self::assertSame('2024-02-02 20:00:00', $sandbox->getLastAutosaveTimestamp());
        self::assertSame('autosave-token-id', $sandbox->getAutosaveTokenId());
        self::assertEquals((new SandboxSizeParameters())->setStorageSizeGB(10), $sandbox->getSizeParameters());
        self::assertSame('databricks-spark-version', $sandbox->getDatabricksSparkVersion());
        self::assertSame('databricks-node-type', $sandbox->getDatabricksNodeType());
        self::assertSame(5, $sandbox->getDatabricksNumberOfNodes());
        self::assertSame('databricks-cluster-id', $sandbox->getDatabricksClusterId());
        self::assertSame('pvc-name', $sandbox->getPersistentStoragePvcName());
        self::assertSame('pvc-manifest', $sandbox->getPersistentStorageK8sManifest());
    }

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
            'createdTimestamp' => (new DateTimeImmutable())->format('c'),
        ]);
        $nullPassword = $sandbox->getPassword();
        self::assertEmpty($nullPassword);
    }
}

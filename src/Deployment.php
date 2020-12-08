<?php


namespace Keboola\Sandboxes\Api;


class Deployment
{
    private string $id;
    private string $deploymentUrl;
    private string $projectId;
    private string $modelName;
    private string $modelVersion;
    private string $createdTimestamp;
    private string $updatedTimestamp;

    public static function fromArray(array $in): self
    {
        $deployment = (new Deployment())
            ->setId((string) $in['id'])
            ->setProjectId($in['projectId'])
            ->setTokenId($in['tokenId'])
            ->setModelName($in['modelName'] ?? '')
            ->setModelVersion($in['modelVersion'] ?? '')
            ->setDeploymentUrl($in['deploymentUrl'] ?? '');
        return $deployment;
    }

    public function setModelName(string $modelName): self
    {
        $this->modelName = $modelName;
        return $this;
    }

    public function setModelVersion(string $modelVersion): self
    {
        $this->modelVersion = $modelVersion;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setDeploymentUrl(string $deploymentUrl): self
    {
        $this->deploymentUrl = $deploymentUrl;
        return $this;
    }

    public function setProjectId(string $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function setTokenId(string $tokenId): self
    {
        $this->tokenId = $tokenId;
        return $this;
    }
}

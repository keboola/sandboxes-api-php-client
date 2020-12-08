<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class Deployment
{
    private string $id;
    private string $projectId;
    private string $tokenId;
    private string $modelName;
    private string $modelVersion;
    private string $deploymentUrl;
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

    public function toArray(): array
    {
        $result = [];
        if (!empty($this->id)) {
            $result['id'] = $this->id;
        }
        if (!empty($this->projectId)) {
            $result['projectId'] = $this->projectId;
        }
        if (!empty($this->tokenId)) {
            $result['tokenId'] = $this->tokenId;
        }
        if (!empty($this->modelName)) {
            $result['modelName'] = $this->modelName;
        }
        if (!empty($this->modelVersion)) {
            $result['modelVersion'] = $this->modelVersion;
        }
        if (!empty($this->deploymentUrl)) {
            $result['deploymentUrl'] = $this->deploymentUrl;
        }
        if (!empty($this->createdTimestamp)) {
            $result['createdTimestamp'] = $this->createdTimestamp;
        }
        if (!empty($this->updatedTimestamp)) {
            $result['updatedTimestamp'] = $this->updatedTimestamp;
        }
        return $result;
    }

    public function toApiRequest(): array
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['createdTimestamp']);
        unset($array['updatedTimestamp']);
        return $array;
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

    public function getModelName(): string
    {
        return $this->modelName;
    }

    public function getModelVersion(): string
    {
        return $this->modelVersion;
    }

    public function getDeploymentUrl(): string
    {
        return $this->deploymentUrl;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedTimestamp(): string
    {
        return $this->createdTimestamp;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getTokenId(): string
    {
        return $this->tokenId;
    }
}

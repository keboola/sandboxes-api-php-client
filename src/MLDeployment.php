<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use Keboola\Sandboxes\Api\Exception\InvalidArgumentException;

class MLDeployment
{
    private string $id;
    private string $projectId;
    private string $tokenId;
    private string $modelName;
    private string $modelVersion;
    private string $modelStage;
    private string $url;
    private string $error;
    private string $createdTimestamp;
    private string $updatedTimestamp;

    private ?string $trackingTokenId = null;

    public static function fromArray(array $in): self
    {
        $mlDeployment = (new MLDeployment())
            ->setId((string) $in['id'])
            ->setProjectId((string) $in['projectId'])
            ->setTokenId((string) $in['tokenId'])
            ->setModelName($in['modelName'] ?? '')
            ->setModelVersion($in['modelVersion'] ?? '')
            ->setModelStage($in['modelStage'] ?? '')
            ->setUrl($in['url'] ?? '')
            ->setError($in['error'] ?? '')
            ->setCreatedTimestamp($in['createdTimestamp'] ?? '')
            ->setUpdatedTimestamp($in['updatedTimestamp'] ?? '');

        if (!empty($in['trackingTokenId'])) {
            $mlDeployment->setTrackingTokenId($in['trackingTokenId']);
        }
        return $mlDeployment;
    }

    public function toArray(): array
    {
        $result = [];
        if (!empty($this->id)) {
            $result['id'] = $this->id;
        }
        if (!empty($this->modelName)) {
            $result['modelName'] = $this->modelName;
        }
        if (!empty($this->modelVersion)) {
            $result['modelVersion'] = $this->modelVersion;
        }
        if (!empty($this->modelStage)) {
            $result['modelStage'] = $this->modelStage;
        }
        if (!empty($this->url)) {
            $result['url'] = $this->url;
        }
        if (isset($this->trackingTokenId)) {
            $result['trackingTokenId'] = $this->trackingTokenId;
        }
        if (!empty($this->error)) {
            $result['error'] = $this->error;
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

    public function getModelStage(): string
    {
        return $this->modelStage;
    }

    public function setModelStage(string $modelStage): MLDeployment
    {
        $this->modelStage = $modelStage;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setError(string $error): self
    {
        $this->error = $error;
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

    public function setTrackingTokenId(string $trackingTokenId): self
    {
        if (empty($trackingTokenId)) {
            throw new InvalidArgumentException(
                'Cannot set the trackingTokenId to an empty value, use the clearTrackingTokenId method instead'
            );
        }
        $this->trackingTokenId = $trackingTokenId;
        return $this;
    }

    public function setCreatedTimestamp(string $createdTimestamp): self
    {
        $this->createdTimestamp = $createdTimestamp;
        return $this;
    }

    public function setUpdatedTimestamp(string $updatedTimestamp): self
    {
        $this->updatedTimestamp = $updatedTimestamp;
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getError(): string
    {
        return $this->error;
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

    public function getTrackingTokenId(): ?string
    {
        return $this->trackingTokenId;
    }

    public function clearTackingTokenId(): self
    {
        $this->trackingTokenId = '';
        return $this;
    }
}

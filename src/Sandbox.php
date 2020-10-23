<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use Keboola\Sandboxes\Api\Exception\InvalidApiResponseException;

class Sandbox
{
    public const DEFAULT_EXPIRATION_DAYS = 7;

    private string $id;
    private string $projectId;
    private ?string $tokenId = null;
    private ?string $configurationId = null;
    private ?string $physicalId = null;

    private string $type;
    private string $size = 'small';

    private string $user;
    private ?string $password = null;
    private ?string $host = null;
    private ?string $url = null;

    private ?string $imageVersion = null;
    private ?string $stagingWorkspaceId = null;
    private ?string $stagingWorkspaceType = null;
    private ?array $workspaceDetails = [];
    private ?string $autosaveTokenId = null;
    private ?array $packages = [];

    private ?bool $active = null;
    private ?string $createdTimestamp = null;
    private ?string $updatedTimestamp = null;
    private ?string $expirationTimestamp = null;
    private ?string $lastAutosaveTimestamp = null;
    private ?int $expirationAfterHours = null;
    private ?string $deletedTimestamp = null;


    public static function fromArray(array $in): self
    {
        $sandbox = new Sandbox();
        if (!isset($in['id'])) {
            throw new InvalidApiResponseException('Property id is missing from API response');
        }
        $sandbox->setId((string) $in['id']);
        if (!isset($in['projectId'])) {
            throw new InvalidApiResponseException('Property projectId is missing from API response');
        }
        $sandbox->setProjectId((string) $in['projectId']);
        if (!isset($in['tokenId'])) {
            throw new InvalidApiResponseException('Property tokenId is missing from API response');
        }
        $sandbox->setTokenId((string) $in['tokenId']);
        if (!isset($in['configurationId'])) {
            throw new InvalidApiResponseException('Property configurationId is missing from API response');
        }
        $sandbox->setConfigurationId((string) $in['configurationId']);
        if (!isset($in['type'])) {
            throw new InvalidApiResponseException('Property type is missing from API response');
        }
        $sandbox->setType($in['type']);
        if (!isset($in['active'])) {
            throw new InvalidApiResponseException('Property active is missing from API response');
        }
        $sandbox->setActive($in['active'] ?? false);

        $sandbox->setPhysicalId($in['physicalId'] ?? '');
        $sandbox->setSize($in['size'] ?? '');
        $sandbox->setUser($in['user'] ?? '');
        $sandbox->setPassword($in['password'] ?? '');
        $sandbox->setHost($in['host'] ?? '');
        $sandbox->setUrl($in['url'] ?? '');
        $sandbox->setImageVersion($in['imageVersion'] ?? '');
        $sandbox->setStagingWorkspaceId(isset($in['stagingWorkspaceId']) ? (string) $in['stagingWorkspaceId'] : '');
        $sandbox->setStagingWorkspaceType($in['stagingWorkspaceType'] ?? '');
        $sandbox->setWorkspaceDetails($in['workspaceDetails'] ?? []);
        $sandbox->setAutosaveTokenId(isset($in['autosaveTokenId']) ? (string) $in['autosaveTokenId'] : '');
        $sandbox->setPackages($in['packages'] ?? []);
        $sandbox->setCreatedTimestamp($in['createdTimestamp'] ?? '');
        $sandbox->setUpdatedTimestamp($in['updatedTimestamp'] ?? '');
        $sandbox->setExpirationTimestamp($in['expirationTimestamp'] ?? '');
        $sandbox->setLastAutosaveTimestamp($in['lastAutosaveTimestamp'] ?? '');
        $sandbox->setExpirationAfterHours($in['expirationAfterHours'] ?? 0);
        $sandbox->setDeletedTimestamp($in['deletedTimestamp'] ?? '');

        return $sandbox;
    }

    public function toArray(): array
    {
        $result = [];
        if (!empty($this->id)) {
            $result['id'] = $this->id;
        }
        if (!empty($this->configurationId)) {
            $result['configurationId'] = $this->configurationId;
        }
        if (!empty($this->physicalId)) {
            $result['physicalId'] = $this->physicalId;
        }

        if (!empty($this->type)) {
            $result['type'] = $this->type;
        }
        if (!empty($this->size)) {
            $result['size'] = $this->size;
        }

        if (!empty($this->user)) {
            $result['user'] = $this->user;
        }
        if (!empty($this->password)) {
            $result['password'] = $this->password;
        }
        if (!empty($this->host)) {
            $result['host'] = $this->host;
        }
        if (!empty($this->url)) {
            $result['url'] = $this->url;
        }

        if (!empty($this->imageVersion)) {
            $result['imageVersion'] = $this->imageVersion;
        }
        if (!empty($this->stagingWorkspaceId)) {
            $result['stagingWorkspaceId'] = $this->stagingWorkspaceId;
        }
        if (!empty($this->stagingWorkspaceType)) {
            $result['stagingWorkspaceType'] = $this->stagingWorkspaceType;
        }
        if (!empty($this->workspaceDetails)) {
            $result['workspaceDetails'] = $this->workspaceDetails;
        }
        if (!empty($this->autosaveTokenId)) {
            $result['autosaveTokenId'] = $this->autosaveTokenId;
        }
        if (!empty($this->packages)) {
            $result['packages'] = $this->packages;
        }

        if (!empty($this->createdTimestamp)) {
            $result['createdTimestamp'] = $this->createdTimestamp;
        }
        if (!empty($this->updatedTimestamp)) {
            $result['updatedTimestamp'] = $this->updatedTimestamp;
        }
        if (!empty($this->expirationTimestamp)) {
            $result['expirationTimestamp'] = $this->expirationTimestamp;
        }
        if (!empty($this->expirationAfterHours)) {
            $result['expirationAfterHours'] = $this->expirationAfterHours;
        }

        if ($this->active !== null) {
            $result['active'] = $this->active;
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

    public function setProjectId(string $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setConfigurationId(string $configurationId): self
    {
        $this->configurationId = $configurationId;
        return $this;
    }

    public function getConfigurationId(): ?string
    {
        return $this->user;
    }

    /**
     * @param string|int $physicalId
     * @return $this
     */
    public function setPhysicalId($physicalId): self
    {
        $this->physicalId = (string) $physicalId;
        return $this;
    }

    public function getPhysicalId(): ?string
    {
        return $this->physicalId;
    }

    public function setImageVersion(string $imageVersion): self
    {
        $this->imageVersion = $imageVersion;
        return $this;
    }

    public function getImageVersion(): ?string
    {
        return $this->imageVersion;
    }

    public function setPackages(array $packages): self
    {
        $this->packages = $packages;
        return $this;
    }

    public function getPackages(): ?array
    {
        return $this->packages;
    }

    public function setCreatedTimestamp(string $createdTimestamp): self
    {
        $this->createdTimestamp = $createdTimestamp;
        return $this;
    }

    public function getCreatedTimestamp(): ?string
    {
        return $this->createdTimestamp;
    }

    public function setUpdatedTimestamp(string $updatedTimestamp): self
    {
        $this->updatedTimestamp = $updatedTimestamp;
        return $this;
    }

    public function getUpdatedTimestamp(): ?string
    {
        return $this->updatedTimestamp;
    }

    public function setExpirationTimestamp(string $expirationTimestamp): self
    {
        $this->expirationTimestamp = $expirationTimestamp;
        return $this;
    }

    public function getExpirationTimestamp(): ?string
    {
        return $this->expirationTimestamp;
    }

    public function setExpirationAfterHours(int $expirationAfterHours): self
    {
        $this->expirationAfterHours = $expirationAfterHours;
        return $this;
    }

    public function getExpirationAfterHours(): ?int
    {
        return $this->expirationAfterHours;
    }

    public function setDeletedTimestamp(string $deletedTimestamp): self
    {
        $this->deletedTimestamp = $deletedTimestamp;
        return $this;
    }

    public function getDeletedTimestamp(): ?string
    {
        return $this->deletedTimestamp;
    }

    public function setLastAutosaveTimestamp(string $lastAutosaveTimestamp): self
    {
        $this->lastAutosaveTimestamp = $lastAutosaveTimestamp;
        return $this;
    }

    public function getStagingWorkspaceId(): ?string
    {
        return $this->stagingWorkspaceId;
    }

    public function setStagingWorkspaceId(string $stagingWorkspaceId): self
    {
        $this->stagingWorkspaceId = $stagingWorkspaceId;
        return $this;
    }

    public function getStagingWorkspaceType(): ?string
    {
        return $this->stagingWorkspaceType;
    }

    public function setStagingWorkspaceType(string $stagingWorkspaceType): self
    {
        $this->stagingWorkspaceType = $stagingWorkspaceType;
        return $this;
    }

    public function getWorkspaceDetails(): ?array
    {
        return $this->workspaceDetails;
    }

    public function setWorkspaceDetails(array $workspaceDetails): self
    {
        $this->workspaceDetails = $workspaceDetails;
        return $this;
    }

    public function getLastAutosaveTimestamp(): ?string
    {
        return $this->lastAutosaveTimestamp;
    }

    public function setAutosaveTokenId(string $autosaveTokenId): self
    {
        $this->autosaveTokenId = $autosaveTokenId;
        return $this;
    }

    public function getAutosaveTokenId(): ?string
    {
        return $this->autosaveTokenId;
    }

    public function setTokenId(string $tokenId): self
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    public function getTokenId(): ?string
    {
        return $this->tokenId;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setSize(string $size): self
    {
        if (!in_array($size, ['small', 'medium', 'large'])) {
            throw new Exception('Unsupported size, use small, medium or large');
        }
        $this->size = $size;
        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }
}

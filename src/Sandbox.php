<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

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
    private bool $mlflow = false;
    private ?string $autosaveTokenId = null;

    private ?bool $active = null;
    private ?string $createdTimestamp = null;
    private ?string $updatedTimestamp = null;
    private ?string $expirationTimestamp = null;
    private ?string $lastAutosaveTimestamp = null;
    private ?int $expirationAfterHours = null;
    private ?string $deletedTimestamp = null;

    public function __construct(?array $sandbox = null)
    {
        if (!empty($sandbox['id'])) {
            $this->setId((string) $sandbox['id']);
        }
        if (!empty($sandbox['projectId'])) {
            $this->setProjectId((string) $sandbox['projectId']);
        }
        if (!empty($sandbox['tokenId'])) {
            $this->setTokenId((string) $sandbox['tokenId']);
        }
        if (!empty($sandbox['configurationId'])) {
            $this->setConfigurationId((string) $sandbox['configurationId']);
        }
        if (!empty($sandbox['physicalId'])) {
            $this->setPhysicalId($sandbox['physicalId']);
        }

        if (!empty($sandbox['type'])) {
            $this->setType($sandbox['type']);
        }
        if (!empty($sandbox['size'])) {
            $this->setSize($sandbox['size']);
        }

        if (!empty($sandbox['user'])) {
            $this->setUser($sandbox['user']);
        }
        if (!empty($sandbox['password'])) {
            $this->setPassword($sandbox['password']);
        }
        if (!empty($sandbox['host'])) {
            $this->setHost($sandbox['host']);
        }
        if (!empty($sandbox['url'])) {
            $this->setUrl($sandbox['url']);
        }

        if (!empty($sandbox['imageVersion'])) {
            $this->setImageVersion($sandbox['imageVersion']);
        }
        if (!empty($sandbox['mlflow'])) {
            $this->setMlflow($sandbox['mlflow']);
        }
        if (!empty($sandbox['autosaveTokenId'])) {
            $this->setAutosaveTokenId((string) $sandbox['autosaveTokenId']);
        }

        if (isset($sandbox['active'])) {
            $this->setActive($sandbox['active'] ?? false);
        }
        if (!empty($sandbox['createdTimestamp'])) {
            $this->setCreatedTimestamp($sandbox['createdTimestamp']);
        }
        if (!empty($sandbox['updatedTimestamp'])) {
            $this->setUpdatedTimestamp($sandbox['updatedTimestamp']);
        }
        if (!empty($sandbox['expirationTimestamp'])) {
            $this->setExpirationTimestamp($sandbox['expirationTimestamp']);
        }
        if (!empty($sandbox['lastAutosaveTimestamp'])) {
            $this->setLastAutosaveTimestamp($sandbox['lastAutosaveTimestamp']);
        }
        if (!empty($sandbox['expirationAfterHours'])) {
            $this->setExpirationAfterHours($sandbox['expirationAfterHours']);
        }
        if (!empty($sandbox['deletedTimestamp'])) {
            $this->setDeletedTimestamp($sandbox['deletedTimestamp']);
        }
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
        if (!empty($this->mlflow)) {
            $result['mlflow'] = $this->mlflow;
        }
        if (!empty($this->autosaveTokenId)) {
            $result['autosaveTokenId'] = $this->autosaveTokenId;
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

    public function setMlflow(bool $mlflow): self
    {
        $this->mlflow = $mlflow;
        return $this;
    }

    public function getMlflow(): bool
    {
        return $this->mlflow;
    }

    public function setSize(string $size): self
    {
        if (!in_array($size, ['small', 'medium', 'large'])) {
            throw new Exception('Unsupported size, use small, medium or large');
        }
        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public static function createPassword(int $length = 16): string
    {
        $chars = array('abcdefghijkmnopqrstuvwxyz', 'ABCDEFGHIJKMNOPQRSTUVWXYZ', '0234567890234567890234567');
        srand((int) microtime() * 1000000);
        $i = 0;
        $pass = '';
        while ($i <= $length - 1) {
            $num = rand() % 25;
            $tmp = substr($chars[$i % 3], $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }
}

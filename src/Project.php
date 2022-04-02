<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class Project
{
    private string $id;
    private ?string $mlflowUri = '';
    private ?string $mlflowAbsSas = '';
    private ?string $mlflowAbsConnectionString = '';
    private ?string $mlflowServerVersion = '';
    private string $mlflowServerVersionLatest = '';
    private ?PersistentStorageReady $persistentStorageReady = null;

    private string $createdTimestamp;
    private string $updatedTimestamp;

    public static function fromArray(array $in): self
    {
        $project = new self();
        $project->id = (string) $in['id'];
        $project->mlflowUri = $in['mlflowUri'] ?? '';
        $project->mlflowAbsSas = $in['mlflowAbsSas'] ?? '';
        $project->mlflowAbsConnectionString = $in['mlflowAbsConnectionString'] ?? '';
        $project->createdTimestamp = $in['createdTimestamp'];
        $project->updatedTimestamp = $in['updatedTimestamp'] ?? '';
        $project->mlflowServerVersion = $in['mlflowServerVersion'] ?? '';
        $project->mlflowServerVersionLatest = $in['mlflowServerVersionLatest'] ?? '';
        $project->persistentStorageReady = array_key_exists('persistentStorageReady', $in)
            ? new PersistentStorageReady($in['persistentStorageReady'])
            : null;

        return $project;
    }

    public function toArray(): array
    {
        $result = [];
        if (!empty($this->id)) {
            $result['id'] = $this->id;
        }
        if ($this->mlflowUri !== '') {
            $result['mlflowUri'] = $this->mlflowUri;
        }
        if ($this->mlflowAbsSas !== '') {
            $result['mlflowAbsSas'] = $this->mlflowAbsSas;
        }
        if ($this->mlflowAbsSas !== '') {
            $result['mlflowAbsConnectionString'] = $this->mlflowAbsConnectionString;
        }

        if ($this->mlflowServerVersion !== '') {
            $result['mlflowServerVersion'] = $this->mlflowServerVersion;
        }

        $result['mlflowServerVersionLatest'] = $this->mlflowServerVersionLatest;

        if ($this->persistentStorageReady !== null) {
            $result['persistentStorageReady'] = $this->persistentStorageReady->toBool();
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
        unset($array['mlflowAbsConnectionString']);
        unset($array['mlflowServerVersionLatest']);
        return $array;
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

    public function setMlflowUri(?string $mlflowUri): self
    {
        $this->mlflowUri = $mlflowUri;
        return $this;
    }

    public function getMlflowUri(): ?string
    {
        return $this->mlflowUri;
    }

    public function setMlflowAbsSas(?string $mlflowAbsSas): self
    {
        $this->mlflowAbsSas = $mlflowAbsSas;
        return $this;
    }

    public function getMlflowAbsSas(): ?string
    {
        return $this->mlflowAbsSas;
    }

    public function getMlflowAbsConnectionString(): ?string
    {
        return $this->mlflowAbsConnectionString;
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

    public function setMlflowServerVersion(?string $serverVersion): self
    {
        $this->mlflowServerVersion = $serverVersion;
        return $this;
    }

    public function getMlflowServerVersion(): ?string
    {
        return $this->mlflowServerVersion;
    }

    public function getMlflowServerVersionLatest(): ?string
    {
        return $this->mlflowServerVersionLatest;
    }

    public function setPersistentStorageReady(PersistentStorageReady $persistentStorageReady): self
    {
        $this->persistentStorageReady = $persistentStorageReady;
        return $this;
    }

    public function getPersistentStorageReady(): ?PersistentStorageReady
    {
        return $this->persistentStorageReady;
    }
}

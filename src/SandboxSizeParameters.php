<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class SandboxSizeParameters
{
    private ?int $storageSize_GB;

    public function __construct(?int $storageSize_GB = null)
    {
        $this->storageSize_GB = $storageSize_GB;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['storageSize_GB'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->storageSize_GB !== null) {
            $data['storageSize_GB'] = $this->storageSize_GB;
        }

        return $data;
    }

    public function getStorageSizeGB(): ?int
    {
        return $this->storageSize_GB;
    }

    public function setStorageSizeGB(?int $storageSize_GB): void
    {
        $this->storageSize_GB = $storageSize_GB;
    }
}

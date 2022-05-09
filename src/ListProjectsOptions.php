<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class ListProjectsOptions
{
    private ?bool $hasPersistentStorage = null;

    public static function create(): self
    {
        return new self();
    }

    public function toQueryParameters(): array
    {
        $query = [];

        if ($this->hasPersistentStorage !== null) {
            $query['hasPersistentStorage'] = $this->hasPersistentStorage;
        }

        return $query;
    }

    public function setHasPersistentStorage(bool $hasPersistentStorage): self
    {
        $this->hasPersistentStorage = $hasPersistentStorage;
        return $this;
    }

    public function clearHasPersistentStorage(): self
    {
        $this->hasPersistentStorage = null;
        return $this;
    }

    public function getHasPersistentStorage(): ?bool
    {
        return $this->hasPersistentStorage;
    }
}

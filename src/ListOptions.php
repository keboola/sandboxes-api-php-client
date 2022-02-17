<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class ListOptions
{
    private ?string $branchId = null;

    public static function create(): self
    {
        return new self();
    }

    public function getBranchId(): ?string
    {
        return $this->branchId;
    }

    public function setBranchId(?string $branchId): self
    {
        $this->branchId = $branchId;
        return $this;
    }

    public function export(): array
    {
        $data = [];

        if ($this->branchId !== null) {
            $data['branchId'] = $this->branchId;
        }

        return $data;
    }
}

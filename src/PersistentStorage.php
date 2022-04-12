<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class PersistentStorage
{
    private ?bool $ready = null;

    public function __construct()
    {
    }

    public static function fromArray(array $values): self
    {
        return (new self())
            ->setReady($values['ready'] ?? null);
    }

    public function setReady(?bool $value): self
    {
        $this->ready = $value;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'ready' => $this->ready,
        ];
    }

    public function isReady(): ?bool
    {
        return $this->ready;
    }
}

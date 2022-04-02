<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class PersistentStorageReady
{
    private ?bool $value;

    public function __construct(?bool $value = null)
    {
        $this->value = $value;
    }

    public static function fromBool(?bool $bool): self
    {
        return new self($bool);
    }

    public function toBool(): ?bool
    {
        return $this->value;
    }
}

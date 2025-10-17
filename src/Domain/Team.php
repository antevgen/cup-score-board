<?php

declare(strict_types=1);

namespace App\Domain;

readonly class Team
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}

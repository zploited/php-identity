<?php

namespace Zploited\Identity\Client\Models;

class RefreshToken implements TokenInterface
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function __toString(): string
    {
        return $this->token;
    }
}
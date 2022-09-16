<?php

namespace Zploited\Identity\Client\Interfaces;

interface TokenInterface
{
    public function getJwtToken();
    public function getHeader(string $header);
}
<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Index
{
    protected function index()
    {
        $response = $this->get($this->resource);

        return json_decode($response->getBody()->getContents());
    }
}
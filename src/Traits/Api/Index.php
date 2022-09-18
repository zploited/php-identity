<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Index
{
    /**
     * Gets a list of all stored entities.
     *
     * @return object
     */
    public function index(): object
    {
        $response = $this->get($this->resource);

        return json_decode($response->getBody()->getContents());
    }
}
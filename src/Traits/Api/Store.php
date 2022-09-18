<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Store
{
    /**
     * Creates a new entity.
     *
     * @param array $params The data being saved to the new entity.
     * @return void
     */
    public function store(array $params): void
    {
        $this->post($this->resource, $params);
    }
}
<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Store
{
    /**
     * Creates a new entity.
     *
     * @param array $params The data being saved to the new entity.
     * @param callable|null $callback
     * @return void
     */
    public function store(array $params, callable $callback = null): void
    {
        $this->post($this->resource, $params, $callback);
    }
}
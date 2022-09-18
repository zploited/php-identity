<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Show
{
    /**
     * Gets a specific entity.
     *
     * @param $id
     * @return object
     */
    public function show($id): object
    {
        return $this->get($this->resource.'/'.$id);
    }
}
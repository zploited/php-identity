<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Update
{
    /**
     * Updates a current entity with new data.
     *
     * @param mixed $id resource identifier
     * @param array $data data being updated
     * @return void
     */
    public function update($id, array $data): void
    {
        $this->patch($this->resource.'/'.$id);
    }
}
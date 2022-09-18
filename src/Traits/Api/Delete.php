<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Delete
{
    /**
     * Deletes a specific entity
     * @param mixed $id
     * @return void
     */
    public function delete($id): void
    {
        $this->delete($this->resource.'/'.$id);
    }
}
<?php

namespace Zploited\Identity\Client\Traits\Api;

trait Destroy
{
    /**
     * Deletes a specific entity
     * @param mixed $id
     * @return void
     */
    public function destroy($id): void
    {
        $this->delete($this->resource.'/'.$id);
    }
}
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
        $response = $this->get($this->resource.'/'.$id);

        return json_decode($response->getBody()->getContents());
    }
}
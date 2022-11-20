<?php

namespace Zploited\Identity\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ResourceApiClient extends ApiEndpoint
{
    public function __construct(Client $client, string $resource)
    {
        parent::__construct($client, $resource);
    }

    /**
     * Gets a list of all stored entities.
     *
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    public function index(callable $callback = null): void
    {
        $this->get($this->resource, $callback);
    }

    /**
     * Gets a specific entity.
     *
     * @param $id
     * @param callable $callback
     * @return void
     * @throws GuzzleException
     */
    public function show($id, callable $callback): void
    {
        $this->get($this->resource.'/'.$id, $callback);
    }

    /**
     * Creates a new entity.
     *
     * @param array $params The data being saved to the new entity.
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    public function store(array $params, callable $callback = null): void
    {
        $this->post($this->resource, $params, $callback);
    }

    /**
     * Updates a current entity with new data.
     *
     * @param mixed $id resource identifier
     * @param array $data data being updated
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    public function update($id, array $data, callable $callback = null): void
    {
        $this->patch($this->resource.'/'.$id, $data, $callback);
    }

    /**
     * Deletes a specific entity.
     *
     * @param mixed $id
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    public function destroy($id, callable $callback = null): void
    {
        $this->delete($this->resource.'/'.$id, $callback);
    }
}
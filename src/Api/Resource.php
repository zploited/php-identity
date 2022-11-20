<?php

namespace Zploited\Identity\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Resource
{
    protected Client $client;
    protected string $resource;

    public function __construct(Client $client, string $resource)
    {
        $this->client = $client;
        $this->resource = $resource;
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

    /**
     * Requests api path using a GET request.
     *
     * @param string $path
     * @param callable|null $callback
     * @return mixed
     * @throws GuzzleException
     */
    protected function get(string $path, callable $callback = null): void
    {
        $response =  $this->client->get($path);

        if($callback !== null) {
            $callback(
                json_decode($response->getBody()->getContents()),
                $response
            );
        }
    }

    /**
     * Requests api path using a POST request.
     *
     * @param string $path
     * @param array $data
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    protected function post(string $path, array $data, callable $callback = null): void
    {
        $response = $this->client->post($path, ['form_params' => $data]);

        if($callback !== null) {
            $callback($response);
        }
    }

    /**
     * Requests api path using a DELETE request.
     *
     * @param string $path
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    protected function delete(string $path, callable $callback = null): void
    {
        $response = $this->client->delete($path);

        if($callback !== null) {
            $callback($response);
        }
    }

    /**
     * Requests api path using a PATCH request.
     *
     * @param string $path
     * @param array $data
     * @param callable|null $callback
     * @return void
     * @throws GuzzleException
     */
    protected function patch(string $path, array $data, callable $callback = null): void
    {
        $response = $this->client->patch($path, ['form_params' => $data]);

        if($callback !== null) {
            $callback($response);
        }
    }
}
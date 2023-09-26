<?php

namespace Zploited\Identity\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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
     * @return mixed|null Return value of provided callback.
     * @throws GuzzleException
     */
    public function index(callable $callback = null): mixed
    {
        return $this->get($this->resource, $callback);
    }

    /**
     * Gets a specific entity.
     *
     * @param $id
     * @param callable $callback
     * @return mixed|null Return value of provided callback.
     * @throws GuzzleException
     */
    public function show($id, callable $callback): mixed
    {
        return $this->get($this->resource.'/'.$id, $callback);
    }

    /**
     * Creates a new entity.
     *
     * @param array $params The data being saved to the new entity.
     * @param callable|null $callback
     * @return mixed|null Return value of provided callback
     * @throws GuzzleException
     */
    public function store(array $params, callable $callback = null): mixed
    {
        return $this->post($this->resource, $params, $callback);
    }

    /**
     * Updates a current entity with new data.
     *
     * @param mixed $id resource identifier
     * @param array $data data being updated
     * @param callable|null $callback
     * @return mixed|null Return value of provided callback
     * @throws GuzzleException
     */
    public function update($id, array $data, callable $callback = null): mixed
    {
        return $this->patch($this->resource.'/'.$id, $data, $callback);
    }

    /**
     * Deletes a specific entity.
     *
     * @param mixed $id
     * @param callable|null $callback
     * @return mixed|null Return value of provided callback
     * @throws GuzzleException
     */
    public function destroy($id, callable $callback = null): mixed
    {
        return $this->delete($this->resource.'/'.$id, $callback);
    }

    /**
     * Requests api path using a GET request, and returns the return parameter of the provided callable.
     *
     * @param string $path
     * @param callable|null $callback
     * @return mixed|null
     * @throws GuzzleException
     */
    protected function get(string $path, callable $callback = null): mixed
    {
        $response =  $this->client->get($path);

        return $this->returnCallbackWithResponse($response, $callback);
    }

    /**
     * Requests api path using a POST request.
     *
     * @param string $path
     * @param array $data
     * @param callable|null $callback
     * @return mixed|null
     * @throws GuzzleException
     */
    protected function post(string $path, array $data, callable $callback = null): mixed
    {
        $response = $this->client->post($path, ['form_params' => $data]);

        return $this->returnCallbackWithResponse($response, $callback);
    }

    /**
     * Requests api path using a DELETE request.
     *
     * @param string $path
     * @param callable|null $callback
     * @return mixed|null
     * @throws GuzzleException
     */
    protected function delete(string $path, callable $callback = null): mixed
    {
        $response = $this->client->delete($path);

        return $this->returnCallbackWithResponse($response, $callback);
    }

    /**
     * Requests api path using a PATCH request.
     *
     * @param string $path
     * @param array $data
     * @param callable|null $callback
     * @return mixed|null
     * @throws GuzzleException
     */
    protected function patch(string $path, array $data, callable $callback = null): mixed
    {
        $response = $this->client->patch($path, ['form_params' => $data]);

        return $this->returnCallbackWithResponse($response, $callback);
    }

    /**
     * Returns whatever the callback returns, providing the json object of the response, and the raw response as parameters.
     *
     * @param ResponseInterface $response
     * @param callable|null $callback
     * @return mixed|null
     */
    protected function returnCallbackWithResponse(ResponseInterface $response, callable $callback = null): mixed
    {
        return $callback !== null ?
            $callback(json_decode($response->getBody()->getContents()), $response) :
            null;
    }
}
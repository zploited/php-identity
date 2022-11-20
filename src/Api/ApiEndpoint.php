<?php

namespace Zploited\Identity\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class ApiEndpoint
{
    protected Client $client;
    protected string $resource;

    /**
     * Class constructor.
     *
     * @param Client $client
     * @param string $resource
     */
    public function __construct(Client $client, string $resource)
    {
        $this->client = $client;
        $this->resource = $resource;
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
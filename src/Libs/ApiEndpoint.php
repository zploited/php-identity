<?php

namespace Zploited\Identity\Client\Libs;

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
     * @return mixed
     * @throws GuzzleException
     */
    protected function get(string $path)
    {
        $response =  $this->client->get($path);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Requests api path using a POST request.
     *
     * @param string $path
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    protected function post(string $path, array $data): void
    {
        $this->client->post($path, ['form_params' => $data]);
    }

    /**
     * Requests api path using a DELETE request.
     *
     * @param string $path
     * @return void
     * @throws GuzzleException
     */
    protected function delete(string $path): void
    {
        $this->client->delete($path);
    }

    /**
     * Requests api path using a PATCH request.
     *
     * @param string $path
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    protected function patch(string $path, array $data)
    {
        $this->client->patch($path, ['form_params' => $data]);
    }
}
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
    }

    /**
     * Requests api path using a GET request.
     *
     * @param string $path
     * @return mixed
     * @throws GuzzleException
     */
    public function get(string $path)
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
    public function post(string $path, array $data): void
    {
        $this->client->post($path, $data);
    }

    /**
     * Requests api path using a DELETE request.
     *
     * @param string $path
     * @return void
     * @throws GuzzleException
     */
    public function delete(string $path): void
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
    public function patch(string $path, array $data)
    {
        $this->client->patch($path, $data);
    }
}
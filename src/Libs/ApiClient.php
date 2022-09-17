<?php

namespace Zploited\Identity\Client\Libs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Zploited\Identity\Client\Interfaces\TokenInterface;

class ApiClient
{
    protected Client $client;

    /**
     * Class constructor
     *
     * @param string $identifier
     * @param TokenInterface $token
     */
    public function __construct(string $identifier, TokenInterface $token)
    {
        $version = require(__DIR__.'/../version.php');

        /*
         * Prepares the Guzzle client with the required headers and the base uri.
         */
        $this->client = new Client([
            'base_uri' => 'https://'.$identifier.'/api/v1',
            'headers' => [
                'accept' => 'application/json',
                'cache-control' => 'no-store',
                'authorization' => 'bearer '.$token->getJwtString(),
                'user-agent' => 'identity/'.$version
            ]
        ]);
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
<?php

namespace Zploited\Identity\Client\Api;

use GuzzleHttp\Client;
use Zploited\Identity\Client\Models\AccessToken;

class ApiClient
{
    protected Client $client;

    /**
     * Class constructor
     *
     * @param string $baseUrl
     * @param AccessToken $token
     */
    public function __construct(string $baseUrl, AccessToken $token)
    {
        $version = require(__DIR__.'/../version.php');

        /*
         * Prepares the Guzzle client with the required headers and the base uri.
         */
        $this->client = new Client([
            'base_uri' => $baseUrl .'/api/v1/',
            'http_errors' => false,
            'headers' => [
                'accept' => 'application/json',
                'cache-control' => 'no-store',
                'authorization' => 'Bearer '.(string)$token,
                'user-agent' => 'identity/'.$version
            ]
        ]);
    }

    /**
     * Gets api client for users resource.
     * @return Resource
     */
    public function users(): Resource
    {
        return $this->resource('users');
    }

    /**
     * Gets api client for a custom resource.
     * @param string $resource
     * @return Resource
     */
    public function resource(string $resource): Resource
    {
        return new Resource($this->client, $resource);
    }

    /**
     * Gets the configured client raw Guzzle client.
     *
     * @return Client
     */
    public function client(): Client
    {
        return $this->client;
    }
}
<?php

namespace Zploited\Identity\Client\Libs;

use GuzzleHttp\Client;
use Zploited\Identity\Client\Models\AccessToken;
use Zploited\Identity\Client\Traits\Api\Destroy;
use Zploited\Identity\Client\Traits\Api\Index;
use Zploited\Identity\Client\Traits\Api\Show;
use Zploited\Identity\Client\Traits\Api\Store;
use Zploited\Identity\Client\Traits\Api\Update;

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
            'headers' => [
                'accept' => 'application/json',
                'cache-control' => 'no-store',
                'authorization' => 'Bearer '.(string)$token,
                'user-agent' => 'identity/'.$version
            ]
        ]);
    }

    public function users(): object
    {
        /**
         * Handles user specific api resources
         */
        return new class($this->client, 'users') extends ApiEndpoint
        {
            use Index, Show, Store, Update, Destroy;

            public function __construct(Client $client, string $resource)
            {
                parent::__construct($client, $resource);
            }
        };
    }

    public function endpoint(string $resource): object
    {
        /**
         * handles a custom defined api endpoint
         */
        return new class($this->client, $resource) extends ApiEndpoint
        {
            use Index, Show, Store, Update, Destroy;

            public function __construct(Client $client, string $resource)
            {
                parent::__construct($client, $resource);
            }
        };
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
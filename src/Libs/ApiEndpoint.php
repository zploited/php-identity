<?php

namespace Zploited\Identity\Client\Libs;

use GuzzleHttp\Client;

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
}
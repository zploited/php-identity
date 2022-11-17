<?php

namespace Zploited\Identity\Client\Tests\Unit;

use Zploited\Identity\Client\Exceptions\IdentityArgumentException;
use Zploited\Identity\Client\Identity;
use Zploited\Identity\Client\Tests\TestCase;

class IdentityTest extends TestCase
{
    protected Identity $client;

    /**
     * @throws IdentityArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Identity([
            'identifier' => 'tenant.domain.tld',
            'client_id' => '123456',
            'redirect_uri' => 'https://domain.tld/callback',
            'scopes' => [],
            'state' => 'abcdef'
        ]);
    }

    public function testGetAuthorizationPath()
    {
        $this->assertEquals(
            'https://tenant.domain.tld/oauth/authorize',
            $this->client->getAuthorizationPath()
        );
    }

    /**
     * @runInSeparateProcess
     * @return void
     */
    public function testGetAuthorizationUrl()
    {
        /*
         * Running the method first, because it needs to activate sessions and set the variable
         * before we use it in the expected string.
         */
        $url = $this->client->getAuthorizationUrl();

        $this->assertEquals(
            'https://tenant.domain.tld/oauth/authorize?response_type=code&client_id=123456&redirect_uri=https%3A%2F%2Fdomain.tld%2Fcallback&scope=&state=abcdef',
            $url
        );
    }
}
<?php

namespace Zploited\Identity\Client\Tests\Unit;

use Zploited\Identity\Client\Exceptions\IdentityArgumentException;
use Zploited\Identity\Client\Tests\TestCase;
use Zploited\Identity\Client\TokenResponse;

class TokenResponseTest extends TestCase
{
    protected TokenResponse $response;
    protected $at = "eyJhbGciOiJIUzI1NiIsInR5cCI6ImF0K2p3dCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.M76x1clCZHjidPb8rApGOK5ibhu1wuvZDPEELQLJujg";

    public function testAccessToken()
    {
        $this->assertEquals($this->at, (string)$this->response->accessToken());
    }

    public function testExpiresAt()
    {
        $this->assertEquals(
            (new \DateTime())->add(new \DateInterval('PT3600S'))->getTimestamp(), // now + 3600 seconds (1 hour)
            $this->response->expiresAt()->getTimestamp()
        );
    }

    public function testExpiresIn()
    {
        $seconds = $this->response->expiresIn();

        $this->assertIsInt($seconds);
        $this->assertEquals(3600, $this->response->expiresIn());
    }

    public function testGetRefreshToken()
    {
        $this->assertEquals('testrefresh', (string)$this->response->refreshToken());
    }

    /**
     * @throws IdentityArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->response = new TokenResponse(
            $this->at,
            3600,
            'testrefresh'
        );
    }
}
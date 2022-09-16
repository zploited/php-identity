<?php

namespace Zploited\Identity\Client\Tests\Unit;

use Zploited\Identity\Client\Tests\TestCase;
use Zploited\Identity\Client\TokenResponse;

class TokenResponseTest extends TestCase
{
    protected TokenResponse $token;

    public function testGetToken()
    {
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q', $this->token->getAccessToken());
    }

    public function testExpiresAt()
    {
        $this->assertEquals(
            (new \DateTime())->add(new \DateInterval('PT3600S'))->getTimestamp(), // now + 3600 seconds (1 hour)
            $this->token->expiresAt()->getTimestamp()
        );
    }

    public function testExpiresIn()
    {
        $seconds = $this->token->expiresIn();

        $this->assertIsInt($seconds);
        $this->assertEquals(3600, $this->token->expiresIn());
    }

    public function testGetRefreshToken()
    {
        $this->assertEquals('testrefresh', $this->token->getRefreshToken());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = new TokenResponse(
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q',
            3600,
            'testrefresh'
        );
    }
}
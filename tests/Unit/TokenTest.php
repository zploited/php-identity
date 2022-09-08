<?php

namespace Unit;

use Zploited\Identity\Client\Tests\TestCase;
use Zploited\Identity\Client\Token;

class TokenTest extends TestCase
{
    protected Token $token;

    public function testGetToken()
    {
        $this->assertEquals('testtoken', $this->token->getAccessToken());
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

        $this->token = new Token(
            'testtoken',
            3600,
            'testrefresh'
        );
    }
}
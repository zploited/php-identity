<?php

namespace Unit;

use Zploited\Identity\Client\Token;
use Zploited\Identity\Client\Tests\TestCase;

class TokenTest extends TestCase
{
    protected Token $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new Token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q');
    }

    public function testCanAccessSubject()
    {
        $this->assertEquals(1234567890, $this->handler->sub);
    }

    public function testUnknownClaimReturnsNull()
    {
        $this->assertNull($this->handler->something);
    }
}
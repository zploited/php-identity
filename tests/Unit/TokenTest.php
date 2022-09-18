<?php

namespace Zploited\Identity\Client\Tests\Unit;

use Zploited\Identity\Client\Token;
use Zploited\Identity\Client\Tests\TestCase;

class TokenTest extends TestCase
{
    protected Token $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new Token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaWF0IjoxNjYzNTEwNzc4LCJleHAiOjE2NjM1MTA3Nzl9.iPbZ5NQWKkkhmr4oFKs81-Qpu5Wo0WaV-npDvq1LTeg');
    }

    public function testCanAccessSubject()
    {
        $this->assertEquals(1234567890, $this->handler->sub);
    }

    public function testUnknownClaimReturnsNull()
    {
        $this->assertNull($this->handler->something);
    }

    public function testIsExpired()
    {
        $this->assertTrue($this->handler->isExpired());
    }
}
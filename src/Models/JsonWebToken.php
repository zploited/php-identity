<?php

namespace Zploited\Identity\Client\Models;

use DateTimeImmutable;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;

/**
 * Represents a JWT object, based off a stringified token;
 */
abstract class JsonWebToken implements TokenInterface
{
    private string $token;

    public string $sub;
    public array $audience;
    public string $iss;
    public DateTimeImmutable $iat;
    public DateTimeImmutable $exp;

    /**
     * Class constructor.
     * Extracts token claims into properties.
     * @param string $token
     * @return Token
     */
    public function __construct(string $token)
    {
        $parser = new Parser(new JoseEncoder());
        $parsed = $parser->parse($token);
        $this->token = $token;

        foreach($parsed->claims()->all() as $claim => $value) {
            $this->$claim = $value;
        }

        return $parsed;
    }

    /**
     * Serializes the object back to a token string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * Checks if the token is expired.
     * @return bool
     */
    public function isExpired(): bool
    {
         return $this->expiresIn() === 0;
    }

    /**
     * Gets how long until token expires, in seconds.
     * @return int
     */
    public function expiresIn(): int
    {
        $diff = ($this->exp->getTimestamp() - time());

        return ($diff <= 0) ? 0 : $diff;
    }
}
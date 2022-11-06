<?php

namespace Zploited\Identity\Client\Models;

use Lcobucci\JWT\Token;
use Zploited\Identity\Client\Exceptions\IdentityArgumentException;

class AccessToken extends JsonWebToken
{
    /**
     * @throws IdentityArgumentException
     */
    public function __construct(string $token)
    {
        /** @var Token $parsed */
        $parsed = parent::__construct($token);

        if(strtolower($parsed->headers()->get('typ')) !== 'at+jwt') {
            throw new IdentityArgumentException('The provided token is not an access token');
        }
    }

    public string $jti;
    public \DateTimeImmutable $nbf;
    public string $sub;
    public array $scopes;
}
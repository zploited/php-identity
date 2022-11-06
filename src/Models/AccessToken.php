<?php

namespace Zploited\Identity\Client\Models;

use Lcobucci\JWT\Token;
use Zploited\Identity\Client\Exceptions\IdentityArgumentException;

class AccessToken extends JsonWebToken
{
    public string $jti;
    public \DateTimeImmutable $nbf;
    public string $sub;
    public array $scopes;

    protected string $kid;
    protected string $alg;
    protected string $typ;

    /**
     * @throws IdentityArgumentException
     */
    public function __construct(string $token)
    {
        /** @var Token $parsed */
        $parsed = parent::__construct($token);

        if(strtolower($parsed->headers()->get('typ')) !== 'at+jwt') {
            throw new IdentityArgumentException('Not an access token');
        }

        if(!$parsed->headers()->has('kid')) {
            throw new IdentityArgumentException('Missing mandatory key ID');
        }

        $this->typ = $parsed->headers()->get('typ');
        $this->kid = $parsed->headers()->get('kid');
        $this->alg = $parsed->headers()->get('alg');
    }

    public function alg(): string
    {
        return $this->alg;
    }

    public function kid(): string
    {
        return $this->kid;
    }

    public function typ(): string
    {
        return $this->typ;
    }
}
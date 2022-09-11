<?php

namespace Zploited\Identity\Client;

use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Zploited\Identity\Client\Exceptions\IdentityCoreException;

/**
 * Simplifies the use of a jwt
 */
class JwtTokenHandler
{
    protected Token $token;
    protected string $jwt;

    /**
     * @throws IdentityCoreException
     */
    public function __construct(string $jwt)
    {
        $this->jwt = $jwt;

        $parser = new Parser(new JoseEncoder());

        try {

            $this->token = $parser->parse($jwt);

        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {

            throw new IdentityCoreException($e->getMessage(), $e->getCode());

        }
    }

    /**
     * Returns the value of a claim with the same name as the property.
     * If the claim does not exist, it returns null
     *
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        try {

            return $this->token->claims()->get($property);

        } catch (\Exception $e) {

            return null;

        }
    }

    /**
     * Setter for making sure no properties are written to the object.
     *
     * @param $property
     * @param $value
     * @return void
     */
    public function __set($property, $value): void
    {
    }
}
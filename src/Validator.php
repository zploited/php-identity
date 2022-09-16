<?php

namespace Zploited\Identity\Client;


use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Zploited\Identity\Client\Exceptions\IdentityCoreException;
use Zploited\Identity\Client\Exceptions\IdentityValidationException;
use Zploited\Identity\Client\Interfaces\TokenInterface;
use Zploited\Identity\Client\Libs\Jwks;

class Validator
{
    protected string $issuer;
    protected ?string $publicKeyPath;

    protected Jwks $jwks;

    public function __construct(string $issuer, ?string $publicKeyPath = null)
    {
        $this->issuer = $issuer;
        $this->publicKeyPath = $publicKeyPath;

        $this->jwks = new Jwks($issuer);
    }

    /**
     * @throws IdentityCoreException
     */
    public function validateToken(TokenInterface $token): bool
    {
        /*
         * Starting out by making the configuration, so we can parse the token.
         * Since we also need to validate the token later, we will set the public key in the configuration as well.
         */
        try {
            $publicKey = ($this->publicKeyPath !== null) ?
                InMemory::file($this->publicKeyPath) :
                InMemory::plainText($this->jwks->pem($token->getHeader('kid')));

        } catch (\Exception $ex) {
            throw new IdentityCoreException('Unable to retrieve the public key.');
        }

        $config = Configuration::forAsymmetricSigner(new Sha256(), InMemory::empty(), $publicKey);

        /*
         * Configuration is in place, and it is time to do some validation.
         * First lets validate the algorithm sent with the token.
         */
        $allowedAlgorithms = ['rs256','hs256'];
        if(!in_array(
            strtolower( $token->getHeader('alg') ),
            $allowedAlgorithms
        )) {
            throw new IdentityValidationException('The token algorithm is not allowed.');
        }

        /*
         * Then lets check if the token has at+jwt as TYP header.
         * This indicates that this is an access token...
         */
        if(strtolower($token->getHeader('typ')) !== 'at+jwt') {
            throw new IdentityValidationException('This is not an access token.');
        }

        /*
         * Next, lets make sure the token is signed with our public key
         */
        if(!$config->validator()->validate(
            $token->getJwtToken(),
            new SignedWith(
                $config->signer(),
                $config->verificationKey()
            )
        ))
        {
            throw new IdentityValidationException('Invalid token signature.');
        }

        /*
         * Checking if the token is expired or used before it is valid (nbf)
         */
        if(!$config->validator()->validate(
            $token->getJwtToken(),
            new ValidAt(SystemClock::fromUTC())
        ))
        {
            throw new IdentityValidationException('The token has expired, or is not yet valid.');
        }

        /*
         * Checking if the token is issued by the correct issuer.
         */
        if(!$config->validator()->validate(
            $token->getJwtToken(),
            new IssuedBy($this->issuer)
        )) {
            throw new IdentityValidationException('Incorrect issuing service.');
        }

        /*
         * We got this far without throwing any exceptions.
         * It means the token is valid, so let
         */
        return true;
    }
}
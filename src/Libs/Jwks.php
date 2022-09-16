<?php

namespace Zploited\Identity\Client\Libs;

use CoderCat\JWKToPEM\JWKConverter;
use GuzzleHttp\Client;
use Zploited\Identity\Client\Traits\SessionStore;

class Jwks
{
    use SessionStore;

    protected string $url;

    public function __construct(string $issuer)
    {
        $this->url = 'https://'.$issuer.'/oauth/jwks.json';
    }

    public function pem(string $kid): ?string
    {
        if (!$this->hasSessionVariable('identity.jwks')) {
            $jwks = $this->jwksFromEndpoint();

            $this->setSessionVariable($jwks);
        } else {
            $jwks = $this->getSessionVariable('identity.jwks');
        }

        return $this->pemFromJwksJson($jwks, $kid);
    }

    /**
     * Gets a json object response from the jwks endpoint on the authorization server.
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function jwksFromEndpoint(): object
    {
        $response = (new Client())->get($this->url);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Takes a json string and searches it for a key id.
     * when found, this jwk is converted into a pem and returned.
     *
     * @param object $json
     * @param string $kid
     * @return string|null
     */
    protected function pemFromJwksJson(object $json, string $kid): ?string
    {
        /*
         * Making sure the keys array exists in the response
         */
        if(!isset($json->keys)) {
            return null;
        }

        /*
         * Looping through all the keys to find the key that matches our key id, and is a signature.
         */
        foreach ($json->keys as $key) {
            if($key->use === 'sig' && $key->kid === $kid) {
                /*
                 * We found one!
                 * Lets convert it into a public key, and return it.
                 */
                try {
                    $converter = new JWKConverter();

                    return $converter->toPEM((array)$key);
                } catch (\Exception $ex) {}
            }
        }

        return null;
    }
}
<?php

namespace Zploited\Identity\Client;

use Exception;
use InvalidArgumentException;
use Zploited\Identity\Client\Traits\SessionState;
use Zploited\Identity\Client\Traits\SessionStore;

/**
 * Identity Class
 * Manages the oauth flows as well as preserving the requested authentication.
 */
class Identity
{
    use SessionStore, SessionState;

    /**
     * @var array{ identifier: string, client_id: string, client_secret: string, redirect_uri: ?string, scopes: string[] }
     */
    protected array $params;

    /**
     * Class constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        /*
         * Checking if 'identifier' is provided
         */
        if(!isset($params['identifier'])) {
            throw new InvalidArgumentException("The identifier parameter is missing.");
        }

        /*
         * Checking if 'client_id' is provided
         */
        if(!isset($params['client_id'])) {
            throw new InvalidArgumentException("The client_id parameter is missing.");
        }

        /*
         * Saves the parameters
         */
        $this->params = $params;
    }

    /**
     * Gets the URL path of where the authorization endpoint is located.
     * This is based off the identifier provided while instantiating the object.
     *
     * @return string
     */
    public function getAuthorizationPath(): string
    {
        return 'https://' . $this->params['identifier'] . '/oauth/authorize';
    }

    /**
     * Gets the full url used for initiating an authorization flow, which is a combination of the authorization path,
     * and a set of query strings the server needs to identify the client and what it should return.
     *
     * @param bool $implicit tells the endpoint what type of response_type should be requested.
     * @return string
     */
    public function getAuthorizationUrl(bool $implicit = false): string
    {
        $queryParams = [
            'response_type' => ($implicit) ? 'token' : 'code',
            'client_id' => $this->params['client_id'],
            'redirect_uri' => $this->params['redirect_uri'],
            'scope' => implode(' ', $this->params['scopes']),
            'state' => (method_exists($this, 'setState')) ? $this->setState() : null
        ];

        return $this->getAuthorizationPath() .'?'. http_build_query($queryParams);
    }

    /**
     * Redirects the browser to the authorization endpoint url, together with the necessary query variables.
     * Done by setting the header location, together with no-cache settings, so this should be executed early in the script,
     * and before any output.
     *
     * @param bool $implicit
     * @return void
     */
    public function beginAuthorizationFlow(bool $implicit = false): void
    {
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
        header('Location: '. $this->getAuthorizationUrl($implicit));
    }

    /**
     * Handles an incoming authorization request callback.
     *
     * @return void
     * @throws InvalidArgumentException|Exception
     */
    public function handleAuthorizationResponse()
    {
        /*
         * Checking if state is provided, and if it is, it has to match the one we sent with it
         * If it doesn't, it should be rejected, since the response must come from another source...
         */
        if(isset($_GET['state']) && $_GET['state'] !== $this->getState()) {
            throw new InvalidArgumentException('The state provided does not match our own state.');
        }

        /*
         * Next we need to have either a token or a code, depending on what type of authorization we have completed.
         * Lets check for both, or give an error if none of them exists.
         */
        if(isset($_GET['code'])) {
            /*
             * We are getting an authorization code, so this needs to be traded at the token endpoint for an
             * access token.
             */


        } elseif (isset($_GET['access_token'])) {
            /*
             * This is an implicit call, so we are getting the token directly without any authorization codes.
             */
            $this->handleTokenResponse(
                $_GET['access_token'],
                $_GET['expires_in'],
                $_GET['token_type'],
                null,
                (isset($_GET['id_token'])) ? $_GET['id_token'] : null
            );

        } else {
            /*
             * We didn't receive either a code, token or error variable...
             * Something is wrong in this call. The endpoint has probably been called directly in the browser...
             */
            throw new InvalidArgumentException('The response should contain either a code, a token or an error.');
        }
    }

    /**
     * Handles a token response, by validating the access token, and returning a token object.
     *
     * @param string $accessToken
     * @param string $expiresIn
     * @param string $type
     * @param string|null $refreshToken
     * @param string|null $idToken
     * @return Token
     * @throws Exception
     */
    protected function handleTokenResponse(string $accessToken, string $expiresIn, string $type = "Bearer", ?string $refreshToken = null, ?string $idToken = null): Token
    {
        return new Token(
            $accessToken,
            $expiresIn,
            $refreshToken,
            $idToken,
            $type
        );
    }
}
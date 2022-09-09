<?php

namespace Zploited\Identity\Client;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Zploited\Identity\Client\Exceptions\IdentityArgumentException;
use Zploited\Identity\Client\Exceptions\IdentityCoreException;
use Zploited\Identity\Client\Exceptions\IdentityErrorResponseException;
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
     * @var Client
     */
    protected Client $client;

    /**
     * Class constructor.
     *
     * @param array $params
     * @throws IdentityArgumentException
     */
    public function __construct(array $params)
    {
        /*
         * Checking if 'identifier' is provided
         */
        if(!isset($params['identifier'])) {
            throw new IdentityArgumentException("The identifier parameter is missing.");
        }

        /*
         * Checking if 'client_id' is provided
         */
        if(!isset($params['client_id'])) {
            throw new IdentityArgumentException("The client_id parameter is missing.");
        }

        /*
         * Saves the parameters
         */
        $this->params = $params;

        /*
         * Initiates the Http client
         */
        $this->client = new Client([
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'Accept' => 'application/json'
        ]);
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
     * Gets the URL path of where the token endpoint can be found.
     *
     * @return string
     */
    public function getTokenEndpointPath(): string
    {
        return 'https://' . $this->params['identifier'] . '/oauth/token';
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
     * Initiates a token request using the password grant.
     *
     * @param string $email
     * @param string $password
     * @return Token
     * @throws GuzzleException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    public function password(string $email, string $password): Token
    {
        $response = $this->client->post($this->getTokenEndpointPath(), [
            'grant_type' => 'password',
            'client_id' => $this->params['client_id'],
            'client_secret' => $this->params['client_secret'],
            'scope' => implode(' ', $this->params['scopes']),
            'username' => $email,
            'password' => $password
        ]);

        return $this->handleGuzzleTokenResponse($response);
    }

    /**
     * Initiates a token request using the client credentials grant.
     *
     * @return Token
     * @throws GuzzleException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    public function clientCredentials(): Token
    {
        $response = $this->client->post($this->getTokenEndpointPath(), [
            'grant_type' => 'client_credentials',
            'client_id' => $this->params['client_id'],
            'client_secret' => $this->params['client_secret'],
            'scope' => implode(' ', $this->params['scopes'])
        ]);

        return $this->handleGuzzleTokenResponse($response);
    }

    /**
     * Handles an incoming authorization request callback.
     *
     * @return Token
     * @throws IdentityArgumentException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    public function handleAuthorizationResponse(): Token
    {
        /*
         * Checking if state is provided, and if it is, it has to match the one we sent with it
         * If it doesn't, it should be rejected, since the response must come from another source...
         */
        if(isset($_GET['state']) && $_GET['state'] !== $this->getState()) {
            throw new IdentityArgumentException('The state provided does not match our own state.');
        }

        /*
         * Next we need to have either a token or a code, depending on what type of authorization we have completed.
         * Lets check for both, or give an error if none of them exists.
         */
        if(isset($_GET['code'])) {
            /*
             * We are getting an authorization code, so this needs to be traded at the token endpoint for an
             * access token.
             * The client headers dictates that the result should not be cached, and accepts Json
             */
            try {
                $response = $this->client->post($this->getTokenEndpointPath(), [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->params['client_id'],
                    'client_secret' => $this->params['client_secret'],
                    'redirect_uri' => $this->params['redirect_uri'],
                    'code' => $_GET['code']
                ]);
            } catch (GuzzleException $exception) {
                throw new IdentityCoreException($exception->getMessage(), $exception->getCode());   // wrapping the GuzzleException into a IdentityCoreException
            }

            return $this->handleGuzzleTokenResponse($response);

        } elseif (isset($_GET['access_token'])) {
            /*
             * This is an implicit call, so we are getting the token directly without any authorization codes.
             */
            return $this->handleTokenResponse(
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
            throw new IdentityArgumentException('The response should contain either a code, a token or an error.');
        }
    }

    /**
     * Handles the Guzzle response for an token endpoint call.
     *
     * @param ResponseInterface $response
     * @return Token
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    protected function handleGuzzleTokenResponse(ResponseInterface $response): Token
    {
        if($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getBody()->getContents());

            /*
             * Catching if the response was an error response
             */
            if(isset($responseData['error'])) {
                throw new IdentityErrorResponseException($responseData['error']);
            }

            /*
             * If not, we continue to process the response into a token
             */
            return $this->handleTokenResponse(
                $responseData['access_token'],
                $responseData['expires_in'],
                $responseData['token_type'],
                $responseData['refresh_token'],
                (isset($responseData['id_token'])) ? $responseData['id_token'] : null
            );
        } else {
            throw new IdentityCoreException($response->getBody()->getContents());
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
     * @throws IdentityCoreException
     */
    protected function handleTokenResponse(string $accessToken, string $expiresIn, string $type = "Bearer", ?string $refreshToken = null, ?string $idToken = null): Token
    {
        try {
            return new Token(
                $accessToken,
                $expiresIn,
                $refreshToken,
                $idToken,
                $type
            );
        } catch (Exception $exception) {
            throw new IdentityCoreException($exception->getMessage(), $exception->getCode());
        }
    }
}
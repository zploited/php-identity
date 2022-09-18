<?php

namespace Zploited\Identity\Client;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Zploited\Identity\Client\Exceptions\IdentityArgumentException;
use Zploited\Identity\Client\Exceptions\IdentityCoreException;
use Zploited\Identity\Client\Exceptions\IdentityErrorResponseException;
use Zploited\Identity\Client\Libs\ApiClient;
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
     * @var array{ identifier: string, client_id: string, client_secret: string, redirect_uri: ?string, scopes: string[], persist_tokens: bool }
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

        if(!isset($params['persist_tokens'])) {
            $params['persist_tokens'] = true;
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
            'Pragma' => 'no-cache'
        ]);
    }

    /**
     * Gets the saved access token.
     *
     * @return Token|null
     */
    public function accessToken(): ?Token
    {
        return $this->loadToken('access');
    }

    /**
     * Gets the saved ID token.
     *
     * @return Token|null
     */
    public function idToken(): ?Token
    {
        return $this->loadToken('id');
    }

    /**
     * Gets the saved refresh token.
     *
     * @return Token|null
     */
    protected function refreshToken(): ?Token
    {
        return $this->loadToken('refresh');
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
     * @return TokenResponse
     * @throws GuzzleException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    public function password(string $email, string $password): TokenResponse
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
     * @return TokenResponse
     * @throws GuzzleException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    public function clientCredentials(): TokenResponse
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
     * @return TokenResponse
     * @throws IdentityArgumentException
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException|GuzzleException
     */
    public function handleAuthorizationResponse(): TokenResponse
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
                $response = $this->client->request('POST', $this->getTokenEndpointPath(), [ 'multipart' =>
                    [
                        ['name' => 'grant_type', 'contents' => 'authorization_code'],
                        ['name' => 'client_id', 'contents' => $this->params['client_id']],
                        ['name' => 'client_secret', 'contents' => $this->params['client_secret']],
                        ['name' => 'redirect_uri', 'contents' => $this->params['redirect_uri']],
                        ['name' => 'code', 'contents' => urldecode($_GET['code'])],
                    ]
                ]);
            } catch (ClientException $clientException) {
                $response = json_decode($clientException->getResponse()->getBody()->getContents());

                throw new IdentityErrorResponseException($response->error, $response->error_description, $response->hint, $response->message);
            } catch (ServerException $serverException) {
                throw new IdentityCoreException($serverException->getResponse()->getBody()->getContents());
            }

            $tokenResponse = $this->handleGuzzleTokenResponse($response);

        } elseif (isset($_GET['access_token'])) {
            /*
             * This is an implicit call, so we are getting the token directly without any authorization codes.
             */
            $tokenResponse = $this->handleTokenResponse(
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

        /*
         * saves the tokens locally before returning the response.
         */
        $this->saveToken('access', $tokenResponse->getAccessToken());
        $this->saveToken('id', $tokenResponse->getIdToken());
        $this->saveToken('refresh', $tokenResponse->getRefreshToken());

        return $tokenResponse;
    }

    /**
     * Gets the api handler for this identity.
     *
     * @return ApiClient
     */
    public function api(): ApiClient
    {
        return new ApiClient($this->params['identifier'], $this->accessToken());
    }

    /**
     * Handles the Guzzle response for a token endpoint call.
     *
     * @param ResponseInterface $response
     * @return TokenResponse
     * @throws IdentityCoreException
     * @throws IdentityErrorResponseException
     */
    protected function handleGuzzleTokenResponse(ResponseInterface $response): TokenResponse
    {
        $responseData = json_decode($response->getBody()->getContents());

        /*
         * If not, we continue to process the response into a token
         */
        return $this->handleTokenResponse(
            $responseData->access_token,
            $responseData->expires_in,
            $responseData->token_type,
            $responseData->refresh_token,
            (isset($responseData->id_token)) ? $responseData->id_token : null
        );
    }

    /**
     * Handles a token response, by validating the access token, and returning a token object.
     *
     * @param string $accessToken
     * @param string $expiresIn
     * @param string $type
     * @param string|null $refreshToken
     * @param string|null $idToken
     * @return TokenResponse
     * @throws IdentityCoreException
     */
    protected function handleTokenResponse(string $accessToken, string $expiresIn, string $type = "Bearer", ?string $refreshToken = null, ?string $idToken = null): TokenResponse
    {
        try {
            return new TokenResponse(
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

    protected function saveToken(string $type, string $token): void
    {
        if($this->params['persist_tokens']) {
            $this->setSessionVariable($this->params['identifier'].'.'.$type, $token);
        }
    }

    protected function loadToken(string $type): ?Token
    {
        if($this->params['persist_tokens']) {
            return new Token($this->getSessionVariable($this->params['identifier'].'.'.$type));
        }

        return null;
    }
}
<?php

namespace Zploited\Identity\Client;

use Zploited\Identity\Client\Traits\SessionState;

/**
 * Identity Class
 * Manages the oauth flows as well as preserving the requested authentication.
 */
class Identity
{
    use SessionState;

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
            throw new \InvalidArgumentException("The identifier parameter is missing.");
        }

        /*
         * Checking if 'client_id' is provided
         */
        if(!isset($params['client_id'])) {
            throw new \InvalidArgumentException("The client_id parameter is missing.");
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

    public function handleAuthorizationResponse()
    {
        /*
         * Checking if state is provided, and if it is, it has to match the one we sent with it
         * If it doesn't, it should be rejected, since the response must come from another source...
         */

        
    }
}
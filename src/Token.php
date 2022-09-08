<?php

namespace Zploited\Identity\Client;

use DateTime;
use Exception;

class Token
{
    /**
     * @var string token for granting api access.
     */
    protected string $accessToken;

    /**
     * @var DateTime when the access token expires.
     */
    protected DateTime $expires;

    /**
     * @var string type of token
     */
    protected string $type;

    /**
     * @var string|null token for retrieving a new access token, when the current expires.
     */
    protected ?string $refreshToken;

    /**
     * @var string|null stringified jwt token containing user information
     */
    protected ?string $idToken;

    /**
     * Class constructor.
     *
     * @param string $accessToken access token.
     * @param int $expiresIn time in seconds until the access token is no longer valid.
     * @param string $type type of token. this is typically always a bearer token.
     * @param string|null $refreshToken token for automatically getting a new access token, without having to authorize again.
     * @param string|null $idToken id token is provided if openid scope is being used, and is a jwt containing information about the user.
     * @throws Exception
     */
    public function __construct(string $accessToken, int $expiresIn, ?string $refreshToken = null, ?string $idToken = null, string $type = "Bearer")
    {
        $this->accessToken = $accessToken;
        $this->expires = (new DateTime())->add(new \DateInterval("PT".$expiresIn."S"));
        $this->refreshToken = $refreshToken;
        $this->idToken = $idToken;
        $this->type = $type;
    }

    /**
     * Gets the access token.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Gets the time when the access token expires in DateTime format
     *
     * @return DateTime
     */
    public function expiresAt(): DateTime
    {
        return $this->expires;
    }

    /**
     * Ges the time in seconds until the access token expires.
     * A negative result means the token is expired, and shows the number of seconds since it expired.
     *
     * @return int
     */
    public function expiresIn(): int
    {
        return $this->expires->getTimestamp() - (new DateTime())->getTimestamp();
    }

    /**
     * Gets the saved refresh token
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
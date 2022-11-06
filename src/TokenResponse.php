<?php

namespace Zploited\Identity\Client;

use DateInterval;
use DateTime;
use Exception;
use Zploited\Identity\Client\Exceptions\IdentityArgumentException;
use Zploited\Identity\Client\Models\AccessToken;
use Zploited\Identity\Client\Models\IdToken;
use Zploited\Identity\Client\Models\RefreshToken;

class TokenResponse
{
    protected AccessToken $accessToken;
    protected DateTime $expires;
    protected string $type;
    protected ?RefreshToken $refreshToken;
    protected ?IdToken $idToken;

    /**
     * Class constructor.
     *
     * @param string $accessToken access token.
     * @param int $expiresIn time in seconds until the access token is no longer valid.
     * @param string|null $refreshToken token for automatically getting a new access token, without having to authorize again.
     * @param string|null $idToken id token is provided if openid scope is being used, and is a jwt containing information about the user.
     * @param string $type type of token. this is typically always a bearer token.
     * @throws IdentityArgumentException
     * @throws Exception
     */
    public function __construct(string $accessToken, int $expiresIn, ?string $refreshToken = null, ?string $idToken = null, string $type = "Bearer")
    {
        $this->accessToken = new AccessToken($accessToken);
        $this->idToken = ($idToken !== null) ? new IdToken($idToken) : null;
        $this->refreshToken = ($refreshToken !== null) ? new RefreshToken($refreshToken) : null;
        $this->expires = (new DateTime())->add(new DateInterval("PT".$expiresIn."S"));
        $this->type = $type;
    }

    /**
     * Gets the access token.
     *
     * @return AccessToken
     */
    public function accessToken(): AccessToken
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
     * @return RefreshToken|null
     */
    public function refreshToken(): ?RefreshToken
    {
        return $this->refreshToken;
    }

    /**
     * Gets the id token.
     *
     * @return IdToken|null
     */
    public function idToken(): ?IdToken
    {
        return $this->idToken;
    }
}
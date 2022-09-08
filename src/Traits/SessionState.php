<?php

namespace Zploited\Identity\Client\Traits;

trait SessionState
{
    use SessionStore;

    /**
     * @var string name of the session variable used for storing the state value.
     */
    protected static string $SESSION_STATE_IDENTIFIER = 'identity_state';

    /**
     * Stores a new session with a random string, used for authorization states.
     * The generated state is returned
     *
     * @return string returns the random string being set as a state
     */
    public function setState(): string
    {
        $state = md5(rand());
        $this->setSessionVariable(self::$SESSION_STATE_IDENTIFIER, $state);

        return $state;
    }

    /**
     * Gets the value of the stored state session, or returns null if no state is set.
     *
     * @return string
     */
    public function getState(): ?string
    {
        return $this->getSessionVariable(self::$SESSION_STATE_IDENTIFIER);
    }
}
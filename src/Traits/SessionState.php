<?php

namespace Zploited\Identity\Client\Traits;

trait SessionState
{
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
        $this->startSessions();

        $state = md5(rand());
        $_SESSION[self::$SESSION_STATE_IDENTIFIER] = $state;

        return $state;
    }

    /**
     * Gets the value of the stored state session, or returns null if no state is set.
     *
     * @return string
     */
    public function getState(): ?string
    {
        $this->startSessions();

        return (isset($_SESSION[self::$SESSION_STATE_IDENTIFIER])) ? $_SESSION[self::$SESSION_STATE_IDENTIFIER] : null;
    }

    /**
     * Method for making sure php sessions are started and ready to save data.
     */
    protected function startSessions(): void
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
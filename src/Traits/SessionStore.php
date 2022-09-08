<?php

namespace Zploited\Identity\Client\Traits;

trait SessionStore
{
    /**
     * Method for making sure php sessions are started and ready to save data.
     */
    private function startSessions(): void
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Saves a value to a session variable
     *
     * @param $variable
     * @param $value
     * @return void
     */
    protected function setSessionVariable($variable, $value): void
    {
        $this->startSessions();

        $_SESSION[$variable] = serialize($value);
    }

    /**
     * Gets a value from a session variable
     *
     * @param $variable
     * @return mixed
     */
    protected function getSessionVariable($variable)
    {
        $this->startSessions();

        return isset($_SESSION[$variable]) ? unserialize($_SESSION[$variable]) : null;
    }

    /**
     * Deletes a value from the session
     *
     * @param $variable
     * @return void
     */
    protected function deleteSessionVariable($variable)
    {
        $this->startSessions();

        unset($_SESSION[$variable]);
    }
}
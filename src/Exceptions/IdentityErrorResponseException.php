<?php

namespace Zploited\Identity\Client\Exceptions;

class IdentityErrorResponseException extends IdentityCoreException
{
    protected string $error;
    protected string $description;
    protected ?string $hint;

    public function __construct(string $error, string $description, ?string $hint = null, ?string $message = null)
    {
        parent::__construct($message);

        $this->error = $error;
        $this->description = $description;
        $this->hint = $hint;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getHint(): string
    {
        return $this->hint;
    }
}
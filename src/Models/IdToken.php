<?php

namespace Zploited\Identity\Client\Models;

class IdToken extends JsonWebToken
{
    public ?string $email = null;
    public ?bool $emailVerified = null;
    public ?string $name = null;
    public ?string $given_name = null;
    public ?string $family_name = null;
    public ?string $middle_name = null;
    public ?string $nickname = null;
    public ?string $profile = null;
    public ?string $picture = null;
    public ?string $website = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $zoneinfo = null;
    public ?string $locale = null;
    public ?int $updated_at = null;
}
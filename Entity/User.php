<?php

namespace App\Entity;

use App\AbstractVendor\ORM\EntityInterface;

/**
 * Class User
 * @method set($key, $value)
 */
class User implements EntityInterface
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $surname,
        public string $email,
        public string $telegramUsername,
        public string $phoneNumber,
        public string $genericUserData,
    ) {}
}

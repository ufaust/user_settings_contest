<?php

namespace App\Service\User\DTO;

use App\AbstractVendor\SomeTools\Arrayable;

class TwoFAValidationDTO implements Arrayable
{
    public const ATTEMPTS_COUNT = 5;
    public const ATTEMPTS_TIMER = 60;

    public function __construct(
        public int $count,
        public string $validationKey,
        public int $lastTry,
        public bool $approved = false,
    ) {}

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'validation_key' => $this->validationKey,
            'last_try' => $this->lastTry,
            'approved' => $this->approved,
        ];
    }

    public static function castFromArray(array $parameters): static
    {
        return new static(
            count: $parameters['count'],
            validationKey: $parameters['validation_key'],
            lastTry: $parameters['last_try'],
        );
    }
}

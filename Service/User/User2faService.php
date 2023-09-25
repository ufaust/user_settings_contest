<?php

namespace App\Service\User;

use App\AbstractVendor\SomeTools\HasherInterface;
use App\Entity\User;
use App\Service\Cache\CacheInterface;
use App\Service\Message\MessengerInterface;
use App\Service\User\DTO\TwoFAValidationDTO;
use App\Service\User\Exception\IncorrectValidationKeyException;
use App\Service\User\Exception\TooManyAttemptsException;

class User2faService
{
    public function __construct(
        private CacheInterface $cacheService,
        private MessengerInterface $messenger,
        private HasherInterface $hasher,
    ) {}

    public function isApprove(User $user): bool
    {
        $cacheUserData = $this->cacheService->get($user->id);

        if (array_key_exists('2FA', $cacheUserData)) {
            $this->makeAttempt($user);

            return false;
        }

        return TwoFAValidationDTO::castFromArray($cacheUserData['2FA'])->approved;
    }

    public function validate(User $user, string $validationKey)
    {
        $cacheUserData = $this->cacheService->get($user->id);
        $validation = TwoFAValidationDTO::castFromArray($cacheUserData['2FA']);

        if ($validation->count > TwoFAValidationDTO::ATTEMPTS_COUNT) {
            throw new TooManyAttemptsException("Too many attempts!");
        }

        if ($validation->lastTry - time() < TwoFAValidationDTO::ATTEMPTS_TIMER ) {
            throw new IncorrectValidationKeyException("Wait for " . TwoFAValidationDTO::ATTEMPTS_TIMER . " seconds for next attempt");
        }

        if ($validation->validationKey !== $validationKey) {
            throw new IncorrectValidationKeyException("Incorrect validation key!");
        }

        $validation->approved = true;
        $this->cacheService->set($user->id, $validation->toArray());
    }

    public function makeAttempt(User $user): void
    {
        $validation = new TwoFAValidationDTO(
            count: 1, validationKey: $this->hasher->makeHash(), lastTry: time()
        );

        $this->cacheService->set($user->id, $validation->toArray());
        $this->messenger->send($user, $validation->validationKey);
    }

    public function updateAttempt(int $userId): void
    {
        $cacheUserData = $this->cacheService->get($userId);
        $cacheUserData['2FA']['attempts']['count'] += 1;
        $cacheUserData['2FA']['attempts']['last_try'] = time();

        $this->cacheService->set($userId, $cacheUserData);
    }
}

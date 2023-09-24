<?php

namespace App\Service\User;

use App\AbstractVendor\SomeTools\HasherInterface;
use App\Entity\User;
use App\Service\Cache\CacheInterface;
use App\Service\Message\MessengerInterface;
use App\Service\User\Exception\AttemptsTimeLimitException;
use App\Service\User\Exception\TooManyAttemptsException;

class User2faService
{
    public const ATTEMPTS_COUNT = 5;
    public const ATTEMPTS_TIMER = 60;

    public function __construct(
        private CacheInterface $cacheService,
        private MessengerInterface $messenger,
        private HasherInterface $hasher,
    ) {}

    public function isApprove(int $userId): bool
    {
        $cacheUserData = $this->cacheService->get($userId);

        if (array_key_exists('validation_key', $cacheUserData['2FA']['attempts'])) {
            return false;
        }

        return true;
    }

    public function validate(int $userId, string $validationKey): bool
    {
        $cacheUserData = $this->cacheService->get($userId);
        if ($cacheUserData[User::CACHE_2FA_VALIDATION]['attempts']['count'] > self::ATTEMPTS_COUNT) {
            throw new TooManyAttemptsException("Too many attempts!");
        }

        if ($cacheUserData[User::CACHE_2FA_VALIDATION]['attempts']['last_try'] - time() < self::ATTEMPTS_TIMER ) {
            throw new AttemptsTimeLimitException("Wait for " . self::ATTEMPTS_TIMER . " seconds for next attempt");
        }

        if ($cacheUserData[User::CACHE_2FA_VALIDATION]['attempts']['validation_key'] !== $validationKey) {
            return false;
        }

        return true;
    }

    public function makeAttempt(User $user): array
    {
        $cacheUserData = $this->cacheService->get($user->id);
        $cacheUserData['2FA']['attempts']['count'] = 1;
        $cacheUserData['2FA']['attempts']['last_try'] = time();
        $cacheUserData['2FA']['attempts']['validation_key'] = $this->hasher->makeHash();

        $this->cacheService->set($user->id, $cacheUserData);
        $this->messenger->send($user, $cacheUserData['2FA']['attempts']['validation_key']);

        return $cacheUserData['2FA']['attempts'];
    }

    public function updateAttempt(int $userId): void
    {
        $cacheUserData = $this->cacheService->get($userId);
        $cacheUserData['2FA']['attempts']['count'] += 1;
        $cacheUserData['2FA']['attempts']['last_try'] = time();

        $this->cacheService->set($userId, $cacheUserData);
    }
}
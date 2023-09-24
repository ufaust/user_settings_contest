<?php

namespace App\Service\Cache;

interface CacheInterface
{
    public function get(int|string $key): mixed;
    public function set(int|string $key, mixed $value): void;
}
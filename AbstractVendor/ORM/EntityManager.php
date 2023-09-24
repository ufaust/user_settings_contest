<?php

namespace App\AbstractVendor\ORM;

interface EntityManager
{
    public function persist(EntityInterface $entity): void;

    public function flush(): void;
}
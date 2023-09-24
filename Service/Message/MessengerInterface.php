<?php

namespace App\Service\Message;

use App\Entity\User;

interface MessengerInterface
{
    public function send(User $user, string $message): bool;
}

<?php

namespace App\Service\User;

use App\AbstractVendor\Form\FormInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function updateSecurityProp(User $user, FormInterface $userForm, string $prop): User
    {
        $user->set($prop, $userForm->get($prop));

        return $user;
    }
}

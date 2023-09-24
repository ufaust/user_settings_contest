<?php

namespace App\Service\User;

use App\AbstractVendor\Form\FormInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Exception\AttemptsTimeLimitException;

class UserService
{
    public function __construct(private UserRepository $userRepository){}

    public function updateSecurityProp(FormInterface $userForm): User
    {
        if (!$this->isApproved()) {
            throw new AttemptsTimeLimitException("2FA Approve required!");
        }

        $user = $this->userRepository->find($userForm->get('id'));
    }
}

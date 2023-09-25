<?php

namespace App\Form;

use App\Entity\User;

class UserType
{
    public function options(): array
    {
        return ['data_class' => User::class];
    }
}
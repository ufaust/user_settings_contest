<?php

namespace App\Form;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class UserType
{
    #[ArrayShape(['data_class' => "string"])]
    public function options(): array
    {
        return ['data_class' => User::class];
    }
}
<?php

namespace App\AbstractVendor\Http;

use App\AbstractVendor\Form\FormInterface;
use App\AbstractVendor\Form\TypeInterface;

class AbstractController
{
    public function createForm(string $type): FormInterface
    {
        return new class() implements FormInterface {
            public function handleRequest(Request $request): void {}
        };
    }
}
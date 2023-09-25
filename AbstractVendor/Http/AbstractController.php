<?php

namespace App\AbstractVendor\Http;

use App\AbstractVendor\Form\FormInterface;

class AbstractController
{
    public function createForm(string $type): FormInterface
    {
        return new class() implements FormInterface {
            public function handleRequest(Request $request): void {}

            public function get(string $key): mixed {}

        };
    }

    public function redirectToRoute(string $route): Response {}

}
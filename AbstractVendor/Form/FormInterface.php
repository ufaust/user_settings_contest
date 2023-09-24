<?php

namespace App\AbstractVendor\Form;

use App\AbstractVendor\Http\Request;

interface FormInterface
{
    public function handleRequest(Request $request);

    public function get(string $key): mixed;
}
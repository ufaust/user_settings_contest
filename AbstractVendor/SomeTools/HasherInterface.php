<?php

namespace App\AbstractVendor\SomeTools;

interface HasherInterface
{
    public function makeHash(): string;
}
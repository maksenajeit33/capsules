<?php

namespace App\Http\Traits;

Trait VerifyCode
{
    // Create a generate number (for verification)
    protected function generate()
    {
        $number = rand(1000, 9999);

        return $number;
    }
}


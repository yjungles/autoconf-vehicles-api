<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ChassiRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $value)) {
            $fail('O chassi deve possuir exatamente 17 caracteres alfanuméricos e não pode conter as letras I, O ou Q.');
        }
    }
}

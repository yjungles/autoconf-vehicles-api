<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MercosulPlateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/i', $value)) {
            $fail('A placa deve seguir o padrão Mercosul (ABC1D23).');
        }
    }
}

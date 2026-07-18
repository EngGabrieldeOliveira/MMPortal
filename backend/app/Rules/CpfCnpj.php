<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        if (! in_array(strlen($digits), [11, 14], true) || preg_match('/^(\d)\1+$/', $digits)) {
            $fail('O campo :attribute deve conter um CPF ou CNPJ válido.');

            return;
        }
        $base = substr($digits, 0, -2);
        $weights = strlen($digits) === 11 ? [[10, 9, 8, 7, 6, 5, 4, 3, 2], [11, 10, 9, 8, 7, 6, 5, 4, 3, 2]] : [[5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]];
        foreach ($weights as $index => $weight) {
            $sum = 0;
            foreach ($weight as $position => $factor) {
                $sum += ((int) $base[$position]) * $factor;
            } $digit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
            if ((int) $digits[strlen($base)] !== $digit) {
                $fail('O campo :attribute deve conter um CPF ou CNPJ válido.');

                return;
            } $base .= $digit;
        }
    }
}

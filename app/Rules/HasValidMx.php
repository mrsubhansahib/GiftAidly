<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasValidMx implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
         $domain = substr(strrchr($value, '@'), 1);
        if (!$domain || !dns_get_record($domain, DNS_MX)) {
            $fail('This is an invalid email.');
        }
    }
}

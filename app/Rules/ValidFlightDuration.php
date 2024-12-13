<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFlightDuration implements Rule
{
    
    public function passes($attribute, $value): bool
    {
        return preg_match('/^\d+\s*(hours|hour|h|hr)$/i', $value);
    }

    
    public function message(): string
    {
        return 'The :attribute must be in a valid duration format, such as "2 hours", "2hr", or "2h".';
    }
}

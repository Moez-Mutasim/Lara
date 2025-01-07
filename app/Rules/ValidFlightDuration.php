<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFlightDuration implements Rule
{
    
    public function passes($attribute, $value): bool
    {
        $regex = '/^\d+\s*(hours?|h)(\s*\d+\s*(minutes?|m))?$/i';
        return preg_match($regex, $value);
    }

    
    public function message(): string
    {
        return 'The :attribute must be in a valid duration format, such as "2 hours", "2h", "2 hours 30 minutes", or "2h30m".';
    }
}

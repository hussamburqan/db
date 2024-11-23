<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateTime implements Rule
{
    public function passes($attribute, $value)
    {
        $timeFormat = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
        return preg_match($timeFormat, $value);
    }

    public function message()
    {
        return 'The :attribute must be in the format HH:mm.';
    }
}
<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class UrlRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $regex = "/^((http:\/\/|https:\/\/|www\.)?";
        $regex .= "[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-z]{1,10}\b([-a-zA-Z0-9@:%_\+.~#()?&\/\/=]*))+/i";
        return preg_match($regex, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeは、有効なURL形式で指定してください。';
    }
    public static function handle(): string
    {
        return 'url';
    }
}

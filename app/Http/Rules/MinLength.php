<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

class MinLength implements Rule
{
    protected $min;

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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = str_replace(["\r\n", "\r", "\n"], " ", $value);
        return mb_strlen($value, 'UTF-8') >= $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public static function handle(): string
    {
        return 'min_length';
    }

    public function message()
    {
        return ":attributeは、" . $this->min . "文字の上に置いてください。";
    }

    public function validate($attribute, $value, $params, Validator $validator): bool
    {
        $this->max = $params[0];
        $handle = $this->handle();
        $validator->setCustomMessages([
            $handle => $this->message(),
        ]);
        return $this->passes($attribute, $value);
    }
}

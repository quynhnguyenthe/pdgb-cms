<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

class MaxLength implements Rule
{
    protected $max;

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
        return mb_strlen($value, 'UTF-8') <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public static function handle(): string
    {
        return 'max_length';
    }

    public function message()
    {
        return ":attributeは、" . $this->max . "文字以下にしてください。";
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

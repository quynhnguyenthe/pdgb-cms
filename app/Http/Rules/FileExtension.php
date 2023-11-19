<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

class FileExtension implements Rule
{
    protected $extensions;

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
        return in_array($value->getClientOriginalExtension(), $this->extensions, true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public static function handle(): string
    {
        return 'file_extension';
    }

    public function message()
    {
        return "選択された:attributeは、有効ではありません。";
    }

    public function validate($attribute, $value, $params, Validator $validator): bool
    {
        $this->extensions = $params;
        $handle = $this->handle();
        $validator->setCustomMessages([
            $handle => $this->message(),
        ]);
        return $this->passes($attribute, $value);
    }
}

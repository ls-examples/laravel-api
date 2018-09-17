<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class Base64Image implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $maxFileSize = 500*1024;
        $explode = explode(',', $value);
        $allow = ['png', 'jpg', 'jpeg', 'gif'];
        $format = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );

        if (!in_array($format, $allow)) {
            return false;
        }

        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            return false;
        }

        $fileSize = strlen(base64_decode($explode[1]));

        if ($fileSize > $maxFileSize) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.base64image');
    }
}

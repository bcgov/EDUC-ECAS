<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SocialInsuranceNumberRule implements Rule
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

        return $this->is_valid_luhn($value);

    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The SIN is not valid';
    }




    private function is_valid_luhn(string $number)
    {
        $sum = 0;
        $flag = 0;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $add = $flag++ & 1 ? $number[$i] * 2 : $number[$i];
            $sum += $add > 9 ? $add - 9 : $add;
        }

        return $sum % 10 === 0;
    }
}

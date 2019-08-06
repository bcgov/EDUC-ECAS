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
        $noSpaces = preg_replace('/\s+/', '', $value);

        if(empty($value)) {
            // field not required
            return true;
        }


        return $this->isValidSin($noSpaces);

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Social Insurance Number is not valid.';
    }


    private function isValidSin(String $num)
    {

        if( ! preg_match('/^\d{9}$/', $num)) {
            return false;
        }

        $sum = '';

        for ($i = strlen($num) - 1; $i >= 0; -- $i) {
            $sum .= $i & 1 ? $num[$i] : $num[$i] * 2;
        }

        return array_sum(str_split($sum)) % 10 === 0;

    }
}

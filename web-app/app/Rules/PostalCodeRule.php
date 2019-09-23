<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PostalCodeRule implements Rule
{

    private $country;

    /**
     * Create a new rule instance.
     *
     * @param $country
     */
    public function __construct($country)
    {
        $this->country = $country;

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


        // A Canadian postal code is required for Canadian addresses
        if ($this->country == 'Canada') {

            $regExTest = preg_match('/^\D\d\D\s?\d\D\d$/i', $value);


            if($regExTest == 1) {
                return true;
            }

            return false;
        }

        // we accept any postal code for non-Canadians
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The postal code is not valid.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PostalCodeRule implements Rule
{

    private $region;

    /**
     * Create a new rule instance.
     *
     * @param $region
     */
    public function __construct($region)
    {
        $this->region = $region;

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

        // A postal code is required
        if (! $value) {
            return false;
        }

        // TODO - replace region tests below with a country test when the country field is available
        // A Canadian postal code is required for Canadian addresses
        if ($this->region == 'BC' or $this->region == 'YT') {

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

<?php

namespace App\Rules;


use App\Dynamics\Interfaces\iCredential;
use Illuminate\Contracts\Validation\Rule;

class ValidProfileCredential implements Rule
{

    private $credentials;


    /**
     * Create a new rule instance.
     *
     * @param iCredential $credentials
     * @param $profile_credential_id
     */
    public function __construct(iCredential $credentials)
    {
        $this->credentials              = $credentials->all();

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
        // Method passes if the requested credential id is a valid id
        return in_array($value, $this->credentials->pluck('id')->toArray());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'That is not a valid credential id.';
    }
}

<?php

namespace App\Http\Requests;

use App\Rules\PostalCodeRule;
use App\Rules\SocialInsuranceNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Sanitize request data before validation.
     */
    protected function prepareForValidation()
    {
        $input = $this->only([
            'preferred_first_name',
            'first_name',
            'last_name',
            'email',
            'phone',
            'social_insurance_number',
            'address_1',
            'address_2',
            'city',
            'region',
            'country',
            'postal_code',
            'district',
            'school',
            'professional_certificate_bc',
            'professional_certificate_yk'
        ]);


        // Sanitize phone numbers, remove everything that isn't a number
        $sanitize_to_integer = ['phone'];
        foreach ($sanitize_to_integer as $field) {
            $request[$field] = preg_replace('/[^0-9.]/', '', $input[$field]);
        }

        // Populate 'school_id'
        if ( $input['school']['id']) {
            $input['school_id'] = $input['school']['id'];
            unset($input['school']);
        }

        // Populate 'district_id'
        if ( $input['district']['id']) {
            $input['district_id'] = $input['district']['id'];
            unset($input['district']);
        }

        // Populate 'country_id'
        if ( $input['country']['id']) {
            $input['country_id'] = $input['country']['id'];
            // don't unset $input['country'] as the name is used in the rules below
        }

        // We'll sanitize all strings

        $input['first_name']                = filter_var($input['first_name'],          FILTER_SANITIZE_STRING);
        $input['last_name']                 = filter_var($input['last_name'],           FILTER_SANITIZE_STRING);
        $input['preferred_first_name']      = filter_var($input['preferred_first_name'],FILTER_SANITIZE_STRING);
        $input['email']                     = filter_var($input['email'],               FILTER_SANITIZE_STRING);
        $input['phone']                     = filter_var($input['phone'],               FILTER_SANITIZE_STRING);
        $input['address_1']                 = filter_var($input['address_1'],           FILTER_SANITIZE_STRING);
        $input['address_2']                 = filter_var($input['address_2'],           FILTER_SANITIZE_STRING);
        $input['city']                      = filter_var($input['city'],                FILTER_SANITIZE_STRING);
        $input['postal_code']               = filter_var($input['postal_code'],         FILTER_SANITIZE_STRING);


        $this->replace($input);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $input = $this->only(['country']);

        
        return [
            'preferred_first_name'          => 'nullable',
            'first_name'                    => 'required',
            'last_name'                     => 'required',
            'email'                         => 'required|email',
            'phone'                         => 'required',
            'address_1'                     => 'required',
            'address_2'                     => 'nullable',
            'city'                          => 'required',
            'region'                        => 'required|alpha|size:2',
            'country_id'                    => 'required',
            'school_id'                     => 'nullable',
            'district_id'                   => 'nullable',
            'postal_code'                   => [ new PostalCodeRule($input['country']['name']) ],
            'social_insurance_number'       => [ 'nullable', new SocialInsuranceNumberRule() ],
            'professional_certificate_bc'   => 'nullable|in:Yes,No',
            'professional_certificate_yk'   => 'nullable|in:Yes,No'
        ];
    }
    
    
    public function messages()
    {
        return [
            'first_name.required'  => 'Your first name is required',
            'last_name.required'   => 'Your last name is required',
            'email.required'       => 'An email is required',
            'email.email'          => 'Invalid email',
            'phone.required'       => 'A phone number is required',
            'address_1.required'   => 'An address is required',
            'city.required'        => 'A city name is required',
            'region.required'      => 'A province or state is required',
            'country.required'     => 'A country is required',
        ];
    }


}

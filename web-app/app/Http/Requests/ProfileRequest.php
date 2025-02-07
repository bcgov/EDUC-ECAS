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

    protected function removeSpecialChars($string) {
        return preg_replace('/[^A-Za-z0-9|\-|\s]/', '', $string); // Removes special chars.
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

        $fieldsToSanitize = ['first_name', 'last_name', 'address_1', 'address_2', 'city', 'professional_certificate_bc', 'professional_certificate_yk'];
        foreach ($fieldsToSanitize as $field) {
            if (isset($input[$field])) {
                $input[$field] = $this->removeSpecialChars($input[$field]);
            }
        }        
 

        if (isset($input['social_insurance_number'])) {
            $input['social_insurance_number'] = preg_replace('/[^0-9.]/', '', $input['social_insurance_number']);
        }

        // Sanitize phone number, remove everything that isn't a number
        $input['phone'] = preg_replace('/[^0-9.]/', '', $input['phone']);


        // Populate 'school_id'
        if ( isset($input['school']['id'])) {
            $input['school_id'] = $input['school']['id'];
            unset($input['school']);
        }

        // Populate 'district_id'
        if ( isset($input['district']['id'])) {
            $input['district_id'] = $input['district']['id'];
            unset($input['district']);
        }

        // Populate 'country_id'
        if ( $input['country']['id']) {
            $input['country_id'] = $input['country']['id'];
            // don't unset $input['country'] as the name is used in the rules below
        }

        // We'll sanitize all strings
        $input['email']                     = filter_var($input['email'],               FILTER_SANITIZE_STRING);
        $input['phone']                     = filter_var($input['phone'],               FILTER_SANITIZE_STRING);
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
            'preferred_first_name'          => 'nullable|string|max:50',
            'first_name'                    => 'required|string|max:50',
            'last_name'                     => 'required|string|max:50',
            'email'                         => 'required|email',
            'phone'                         => 'required|numeric|digits:10',
            'address_1'                     => 'required|string|max:200',
            'address_2'                     => 'nullable|string|max:200',
            'city'                          => 'required|string|max:80',
            'region'                        => 'required|alpha|size:2',
            'country_id'                    => 'required|string|max:50',
            'school_id'                     => 'nullable|string|max:50',
            'district_id'                   => 'nullable|string|max:50',
            'postal_code'                   => [ 'required' , 'string' , 'max:20' , new PostalCodeRule($input['country']['name']) ],
            'social_insurance_number'       => [ 'nullable', 'numeric' , 'digits:9' , new SocialInsuranceNumberRule() ],
            'professional_certificate_bc'   => 'nullable|string|max:50',
            'professional_certificate_yk'   => 'nullable|string|max:50'
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

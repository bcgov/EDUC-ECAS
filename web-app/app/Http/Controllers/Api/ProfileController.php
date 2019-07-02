<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Profile;
use Illuminate\Http\Request;


class ProfileController extends BaseController
{



    /*
 * Create the Profile record (Contact in Dynamics)
 */
    public function store(Request $request)
    {

        $request = $this->validateProfileRequest($request);

        $profile = new Profile();
        $user_id = $profile->create($request->all());

        return $this->show($user_id);
    }

    /*
     * Update an existing user Profile (Contact in Dynamics)
     */
    public function update(Request $request)
    {
        $request = $this->validateProfileRequest($request);

        (new Profile())->update($this->userId(), $request->all());

        return json_encode($this->user());
    }

    /*
     * Create a new Assignment
     * Or change the status of an existing Assignment
     */
    private function validateProfileRequest(Request $request): Request
    {
        // Get rid of spaces
        $remove_spaces_from = ['postal_code', 'sin'];
        foreach ($remove_spaces_from as $field) {
            if (isset($request[$field])) {
                $request[$field] = preg_replace('/\s+/', '', $request[$field]);
            }
        }

        // Sanitize phone numbers, remove everything that isn't a number
        $sanitize_to_integer = ['phone'];
        foreach ($sanitize_to_integer as $field) {
            $request[$field] = preg_replace('/[^0-9.]/', '', $request[$field]);
        }

        // If we pass in blank look-up ids Dynamics freaks out
        // remove options that are blank
        $remove_blank_options = ['district_id', 'school_id'];
        foreach ($remove_blank_options as $field) {
            if ( ! $request[$field]) {
                unset($request[$field]);
            }
        }

        $this->validate($request, [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'required|email',
            'phone'       => 'required',
            'address_1'   => 'required',
            'city'        => 'required',
            'region'      => 'required',
            'postal_code' => 'required|regex:/^\D\d\D\s?\d\D\d$/i',
            'sin'         => 'regex:/^\d{9}$/i'
        ],
            [
                'first_name.required'  => 'Required',
                'last_name.required'   => 'Required',
                'email.required'       => 'Required',
                'email.email'          => 'Invalid email',
                'phone.required'       => 'Required',
                'address_1.required'   => 'Required',
                'city.required'        => 'Required',
                'region.required'      => 'Required',
                'postal_code.required' => 'Required',
                'postal_code.regex'    => 'Invalid Postal Code',
            ]);

        return $request;
    }




}

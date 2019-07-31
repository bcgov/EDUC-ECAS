<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Interfaces\iModelRepository;
use App\Rules\SocialInsuranceNumberRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends BaseController
{


    public function index()
    {

        abort('404');

    }


    public function show($id, Request $request)
    {

        // check user is updating their own profile
        $profile = $this->model->get($id);
        $this->checkOwner($request, $profile['federated_id']);


        $profile = $this->model->filter(['federated_id'=> $id])->first();

        return new ProfileResource($profile);
    }


    /*
 * Create the Profile record (Contact in Dynamics)
 */
    public function store(Request $request)
    {

        $api_token = explode(' ', $request->headers->get('Authorization'));

        $user = User::where('api_token', $api_token[1])
            ->get();

        $request = $this->validateProfileRequest($request);
        $data = $request->all();  // TODO - Remove before flight - dangerous
        $data['federated_id'] = $user->id;

        $new_model_id = $this->model->create($data);

        return new ProfileResource($this->model->get($new_model_id));
    }

    /*
     * Update an existing user Profile (Contact in Dynamics)
     */
    public function update($id, Request $request)
    {

        // check user is updating their own profile
        $profile = $this->model->get($id);
        $this->checkOwner($request, $profile['federated_id']);


        $this->validateProfileRequest($request);

        // TODO - Remove before flight - map specific fields
        $data = $request->all();
        $data['federated_id'] = $profile['federated_id'];
        $response = $this->model->update($profile['id'], $data);

        return new ProfileResource($response);
    }


    public function destroy($federated_id)
    {
        abort(401, 'method not available');
        // unauthorized

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
            'first_name'                    => 'required',
            'last_name'                     => 'required',
            'email'                         => 'required|email',
            'phone'                         => 'required',
            'address_1'                     => 'required',
            'city'                          => 'required',
            'region'                        => 'required',
            'postal_code'                   => 'required|regex:/^\D\d\D\s?\d\D\d$/i',
            //'social_insurance_number'       => [ new SocialInsuranceNumberRule ]
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

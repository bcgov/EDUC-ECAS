<?php

namespace App\Http\Requests;


use App\Keycloak\KeycloakGuard;
use Illuminate\Foundation\Http\FormRequest;


class SearchRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        $authentication = resolve(KeycloakGuard::class);
        return $authentication->hasUser();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'q'  => 'required|string|max:80',
        ];
    }
}

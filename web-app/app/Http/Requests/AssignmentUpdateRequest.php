<?php

namespace App\Http\Requests;

use App\Dynamics\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentUpdateRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'action'  => [ 'required' , Rule::in([
                Assignment::APPLIED_STATUS,
                Assignment::ACCEPTED_STATUS,
                Assignment::DECLINED_STATUS,
                Assignment::WITHDREW_STATUS
            ])]
        ];
    }
}

<?php

namespace App\Http\Requests;

class RegisterFreelancerRequest extends StoreFreelancerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array>
     */
    public function rules()
    {
        return parent::rules() + [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ];
    }
}

<?php

namespace App\Http\Requests;

class RegisterHireManagerRequest extends StoreHireManagerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return parent::rules() + [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ];
    }
}

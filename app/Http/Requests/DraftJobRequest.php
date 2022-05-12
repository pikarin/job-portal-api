<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DraftJobRequest extends FormRequest
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
     * @return array<string, array>
     */
    public function rules()
    {
        return [
            'title' => ['required'],
            'description' => ['sometimes', 'nullable'],
            'complexity' => ['sometimes', 'nullable'],
            'duration' => ['sometimes', 'nullable'],
            'payment_amount' => ['sometimes', 'nullable'],
        ];
    }
}

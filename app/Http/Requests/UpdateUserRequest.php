<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user())],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'min:8', 'string', 'confirmed'],
            'institution' => ['nullable', 'string', 'max:255'],
            'seal_number' => ['nullable', 'string', 'max:255'],
            'preferences' => ['nullable','array'],
            'preferences.*' => ['integer', 'exists:preferences,id'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.string' => 'A jelszónak egy karakterláncnak kell lennie.',
            'password.min' => 'Kérjük, válasszon hosszabb jelszót. Minimális karakterszám: 8',
            'email.max' => 'Az e-mail túllépte a megengedett karakterek számát.',
            'email.unique' => 'Ez az e-mail cím már foglalt.',
        ];
    }
}

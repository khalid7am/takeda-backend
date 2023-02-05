<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guest();
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
            'email' => ['required', 'string', 'email:filter', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['required', 'string', 'in:male,female'],
            'institution' => ['nullable', 'string', 'max:255'],
            'seal_number' => ['nullable', 'string', 'max:255'],
            'is_doctor' => ['boolean'],
            'preferences' => ['nullable','array'],
            'preferences.*' => ['integer', 'exists:preferences,id'],
            'is_editor' => ['boolean'],
            'terms' => ['accepted'],
            'declaration' => ['accepted'],
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
            'email.max' => 'Az e-mail túllépte a megengedett karakterek számát.',
            'email.unique' => 'Ez az e-mail cím már foglalt.',
            'email.email' => 'Kérjük, adjon meg egy érvényes e-mailt.',
            'password.string' => 'A jelszónak egy karakterláncnak kell lennie.',
            'password.min' => 'Kérjük, válasszon hosszabb jelszót. Minimális karakterszám: 8',
            'terms.accepted' => 'Kérjük, fogadja el a feltételeket.',
            'declaration.accepted' => 'Kérjük, fogadja el az adatvédelmi szabályzatot.',
        ];
    }
}

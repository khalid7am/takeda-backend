<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', 'string', 'exists:password_resets,token'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'token.exists' => 'A beillesztett kód rossz!',
            'password.string' => 'A jelszónak egy karakterláncnak kell lennie.',
            'password.min' => 'Kérjük, válasszon hosszabb jelszót. Minimális karakterszám: 8',
            'password.confirmed' => 'A jelszavak nem egyeznek.',
        ];
    }
}

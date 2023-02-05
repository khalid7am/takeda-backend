<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterAuthorRequest extends FormRequest
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
            'alkalmazott_tag' => ['required', 'string', 'max:255'],
            'reszvenytulajdon' => ['required', 'string', 'max:255'],
            'eloadoi_dij' => ['required', 'string', 'max:255'],
            'testuleti_reszvetel' => ['required', 'string', 'max:255'],
            'konzultacios_szerzodes' => ['nullable', 'string', 'max:255'],
            'tovabbkepzesi_hozzajarulas' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}

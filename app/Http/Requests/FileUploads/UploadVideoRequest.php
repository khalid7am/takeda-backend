<?php

namespace App\Http\Requests\FileUploads;

use Illuminate\Foundation\Http\FormRequest;

class UploadVideoRequest extends FormRequest
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
            'file' => ['required','file','mimes:mp4,mov,ogg,qt,webm,flv,avi,wmv', 'max:2097152'],
        ];
    }
}

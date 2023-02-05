<?php

namespace App\Http\Requests;

use App\Types\ArticleType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleStoreRequest extends FormRequest
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
            'type' => ['required', 'string', Rule::in(ArticleType::VIDEO, ArticleType::DEMO, ArticleType::LEGO, ArticleType::AUDIO)],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:512'],
            'preferences' => ['required','array'],
            'content' => ['required', 'array'],
            'questions' => ['required', 'array'],
            'media' => [Rule::requiredIf(function (){
                return in_array($this->type, [ArticleType::AUDIO, ArticleType::VIDEO]);
            })],
            'uniqueSlidesId' => ['nullable', 'string'],
            // 'lecturer' => ['required', 'string', 'exists:users,uuid'],
        ];
    }
}

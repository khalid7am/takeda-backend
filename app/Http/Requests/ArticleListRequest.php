<?php

namespace App\Http\Requests;

use App\Types\ArticleType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleListRequest extends FormRequest
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
            'filter' => ['nullable', 'string', Rule::in(ArticleType::VIDEO, ArticleType::DEMO, ArticleType::LEGO, ArticleType::AUDIO, 'new')],
            'tags' => ['nullable', 'exists:preferences,id'],
            'limit' => ['nullable', 'numeric'],
            'order_by' => ['nullable', 'string', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:255'],
            'rating_filter' => ['nullable', 'string', 'in:popular,a_to_z,by_topic,by_type'],
        ];
    }
}

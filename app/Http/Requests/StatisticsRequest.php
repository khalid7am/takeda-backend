<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatisticsRequest extends FormRequest
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
            'users_label' => ['string', 'in:all,new,active,pending'],
            'articles_label' => ['string', 'in:all,great,good,normal,poor,bad'],
            'articletypes_label' => ['string', 'in:lego,demo,video,audio'],
            'article_views_downloads_label' => ['string', 'in:views,finished-quiz,downloads'],
            'comments_label' => ['string', 'in:all,great,good,normal,poor,bad'],
            'admin_label' => ['string', 'in:admin-logins,approved-posts,rejected-posts,updated-comments,deleted-comments,accepted-users,rejected-users,deleted-users'],
            'user_label' => ['string', 'in:taken-quizzes,finished-quizzes,posts-viewed,comments-made,posts-created'],
            'preferenceId' => ['nullable', 'string', 'exists:preferences,slug']
        ];
    }
}

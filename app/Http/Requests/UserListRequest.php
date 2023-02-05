<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
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
            'filter' => ['nullable', 'string', 'in:active,authors,regular,pending,rejected,deleted'],
            'order_by' => ['nullable', 'string', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:255'],
            'only_users' => ['nullable', 'boolean'],
            'admin_filter' => ['nullable', 'string', 'in:all,admins,superadmin,online,suspended,offline'],
        ];
    }
}

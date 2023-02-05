<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthorRequest;
use App\Http\Resources\SimpleUserResource;
use App\Models\Author;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterAuthorController extends Controller
{
    public function __invoke(User $user, RegisterAuthorRequest $request)
    {
        $user->author()->updateOrCreate([], $request->validated());

        return response()->json([
            "success" => true,
            "user" => SimpleUserResource::make($user),
            "is_new_user" => is_null($user->accepted_at) ? true : false,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SimpleUserResource;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->accepted()->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false], 403);
        } else if(!$user->isAdmin()) {
            return response()->json(['success' => false], 401);
        }

        $user->loggedIn();

        return response()->json([
            "success" => true,
            "token" => $user->createToken($request->email . $request->password . uniqid())->plainTextToken,
            "admin" => SimpleUserResource::make($user)
        ]);
    }
}

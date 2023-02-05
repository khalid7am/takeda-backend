<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SimpleUserResource;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request){
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false], 403);
        }
        
        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                "is_accepted" => false,
            ], 200);
        }

        return response()->json([
            "success" => true,
            "token" => $user->createToken($request->email . $request->password . uniqid())->plainTextToken,
            "user" => SimpleUserResource::make($user),
        ]);
    }
}

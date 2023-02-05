<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use App\Notifications\SendPasswordResetCode;
use App\Http\Requests\CheckPasswordResetTokenRequest;
use App\Http\Requests\Auth\RequestPasswordResetRequest;

class PasswordResetController extends Controller
{
    public function request(RequestPasswordResetRequest $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->firstOrFail();

        $user->passwordResets()->delete();
        $user->passwordResets()->create([
            'token' => Str::random(16),
        ]);

        try {
            $user->notify(new SendPasswordResetCode($user));
        }catch (\Exception $exception){
            \Log::error($exception->getMessage());
        }

        return response()->noContent();
    }

    public function checkToken(CheckPasswordResetTokenRequest $request){
        $token = $request->token;
        PasswordReset::where('token', $token)->valid()->firstOrFail();
        return response()->noContent();
    }

    public function reset(ResetPasswordRequest $request)
    {
        $token = $request->token;
        $user = PasswordReset::where('token', $token)->valid()->firstOrFail()->user;
        $user->update([
            'password' => bcrypt($request->password),
        ]);
        $user->passwordResets()->delete();
        return response()->noContent();
    }
}

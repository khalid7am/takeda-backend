<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
use App\Types\RoleType;
use App\Models\Profession;
use Laravolt\Avatar\Facade as AvatarFacade;
use App\Types\ProfessionType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\SimpleUserResource;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $uniqid = uniqid();
        $pathToSave = "avatars/$uniqid.png";
        $path = storage_path("app/public/$pathToSave");

        AvatarFacade::create($request->name)->getImageObject()->save($path, 100);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
            'institution' => $request->institution,
            'seal_number' => $request->seal_number,
            'role_id' => Role::firstWhere('code', '=', ($request->is_editor ? RoleType::EDITOR : RoleType::USER))->getKey(),
            'profession_id' => Profession::firstWhere('code', '=', ($request->has("is_doctor") ? ProfessionType::DOCTOR : ProfessionType::NURSE))->getKey(),
            'profile_picture' => $pathToSave,
        ]);

        $user->preferences()->sync($request->preferences);

        return response()->json([
            "success" => true,
            "uuid" => $user->uuid,
        ]);
    }
}

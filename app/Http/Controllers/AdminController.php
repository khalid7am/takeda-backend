<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserSessionResource;
use App\Http\Resources\AdminActivityLogResource;
use App\Http\Resources\UserActivityLogResource;
use App\Models\User;
use App\Models\Role;
use App\Types\RoleType;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserListRequest;

class AdminController extends Controller
{
    public function me()
    {
        $admin = auth()->user();

        return SimpleUserResource::make($admin);
    }

    public function show(User $admin)
    {
        return SimpleUserResource::make($admin);
    }

    public function sessions(User $admin)
    {
        return UserSessionResource::collection($admin->sessions);
    }

    public function list()
    {
        $admins = User::admins()->accepted()->get();

        return UserListResource::collection($admins);
    }

    public function editors()
    {
        $editors = User::editors()->get();

        return UserListResource::collection($editors);
    }

    public function all(UserListRequest $request)
    {
        $orderBy    = $request->get('order_by', 'asc');
        $filter     = $request->get('admin_filter', 'all');

        $admins = User::query()
                    ->admins()
                     ->orderBy('name', $orderBy);
        
        switch ($filter) {
            case 'all':
                $admins = $admins->withTrashed()->get();
                break;
            case 'admins':
                $admins = $admins->whereHas('role', function($query) {
                    return $query->where('code', RoleType::ADMIN);
                })->get();
                break;
            case 'superadmin':
                $admins = $admins->superadmins()->get();
                break;
            case 'online':
                $admins = $admins->get()->where('is_online', true);
                break;
            case 'suspended':
                $admins = $admins->onlyTrashed()->get();
                break;
            case 'offline':
                $admins = $admins->get()->where('is_online', false);
                break;
        }

        return UserListResource::collection($admins);
    }

    public function update(UpdateAdminRequest $request, User $user)
    {
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->name = $request->name;
        // TODO: SEND A CONFIRMATION EMAIL
        $user->email = $request->email;
        $user->institution = $request->institution;
        $user->seal_number = $request->seal_number;
        $user->is_lecturer = $request->is_lecturer;
        if ($request->is_editor) {
            if ($user->role_id == Role::firstWhere('code', '=', RoleType::USER)->getKey()) {
                $user->role_id = Role::firstWhere('code', '=', RoleType::EDITOR)->getKey();
            }
        } else {
            if ($user->role_id == Role::firstWhere('code', '=', RoleType::EDITOR)->getKey()) {
                $user->role_id = Role::firstWhere('code', '=', RoleType::USER)->getKey();
            }
        }
        $user->save();
        $user->preferences()->sync($request->preferences);

        return SimpleUserResource::make($user);
    }

    public function updateAdmin(UpdateAdminRequest $request, User $user)
    {
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->institution = $request->institution;
        $user->seal_number = $request->seal_number;
        $user->is_lecturer = $request->is_lecturer;
        $user->save();

        return SimpleUserResource::make($user);
    }

    public function activityLog(User $admin)
    {
        return AdminActivityLogResource::make($admin);
    }

    public function activityUser(User $user)
    {
        return UserActivityLogResource::make($user);
    }
}

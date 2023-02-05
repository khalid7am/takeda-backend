<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserListRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserPreferencesRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ArticleListRequest;
use App\Http\Requests\UserResultsRequest;
use App\Http\Resources\AuthorInformationResource;
use App\Models\ArticleUserAnswered;
use App\Types\RoleType;
use App\Models\Role;
use App\Services\RankingService;
use App\Services\UserService;
use Carbon\Carbon;

class UserController extends Controller
{
    public function me()
    {
        $user = auth()->user();

        return SimpleUserResource::make($user);
    }

    public function list(UserListRequest $request)
    {
        $orderBy    = $request->get('order_by', 'asc');
        $filter     = $request->get('filter', 'all');
        $searchTerm = $request->get('search', null);
        $onlyUsers  = $request->get('only_users', false);

        $users = User::query()
                     ->orderBy('name', $orderBy)
                     ->when($searchTerm, fn ($query) => $query->search($searchTerm));

        if ($onlyUsers) {
            $users->notAdmins();
        }

        switch ($filter) {
            case 'all':
                $users->withTrashed();
                break;
            case 'active':
                $users->accepted();
                break;
            case 'authors':
                $users->authors();
                break;
            case 'regular':
                $users->normals();
                break;
            case 'pending':
                $users->pending();
                break;
            case 'rejected':
                $users->rejected();
                break;
            case 'deleted':
                $users->withTrashed()->whereNotNull('deleted_at');
                break;
        }

        $users = $users->get();

        return UserListResource::collection($users);
    }

    public function new(UserListRequest $request)
    {
        $orderBy    = $request->get('order_by', 'asc');
        $searchTerm = $request->get('search', null);

        $users = User::query()
                     ->orderBy('name', $orderBy)
                     ->when($searchTerm, fn ($query) => $query->search($searchTerm));

        $users->pending();

        $users = $users->get();

        return UserListResource::collection($users);
    }

    public function lecturers()
    {
        $lecturers = User::lecturers()->get();

        return UserListResource::collection($lecturers);
    }

    public function show(User $user)
    {
        return SimpleUserResource::make($user);
    }

    public function accept(User $user)
    {
        $user->accept();
        return response()->noContent();
    }

    public function reject(User $user)
    {
        if (auth()->id() !== $user->id) {
            $user->reject();
        }
        return response()->noContent();
    }

    public function pend(User $user)
    {
        if (auth()->id() !== $user->id) {
            $user->pend();
        }
        return response()->noContent();
    }

    public function delete(User $user)
    {
        if (auth()->user()->role->code !== RoleType::SUPERADMIN) {
            return response()->json([
                'success' => false,
            ], 401);
        }

        if (auth()->id() !== $user->id) {
            $user->update([
                'deleter_id' => auth()->id(),
                'accepted_at' => null,
                'rejected_at' => null,
                'accepter_id' => null,
                'rejecter_id' => null,
            ]);
            $user->delete();
        }
        return response()->noContent();
    }

    public function restore(string $userUuid)
    {
        User::withTrashed()->where('uuid', $userUuid)->restore();
        // CHECK THIS ONE
        User::where('uuid', $userUuid)->update([
            'deleter_id' => null,
        ]);
        return response()->noContent();
    }

    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return abort(403, 'Rossz jelszó.');
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        // TODO: SEND A CONFIRMATION EMAIL
        $user->email = $request->email;
        $user->institution = $request->institution;
        $user->seal_number = $request->seal_number;
        $user->save();
        $user->preferences()->sync($request->preferences);

        return SimpleUserResource::make($user);
    }

    public function updatePreferences(UpdateUserPreferencesRequest $request)
    {
        auth()->user()->preferences()->sync($request->preferences);
        return response()->noContent();
    }

    public function status(User $user)
    {
        return response()->json([
            'status' => $user->getStatusText(),
        ]);
    }

    public function articles(User $user, ArticleListRequest $request)
    {
        $type = $request->filter;
        $articles = $user->articles->where('type', $type);
        return ArticleResource::collection($articles);
    }

    public function comments(User $user)
    {
        return CommentResource::collection($user->comments);
    }

    public function search(UserListRequest $request)
    {
        $searchTerm = $request->get('search');

        if ($searchTerm) {
            $user = User::where('email', $searchTerm)->firstOrFail();

            return UserListResource::make($user);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please insert an email!',
        ]);
    }

    public function updateRole(User $user, string $role)
    {
        if (!in_array($role, [RoleType::SUPERADMIN, RoleType::ADMIN, RoleType::EDITOR, RoleType::USER])) {
            return response()->json([
                'success' => false,
                'error' => "Role doesn't exist.",
            ], 404);
        }

        $user->update([
            'role_id' => Role::firstWhere('code', '=', $role)->getKey(),
        ]);

        // TODO: EMIT EVENT TO INFORM THE USER THAT HE BECAME AN ADMIN

        return response()->noContent();
    }


    public function userResults(Request $request)
    {
        $user = auth()->user();

        $usersData = (new UserService)->getUserResults($user);

        return response()->json([
            'success' => true,
            'data' => $usersData
        ]);
    }

    public function authorInformation(User $user)
    {
        $author = $user->author;
        if (!$author) {
            abort(404);
        }

        return AuthorInformationResource::make($author);
    }
}

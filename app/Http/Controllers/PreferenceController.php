<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;
use App\Http\Resources\PreferenceResource;
use App\Http\Resources\PreferencePostsResource;
use App\Http\Requests\PreferenceCreateRequest;
use App\Http\Requests\PreferenceUpdateNameRequest;
use App\Http\Requests\PreferenceUpdateImageRequest;
use App\Http\Requests\RelatedPreferenceRequest;
use App\Http\Resources\PreferenceRelatedResource;
use App\Types\PreferenceType;

class PreferenceController extends Controller
{
    public function list()
    {
        $preferences = Preference::orderBy('name')->get();

        return PreferenceResource::collection($preferences);
    }

    public function all()
    {
        $preferences = Preference::withCount('articles')->get();

        return PreferenceResource::collection($preferences);
    }

    public function articles(Preference $preference)
    {
        return PreferencePostsResource::make($preference);
    }

    public function create(PreferenceCreateRequest $request){
        $preference = Preference::create($request->validated());
        return PreferenceResource::make($preference);
    }

    public function updateName(PreferenceUpdateNameRequest $request, Preference $preference)
    {
        $preference->update($request->validated());
        return PreferenceResource::make($preference);
    }

    public function updateImage(PreferenceUpdateImageRequest $request, Preference $preference)
    {
        $preference->update($request->validated());
        return PreferenceResource::make($preference);
    }

    public function delete(Preference $preference)
    {
        $preference->delete();
        return response()->noContent();
    }

    public function listPreferenceQuizzes(Preference $preference)
    {
        $slugs = $preference->articles->pluck('slug');

        return response()->json([
            "preference" => $preference->name,
            "slugs" => $slugs->shuffle()
        ]);
    }

    public function related()
    {
        $preferences = Preference::whereHas('related')->get();

        return PreferenceRelatedResource::collection($preferences);
    }

    public function updateRelated(RelatedPreferenceRequest $request)
    {
        if (!auth()->user()->is_superadmin) {
            return response()->json('Nem vagy szuperadmin!', 403);
        }

        $preference = Preference::findOrFail($request->related);

        if ($preference->category !== PreferenceType::DIAGNOSES) {
            return response()->json('témaváltoztatás nem megengedett!', 405);
        }

        $preference->related()->sync($request->preferences);

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteRelated(Preference $preference)
    {
        $preference->related()->sync([]);

        return response()->json([
            'success' => true,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends Controller
{
    public function getDisclaimer()
    {
        $disclaimer = Page::where('slug', 'disclaimer')->first();
        if (!$disclaimer) {
            return response()->json(['data' => ['success' => false]]);
        }

        return PageResource::make($disclaimer);
    }

    public function updateDisclaimer(PageRequest $request)
    {
        Page::updateOrCreate(
            ['slug' => 'disclaimer'],
            [ 'name' => $request->name, 'content' => $request->content, 'slug' => 'disclaimer']
        );

        return response()->noContent();
    }
}

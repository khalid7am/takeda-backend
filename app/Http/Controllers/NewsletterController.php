<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request)
    {
        // TODO: SAVE ($request->email) IN THE NEWSLETTER LIST

        return response()->noContent();
    }

    public function unsubscribe(NewsletterRequest $request)
    {
        // TODO: REMOVE ($request->email) FROM THE NEWSLETTER LIST

        return response()->noContent();
    }
}

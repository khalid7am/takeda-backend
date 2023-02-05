<?php

namespace App\Helpers;

use Storage;

class FileDownloadHelper
{
    public static function downloadFile($headers, $path, $filename)
    {
        if (!Storage::disk('public')->exists($path)) {
            abort(404, $filename . ' not found!');
        }

        return response()->download(Storage::disk('public')->path($path), $filename, $headers);
    }
}

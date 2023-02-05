<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadFileRequest;
use Illuminate\Http\Request;
use App\Helpers\FileDownloadHelper;

class FileDownloadController extends Controller
{
    public function downloadAudio(DownloadFileRequest $request)
    {
        $headers = array(
            'Content-Type: audio/ogg',
        );

        $response = FileDownloadHelper::downloadFile($headers, $request->path, $request->filename);

        return $response;
    }

    public function downloadImage(DownloadFileRequest $request)
    {
        $headers = array(
            'Content-Type: image/jpeg',
        );

        $response = FileDownloadHelper::downloadFile($headers, $request->path, $request->filename);

        return $response;
    }
}

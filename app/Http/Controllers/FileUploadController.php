<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\FileDataResource;
use App\Http\Requests\FileUploads\UploadImageRequest;
use App\Http\Requests\FileUploads\UploadVideoRequest;
use App\Http\Requests\FileUploads\UploadAnyTypeRequest;
use App\Http\Requests\FileUploads\UploadDocumentRequest;

class FileUploadController extends Controller
{
    public function document(UploadDocumentRequest $request)
    {
        $folder = uniqid();
        $file = $request->file('file');
        $fileName = Str::slug($file->getClientOriginalName(),'_') . "." . $file->getClientOriginalExtension();
        $path = $file->storePubliclyAs("public/documents/$folder", $fileName);

        $path = Str::replaceFirst("public/","", $path);

        return FileDataResource::make($path);
    }

    public function image(UploadImageRequest $request)
    {
        $folder = uniqid();
        $file = $request->file('file');
        $fileName = Str::slug($file->getClientOriginalName(),'_') . "." . $file->getClientOriginalExtension();
        $path = $file->storePubliclyAs("public/images/$folder", $fileName);

        $path = Str::replaceFirst("public/","", $path);

        return FileDataResource::make($path);
    }

    public function video(UploadVideoRequest $request)
    {
        $folder = uniqid();
        $file = $request->file('file');
        $fileName = Str::slug($file->getClientOriginalName(),'_') . "." . $file->getClientOriginalExtension();
        $path = $file->storePubliclyAs("public/videos/$folder", $fileName);

        $path = Str::replaceFirst("public/","", $path);

        return FileDataResource::make($path);
    }

    public function any(UploadAnyTypeRequest $request)
    {
        $folder = uniqid();
        $file = $request->file('file');
        $fileName = Str::slug($file->getClientOriginalName(),'_') . "." . $file->getClientOriginalExtension();
        $path = $file->storePubliclyAs("public/files/$folder", $fileName);

        $path = Str::replaceFirst("public/","", $path);

        return FileDataResource::make($path);
    }
}

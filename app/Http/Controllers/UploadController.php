<?php

namespace App\Http\Controllers;

use App\Contracts\UploadServiceContract;
use App\Http\Requests\UploadRequest;
use App\Models\Media;
use Illuminate\Support\Facades\Gate;

class UploadController
{
    public function __construct(private UploadServiceContract $uploadService) {}

    public function __invoke(UploadRequest $request)
    {
        Gate::authorize('upload-files');

        $file = $this->uploadService->upload(
            $request->file('file'),
            $request->input('collection')
        );

        Media::create($file->toArray());

        return redirect()->back();
    }
}

<?php

namespace App\Services;

use App\Contracts\UploadServiceContract;
use App\DataObjects\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService implements UploadServiceContract
{
    public function upload(UploadedFile $file, string $collection = null): File
    {
        $name = $file->hashName();
        $path = Storage::put($collection ? "$collection/$name" : $name, $file);

        return new File(
            name: $name,
            originalName: $file->getClientOriginalName(),
            mime: $file->getClientMimeType(),
            path: $path,
            disk: config('filesystems.default'),
            hash: hash_file('sha256', Storage::path($path)),
            collection: $collection,
            size: $file->getSize(),
        );
    }
}

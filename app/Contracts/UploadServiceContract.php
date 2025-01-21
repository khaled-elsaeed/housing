<?php

namespace App\Contracts;

use App\DTOs\File;
use Illuminate\Http\UploadedFile;
use App\Models\Media;


interface UploadServiceContract
{
    public function upload(UploadedFile $file, string $collection = null): Media;
}

<?php

namespace App\Contracts;

use App\DTOs\File;
use Illuminate\Http\UploadedFile;

interface UploadServiceContract
{
    public function upload(UploadedFile $file, string $collection = null): File;
}

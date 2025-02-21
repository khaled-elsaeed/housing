<?php

namespace App\Contracts;

use App\DTOs\File;
use Illuminate\Http\UploadedFile;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;



interface UploadServiceContract
{
    public function upload(UploadedFile $file, ?string $collection = null, Model $model): Media;

}

<?php

namespace App\Services;

use App\Contracts\UploadServiceContract;
use App\DTOs\File;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadService implements UploadServiceContract 
{

    public function upload(UploadedFile $file, string $collection = null): Media
    {
        // Generate the file name using hashName()
        $name = $file->hashName();
    
        // If a collection is provided, use it as a folder in the storage path
        $path = $collection ? "$collection/$name" : $name;
    
    
        // Store the file on the public disk with the constructed path
        $storedPath = Storage::disk('public')->putFileAs('payments', $file, $name);
    
    
        // Generate the absolute path for hashing
        $absolutePath = Storage::disk('public')->path($storedPath);
        
        // Generate file hash
        $fileHash = hash_file('sha256', $absolutePath);
        
        // Create the file DTO
        $fileDto = new File(
            name: $name,
            originalName: $file->getClientOriginalName(),
            mime: $file->getClientMimeType(),
            path: $storedPath, // Store relative path
            disk: 'public', // Explicitly set the disk
            hash: $fileHash, // Use the generated hash
            collection: $collection,
            size: $file->getSize(),
        );
    
        // Create and return the media object
        $media = Media::create($fileDto->toArray());
        
        return $media;
    }
    

}
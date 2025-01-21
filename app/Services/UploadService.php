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
        // Construct the path such that it doesn't duplicate the file name in the path
        $path = $collection ? "$collection/$name" : $name;
    
        // Log the constructed path for debugging
        Log::info('Attempting to store file', ['storage_path' => $path]);
    
        // Store the file on the public disk with the constructed path
        $storedPath = Storage::disk('public')->putFileAs('payments', $file, $name);
    
        // Log the final storage path after uploading
        Log::info('File stored successfully', ['storage_path' => $storedPath]);
    
        // Generate the absolute path for hashing
        $absolutePath = Storage::disk('public')->path($storedPath);
    
        // Log the absolute path
        Log::info('Generated absolute path', ['absolute_path' => $absolutePath]);
    
        // Generate file hash
        $fileHash = hash_file('sha256', $absolutePath);
    
        // Log the file hash
        Log::info('Generated file hash', ['hash' => $fileHash]);
    
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
    
        // Log the media creation
        Log::info('Media record created', ['media_id' => $media->id]);
    
        return $media;
    }
    

}
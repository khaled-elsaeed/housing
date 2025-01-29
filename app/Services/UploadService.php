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
    /**
     * Upload a file to the specified storage disk.
     *
     * @param UploadedFile $file
     * @param string|null $collection
     * @return Media
     */
    public function upload(UploadedFile $file, string $collection = null): Media
    {
        // Generate the file name using hashName()
        $name = $file->hashName();
    
        // If a collection is provided, use it as a folder in the storage path
        $path = $collection ? "$collection/$name" : $name;
    
        // Store the file in the appropriate directory
        if ($collection == 'payments') {
            $storedPath = Storage::disk('public')->putFileAs('payments', $file, $name);
        } else if ($collection == 'profile_picture') {
            $storedPath = Storage::disk('public')->putFileAs('profile_picture', $file, $name);
        } else {
            $storedPath = Storage::disk('public')->putFileAs($collection, $file, $name);
        }
    
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

    /**
     * Delete a file from the storage disk.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            // Check if the file exists
            if (Storage::disk('public')->exists($path)) {
                // Delete the file
                Storage::disk('public')->delete($path);
                return true;
            }

            // Log a warning if the file does not exist
            Log::warning('File not found for deletion:', ['path' => $path]);
            return false;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error deleting file: ' . $e->getMessage(), [
                'path' => $path,
                'exception' => $e,
            ]);

            return false;
        }
    }
}
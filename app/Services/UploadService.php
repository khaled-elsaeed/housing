<?php

namespace App\Services;

use App\Contracts\UploadServiceContract;
use App\DTOs\File;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Exception;

class UploadService implements UploadServiceContract
{
    /**
     * Upload a file to the specified storage disk.
     *
     * @param UploadedFile $file The file to upload.
     * @param string|null $collection The collection/folder to store the file in.
     * @param Model $model The model to associate the uploaded file with.
     * @return Media The created Media instance.
     * @throws Exception If the upload fails.
     */
    public function upload(UploadedFile $file, string $collection = null, Model $model): Media
    {
        try {
            $name = $file->hashName();
            $folder = $collection ?? 'uploads';
            $storedPath = Storage::disk('public')->putFileAs($folder, $file, $name);
            $absolutePath = Storage::disk('public')->path($storedPath);
            $fileHash = hash_file('sha256', $absolutePath);

            $fileDto = new File(
                name: $name,
                originalName: $file->getClientOriginalName(),
                mime: $file->getClientMimeType(),
                path: Storage::url($storedPath),
                disk: 'public',
                hash: $fileHash,
                collection: $collection,
                size: $file->getSize(),
            );

            $media = Media::create($fileDto->toArray());
            $model->media()->save($media);

            return $media;
        } catch (Exception $e) {
            Log::error('Error uploading file', [
                'error' => $e->getMessage(),
                'user_id' => $model->id ?? null,
                'action' => 'upload_file',
            ]);

            throw $e;
        }
    }

/**
 * Delete a file from the storage disk and its associated Media record.
 *
 * @param Media $media The Media instance representing the file to delete.
 * @return bool True if the file and Media record were deleted successfully, false otherwise.
 */
public function delete(Media $media): bool
{
    try {
        $path = $media->path;

        // Delete the file from storage
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        } else {
            Log::warning('File not found for deletion', [
                'media_id' => $media->id,
                'path' => $path,
                'action' => 'delete_file',
            ]);
        }

        $media->delete();
        return true;
    } catch (Exception $e) {
        Log::error('Error deleting file or Media record', [
            'error' => $e->getMessage(),
            'media_id' => $media->id,
            'action' => 'delete_file',
        ]);

        return false;
    }
}
}
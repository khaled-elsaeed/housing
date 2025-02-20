<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaCheckSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Source folder to check
        $sourceFolder = Storage::disk('public')->path('payments');
        
        // Destination folder for not found files
        $notFoundFolder = Storage::disk('public')->path('not_found_media');
        
        // Create the not found folder if it doesn't exist
        if (!File::exists($notFoundFolder)) {
            File::makeDirectory($notFoundFolder, 0755, true);
        }
        
        // Get all files from the source folder
        $files = File::files($sourceFolder);
        
        $this->command->info('Found ' . count($files) . ' files to check.');
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $path = 'payments/' . $filename;
            $fullPath = $file->getPathname();
            $fileHash = hash_file('sha256', $fullPath);
            
            // Check if file exists in media table by path or hash
            $mediaItem = DB::table('media')
                ->where('path', 'like', '%' . $path . '%')
                ->first();
            
            if ($mediaItem) {
                // File found in database, update details if needed
                $this->command->info("File {$filename} found in database. Updating details...");
                
                // Get file details
                $size = $file->getSize();
                $mimeType = File::mimeType($fullPath);
                
                // Update only if details have changed
                if ($mediaItem->size != $size || $mediaItem->mime_type != $mimeType) {
                    DB::table('media')
                        ->where('id', $mediaItem->id)
                        ->update([
                            'size' => $size,
                            'mime_type' => $mimeType,
                            'updated_at' => now()
                        ]);
                    
                    $this->command->info("Updated details for {$filename}");
                } else {
                    $this->command->info("No changes needed for {$filename}");
                }
            } else {
                // File not found in database, move to not found folder and delete from original location
                $this->command->warn("File {$filename} not found in database. Moving to not found folder...");
                
                // Copy file to not found folder
                File::copy($fullPath, $notFoundFolder . '/' . $filename);
                
                // Delete the file from payments folder after successful copy
                if (File::exists($notFoundFolder . '/' . $filename)) {
                    File::delete($fullPath);
                    $this->command->info("File {$filename} successfully deleted from payments folder.");
                } else {
                    $this->command->error("Failed to copy {$filename}. File not deleted from payments folder.");
                }
                
                // Log this action
                $this->logNotFoundFile($filename, $path, $fileHash);
            }
        }
        
        $this->command->info('Media check completed successfully.');
    }
    
    /**
     * Log not found file details for reference
     *
     * @param string $filename
     * @param string $path
     * @param string $fileHash
     * @return void
     */
    private function logNotFoundFile($filename, $path, $fileHash)
    {
        $logPath = storage_path('logs/not_found_media.log');
        $logEntry = "[" . now() . "] File not found in database: {$filename}, Path: {$path}, Hash: {$fileHash}\n";
        File::append($logPath, $logEntry);
    }
}
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
        // Source and destination folders
        $sourceFolder = Storage::disk('public')->path('payments');
        $notFoundFolder = Storage::disk('public')->path('not_found_media');

        // Ensure the not_found folder exists
        if (!File::exists($notFoundFolder)) {
            File::makeDirectory($notFoundFolder, 0755, true);
        }

        // Get all files from the source folder
        $files = File::files($sourceFolder);
        $this->command->info("\n🔍 Found " . count($files) . " files to check.\n");

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $path = 'payments/' . $filename;
            $fullPath = $file->getPathname();
            $fileHash = hash_file('sha256', $fullPath); 

            $this->command->info("🔎 Checking: {$filename}");

            
            $mediaItem = DB::table('media')
                ->where(function ($query) use ($path, $fileHash) {
                    $query->where('path', 'like', '%' . $path . '%')
                          ->orWhere('file_hash', $fileHash);
                })
                ->first();

            if ($mediaItem) {
                // File found in database, update details if needed
                $this->command->info("✅ File found in database (ID: {$mediaItem->id})");

                // Get file details
                $size = $file->getSize();
                $mimeType = File::mimeType($fullPath);

                // Update missing hash if not present in the database
                if (empty($mediaItem->file_hash)) {
                    DB::table('media')
                        ->where('id', $mediaItem->id)
                        ->update(['file_hash' => $fileHash, 'updated_at' => now()]);

                    $this->command->warn("🛠 Hash updated for {$filename}");
                }

                // Update file details if changed
                if ($mediaItem->size != $size || $mediaItem->mime_type != $mimeType) {
                    DB::table('media')
                        ->where('id', $mediaItem->id)
                        ->update([
                            'size' => $size,
                            'mime_type' => $mimeType,
                            'updated_at' => now()
                        ]);

                    $this->command->info("📝 Updated details: Size - {$size} bytes | MIME Type - {$mimeType}");
                } else {
                    $this->command->info("✅ No changes needed for {$filename}");
                }
            } else {
                // File not found in database, move to `not_found_media`
                $this->command->warn("❌ File not found in database. Moving to 'not_found_media'...");

                // Copy file to not found folder
                File::copy($fullPath, $notFoundFolder . '/' . $filename);

                // Delete original file after successful copy
                if (File::exists($notFoundFolder . '/' . $filename)) {
                    File::delete($fullPath);
                    $this->command->info("📁 Moved: {$filename} -> not_found_media/");
                } else {
                    $this->command->error("⚠️ Failed to move {$filename}.");
                }

                // Log the missing file details
                $this->logNotFoundFile($filename, $path, $fileHash);
            }

            $this->command->line(str_repeat('-', 50)); // Separator for better readability
        }

        $this->command->info("\n✅ Media check completed successfully.\n");
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
        $logEntry = "[" . now() . "] ❌ File not found in database: {$filename}, Path: {$path}, Hash: {$fileHash}\n";
        File::append($logPath, $logEntry);
    }
}

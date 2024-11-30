<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteDuplicatedPicturesSeeder extends Seeder
{
    public function run()
    {
        // Step 1: Find the user_ids that have duplicates (more than one record)
        $duplicateUsers = DB::table('documents') // Assuming 'documents' is your table name
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1') // Find users with more than one document
            ->get();

        // Step 2: Loop through each user_id with duplicates
        foreach ($duplicateUsers as $user) {
            // Get all documents for the user
            $documents = DB::table('documents')
                ->where('user_id', $user->user_id)
                ->orderBy('created_at') // You can change to another field if needed (like 'id')
                ->get();

            // Step 3: Keep the first document and delete the others
            $firstDocument = $documents->first(); // Keep the first record (or change logic if needed)

            // Loop through the documents and delete the duplicates
            foreach ($documents as $document) {
                // Skip the first document to keep it
                if ($document->id !== $firstDocument->id) {
                    // Delete the document from the database
                    DB::table('documents')
                        ->where('id', $document->id)
                        ->delete();

                    // Step 4: Delete the file from storage (public disk)
                    $filePath = $document->document_path; // Assuming document_path is relative to storage/app/public

                    if (Storage::disk('public')->exists($filePath)) {
                        // Delete the file from storage
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        }
    }
}


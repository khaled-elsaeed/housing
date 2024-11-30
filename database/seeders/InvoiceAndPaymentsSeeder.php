<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\UniversityArchive;
use App\Models\Reservation;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceAndPaymentsSeeder extends Seeder
{
    public function run(): void
    {
        Log::info("Starting the Invoice and Payments Seeder...");

        // Create or fetch the 'resident' role
        $residentRole = Role::firstOrCreate(['name' => 'resident']);
        Log::info("Resident role created or fetched.");

        // Load the spreadsheet
        $filePath = database_path('data/reservations.csv');
        Log::info("Loading spreadsheet from: $filePath");

        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $userNationalLinkData = [];
        $rowCount = $sheet->getHighestRow();
        Log::info("Total rows to process: $rowCount");

        // Process each row
        foreach ($sheet->getRowIterator(2) as $row) {
            $data = $this->getRowData($row);
            Log::info("Processing row with data: " . implode(", ", $data));

            // Check if the user already exists in the university archive and has a reservation
            $existingUser = $this->findExistingUser($data);

            if ($existingUser) {
                Log::info("Found existing user with ID: {$existingUser->id}");

                $reservation = Reservation::where('user_id', $existingUser->id)
                    // Assuming active reservations are needed
                    ->first();

                if ($reservation) {
                    // Check if the invoice already exists for the reservation
                    if ($this->invoiceExists($reservation->id)) {
                        continue; // Skip if invoice already exists
                    }

                    // Create invoice and payment for the user with a valid reservation
                    $this->createInvoiceAndPayment($reservation->id, $data);
                } else {
                    Log::warning("No active reservation found for user ID: {$existingUser->id}");
                }
            } else {
                Log::warning("No existing user found for national_id: {$data[5]}");
            }
        }

        // Insert user-national link data if any
        if (!empty($userNationalLinkData)) {
            DB::table('user_national_link')->insert($userNationalLinkData);
            Log::info("User national link data inserted.");
        }
    }

    /**
     * Check if an invoice already exists for the given reservation ID.
     *
     * @param int $reservationId
     * @return bool
     */
    public function invoiceExists(int $reservationId): bool
    {
        return Invoice::where('reservation_id', $reservationId)->exists();
    }

    /**
     * Load the spreadsheet from the given file path.
     *
     * @param string $filePath
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private function loadSpreadsheet(string $filePath)
    {
        Log::info("Loading spreadsheet...");
        return IOFactory::load($filePath);
    }

    /**
     * Extract data from a row in the spreadsheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Row $row
     * @return array
     */
    private function getRowData($row): array
    {
        $cells = $row->getCellIterator();
        $cells->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cells as $cell) {
            $data[] = $cell->getValue();
        }

        return $data;
    }

    /**
     * Find an existing user by their national ID.
     *
     * @param array $data
     * @return User|null
     */
    private function findExistingUser(array $data)
    {
        Log::info("Searching for user with national_id: {$data[5]}");

        // Correct method call
        $user = User::getUserByNationalId($data[5]);

        if ($user) {
            Log::info("User found for national_id: {$data[5]}");
            return $user;
        }

        Log::warning("No user found for national_id: {$data[5]}");
        return null; // Return null if no user exists
    }

    /**
     * Create an invoice and payment for a reservation.
     *
     * @param int $reservationId
     * @param array $data
     */
    public function createInvoiceAndPayment(int $reservationId, array $data)
{
    Log::info("Creating invoice and payment for reservation ID: $reservationId");

    DB::beginTransaction();

    try {
        // Check if a receipt image is provided
        $receiptImage = isset($data[99]) ? $data[99] : null;
        $paidAt = isset($data[101]) ? $data[101] : null;

        // Determine the invoice status
        $invoiceStatus = ($receiptImage !== "NULL" && $receiptImage !== null) ? 'paid' : 'unpaid';

        // Create the fee invoice
        $feeInvoice = DB::table('invoices')->insertGetId([
            'reservation_id' => $reservationId,
            'amount' => 15000,  // Example amount, adjust as needed
            'status' => $invoiceStatus,  // Set the invoice status based on receipt presence
            'category' => 'fee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If the receipt image is provided, create the payment record
        if ($receiptImage !== "NULL" && $receiptImage !== null) {
            $paymentId = DB::table('payments')->insertGetId([
                'reservation_id' => $reservationId,
                'status' => 'pending',  // Mark payment as paid
                'receipt_image' => $receiptImage,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info("Payment created with ID: $paymentId");
        } else {
            Log::info("No receipt image provided. Skipping payment creation.");
        }

        DB::commit();
        Log::info("Transaction committed successfully.");
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating invoice and payment: ' . $e->getMessage());
        throw $e;
    }
}

}

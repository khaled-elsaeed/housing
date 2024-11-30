<?php

namespace App\Exports\Invoices;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicesExport
{
    public function downloadExcel()
    {
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Invoice ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'National ID');
        $sheet->setCellValue('D1', 'Faculty');
        $sheet->setCellValue('E1', 'Mobile');
        $sheet->setCellValue('F1', 'Invoice Status');
        $sheet->setCellValue('G1', 'Payment Status');

        // Fetch invoices with related data
        $invoices = Invoice::with(['reservation.user.student', 'reservation.payment'])
            ->orderBy('status', 'desc')
            ->get();

        // Fill rows with data
        $row = 2; // Start at row 2, since row 1 contains headers
        foreach ($invoices as $invoice) {
            $invoiceStatus = $invoice ? ($invoice->status == 'paid' ? 'Paid' : 'Unpaid') : 'No Invoice';
            $paymentStatus = optional($invoice->reservation->payment)->status ?? 'No Payment';

            $sheet->setCellValue('A' . $row, $invoice->id);
            $sheet->setCellValue('B' . $row, optional($invoice->reservation->user->student)->name_en ?? 'N/A');
            $sheet->setCellValue('C' . $row, optional($invoice->reservation->user->student)->national_id ?? 'N/A');
            $sheet->setCellValue('D' . $row, optional($invoice->reservation->user->student->faculty)->name_en ?? 'N/A');
            $sheet->setCellValue('E' . $row, optional($invoice->reservation->user->student)->mobile ?? 'N/A');
            $sheet->setCellValue('F' . $row, $invoiceStatus);
            $sheet->setCellValue('G' . $row, $paymentStatus);

            $row++;
        }

        // Set headers for file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="invoices.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        // Fetch invoices with related data
        $invoices = Invoice::with(['reservation.user.student', 'reservation.payment'])
            ->orderBy('status', 'desc')
            ->get();

        // Generate PDF using a view
        $pdf = Pdf::loadView('reports.invoices_report', compact('invoices'));
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF as a download
        return $pdf->download('invoices_report.pdf');
    }
}

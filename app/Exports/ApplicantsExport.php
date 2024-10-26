<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User; 
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicantsExport
{
    public function download()
    {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headings
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'username_ar');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'created_at');
        // Fetch data from the database
        $applicants = User::all(); // Adjust this to fetch the data you need

        // Populate the spreadsheet with applicant data
        $row = 2; // Start from the second row (after headings)
        foreach ($applicants as $applicant) {
            $sheet->setCellValue('A' . $row, $applicant->id);
            $sheet->setCellValue('B' . $row, $applicant->username_ar);
            $sheet->setCellValue('C' . $row, $applicant->email);
            $sheet->setCellValue('D' . $row, $applicant->created_at);
            $row++;
        }

        // Set the response headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="applicants.xlsx"');
        header('Cache-Control: max-age=0');

        // Write the file and output to the browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportToPDF()
    {
        // Fetch all applicants; customize the query if needed
        $applicants = User::all();

        // Load the Blade view and pass the data to it
        $pdf = Pdf::loadView('reports.applicants_report', compact('applicants'));

        // Set paper size and orientation if desired (optional)
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF object to the caller
        return $pdf;
    }
}

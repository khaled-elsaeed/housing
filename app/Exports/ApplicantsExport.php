<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicantsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headings
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Created At');

        // Fetch and populate data
        $applicants = User::all();
        $row = 2;
        foreach ($applicants as $applicant) {
            $sheet->setCellValue("A{$row}", $applicant->id);
            $sheet->setCellValue("B{$row}", $applicant->username_ar);
            $sheet->setCellValue("C{$row}", $applicant->email);
            $sheet->setCellValue("D{$row}", $applicant->created_at);
            $row++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="applicants.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        $applicants = User::all();
        $pdf = Pdf::loadView('reports.applicants_report', compact('applicants'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }
}

<?php

namespace App\Exports\Applicants;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicantDocumentsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'National ID');
        $sheet->setCellValue('C1', 'Faculty');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Mobile');

        $applicants = User::whereHas('documents') 
                         ->role('resident') 
                         ->get();

        $row = 2;
        foreach ($applicants as $applicant) {
            $sheet->setCellValue("A{$row}", $applicant->student->name_en ?? 'N/A');
            $sheet->setCellValue("B{$row}", $applicant->student->national_id ?? 'N/A');
            $sheet->setCellValue("C{$row}", $applicant->student->faculty->name_en ?? 'N/A');
            $sheet->setCellValue("D{$row}", $applicant->email ?? 'N/A');
            $sheet->setCellValue("E{$row}", $applicant->student->mobile ?? 'N/A');
            $row++;
        }

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
        // Get only users who have uploaded documents (assuming there's a "documents" relationship)
        $applicants = User::whereHas('documents')->get(); // Filter only users with documents
        $pdf = Pdf::loadView('reports.applicants_report', compact('applicants'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }
}


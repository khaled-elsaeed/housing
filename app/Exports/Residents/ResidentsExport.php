<?php

namespace App\Exports\Residents;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ResidentsExport
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

        $residents = User::role('resident')
            ->whereHas('student', function ($query) {
                $query->whereIn('application_status', ['final_accepted']);
            })
            ->get();

        $row = 2;
        foreach ($residents as $resident) {
            $sheet->setCellValue("A{$row}", $resident->student->name_en ?? 'N/A');
            $sheet->setCellValue("B{$row}", $resident->student->national_id ?? 'N/A');
            $sheet->setCellValue("C{$row}", $resident->student->faculty->name_en ?? 'N/A');
            $sheet->setCellValue("D{$row}", $resident->email ?? 'N/A');
            $sheet->setCellValue("E{$row}", $resident->student->mobile ?? 'N/A');
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="residents.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        $residents = User::role('resident')
            ->whereHas('student', function ($query) {
                $query->whereIn('application_status', ['pending', 'preliminary_accepted']);
            })
            ->get();

        $pdf = Pdf::loadView('reports.residents_report', compact('residents'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('residents_report.pdf');
    }
}

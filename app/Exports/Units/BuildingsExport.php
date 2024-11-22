<?php

namespace App\Exports\Units;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Building;
use Barryvdh\DomPDF\Facade\Pdf;

class BuildingsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'Building Number');
        $sheet->setCellValue('B1', 'Gender');
        $sheet->setCellValue('C1', 'Max Apartments');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Description');

        // Fetch and populate building data
        $buildings = Building::all();
        $row = 2;
        foreach ($buildings as $building) {
            $sheet->setCellValue("A{$row}", $building->number ?? 'N/A');
            $sheet->setCellValue("B{$row}", ucfirst($building->gender) ?? 'N/A');
            $sheet->setCellValue("C{$row}", $building->max_apartments ?? 'N/A');
            $sheet->setCellValue("D{$row}", ucfirst(str_replace('_', ' ', $building->status)) ?? 'N/A');
            $sheet->setCellValue("E{$row}", $building->note ?? 'No description available');
            $row++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="buildings.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        // Fetch building data
        $buildings = Building::all();

        // Generate PDF using a view
        $pdf = Pdf::loadView('reports.buildings_report', compact('buildings'));
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('buildings.pdf');
    }
}

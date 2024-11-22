<?php

namespace App\Exports\Units;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Apartment;
use Barryvdh\DomPDF\Facade\Pdf;

class ApartmentsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'Apartment Number');
        $sheet->setCellValue('B1', 'Building Number');
        $sheet->setCellValue('C1', 'Max Rooms');
        $sheet->setCellValue('D1', 'Occupancy Status');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Description');

        // Fetch and populate apartment data
        $apartments = Apartment::with('building')->get();
        $row = 2;
        foreach ($apartments as $apartment) {
            $sheet->setCellValue("A{$row}", $apartment->number ?? 'N/A');
            $sheet->setCellValue("B{$row}", $apartment->building->number ?? 'N/A');
            $sheet->setCellValue("C{$row}", $apartment->max_rooms ?? 'N/A');
            $sheet->setCellValue("D{$row}", ucfirst($apartment->occupancy_status) ?? 'N/A');
            $sheet->setCellValue("E{$row}", ucfirst(str_replace('_', ' ', $apartment->status)) ?? 'N/A');
            $sheet->setCellValue("F{$row}", $apartment->description ?? 'No description available');
            $row++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="apartments.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        // Fetch apartment data
        $apartments = Apartment::with('building')->get();

        // Generate PDF using a view
        $pdf = Pdf::loadView('reports.apartments_report', compact('apartments'));
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('apartments.pdf');
    }
}

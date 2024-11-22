<?php

namespace App\Exports\Units;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;

class RoomsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'Room Number');
        $sheet->setCellValue('B1', 'Apartment Number');
        $sheet->setCellValue('C1', 'Building Number');
        $sheet->setCellValue('D1', 'Max Occupancy');
        $sheet->setCellValue('E1', 'Current Occupancy');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Type');
        $sheet->setCellValue('H1', 'Purpose');

        // Fetch and populate room data
        $rooms = Room::with('apartment.building')->get();
        $row = 2;
        foreach ($rooms as $room) {
            $sheet->setCellValue("A{$row}", $room->number ?? 'N/A');
            $sheet->setCellValue("B{$row}", $room->apartment->number ?? 'N/A');
            $sheet->setCellValue("C{$row}", $room->apartment->building->number ?? 'N/A');
            $sheet->setCellValue("D{$row}", $room->max_occupancy ?? 'N/A');
            $sheet->setCellValue("E{$row}", $room->current_occupancy ?? 'N/A');
            $sheet->setCellValue("F{$row}", ucfirst($room->status) ?? 'N/A');
            $sheet->setCellValue("G{$row}", ucfirst($room->type) ?? 'N/A');
            $sheet->setCellValue("H{$row}", ucfirst($room->purpose) ?? 'N/A');
            $row++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rooms.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        // Fetch room data
        $rooms = Room::with('apartment.building')->get();

        // Generate PDF using a view
        $pdf = Pdf::loadView('reports.rooms_report', compact('rooms'));
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('rooms.pdf');
    }
}

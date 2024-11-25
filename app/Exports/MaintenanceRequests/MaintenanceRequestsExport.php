<?php

namespace App\Exports\MaintenanceRequests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\MaintenanceRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceRequestsExport
{
    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the headers for the Excel sheet
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Student Name');
        $sheet->setCellValue('C1', 'Location');
        $sheet->setCellValue('D1', 'Issue Type');
        $sheet->setCellValue('E1', 'Description');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Updated At');

        $maintenanceRequests = MaintenanceRequest::with('user', 'room')->get();
        $row = 2;
        foreach ($maintenanceRequests as $request) {
            $location = $request->room ? $request->room->getLocation() : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];
            $sheet->setCellValue("A{$row}", $row - 1); 
            $sheet->setCellValue("B{$row}", $request->user->getUsernameEnAttribute() ?? 'N/A');
            $sheet->setCellValue("C{$row}", "Building {$location['building']} - Apartment {$location['apartment']} - Room {$location['room']}");
            $sheet->setCellValue("D{$row}", $request->issue_type ?? 'Unknown');
            $sheet->setCellValue("E{$row}", $request->description ?? 'No Description');
            $sheet->setCellValue("F{$row}", $request->status); 
            $sheet->setCellValue("G{$row}", $request->created_At->format('d M Y, h:i A') ?? 'N/A');
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="maintenance_requests.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadPDF()
    {
        $maintenanceRequests = MaintenanceRequest::with('user', 'room')->get();
        $pdf = Pdf::loadView('reports.maintenance_requests_report', compact('maintenanceRequests'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

   
}

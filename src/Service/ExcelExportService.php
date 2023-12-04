<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public function exportStationsToExcel(array $stations): void
    {
        // Créez un objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Ajoutez des données à la feuille de calcul
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nom de la Station');
        $sheet->setCellValue('B1', 'Emplacement');
        $sheet->setCellValue('C1', 'Description');

        foreach ($stations as $index => $station) {
            $row = $index + 2; // Commencez à la ligne 2
            $sheet->setCellValue('A' . $row, $station->getNomS());
            $sheet->setCellValue('B' . $row, $station->getEmplacementS());
            $sheet->setCellValue('C' . $row, $station->getDescriptionS());
        }

        // Créez un objet Writer pour exporter en fichier Excel
        $writer = new Xlsx($spreadsheet);

        // Définissez le chemin du fichier Excel
        $filePath = 'export_stations.xlsx';

        // Enregistrez le fichier Excel sur le serveur
        $writer->save($filePath);
    }
}
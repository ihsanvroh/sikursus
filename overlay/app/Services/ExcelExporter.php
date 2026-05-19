<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Helper untuk generate Excel (.xlsx) dari array of rows.
 */
class ExcelExporter {

    /**
     * Generate streamed Xlsx download response.
     *
     * @param string $filename  e.g. "laporan-2026-05.xlsx"
     * @param array  $headers   ["Tanggal","Siswa","Nominal", ...]
     * @param array  $rows      [["2026-05-01","Andi",150000], ...]
     * @param string $title     judul yang muncul di row pertama (optional)
     */
    public function download(string $filename, array $headers, array $rows, string $title = ''): StreamedResponse {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');

        $startRow = 1;
        if ($title !== '') {
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:'.$this->columnLetter(count($headers)).'1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $startRow = 3;
        }

        // Header row
        foreach ($headers as $i => $h) {
            $col = $this->columnLetter($i + 1);
            $cell = $col . $startRow;
            $sheet->setCellValue($cell, $h);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');
        }

        // Data rows
        foreach ($rows as $ri => $row) {
            $rowNum = $startRow + 1 + $ri;
            foreach (array_values($row) as $ci => $value) {
                $col = $this->columnLetter($ci + 1);
                $sheet->setCellValueExplicitByColumnAndRow(
                    $ci + 1, $rowNum,
                    is_numeric($value) && !str_starts_with((string)$value, '0') ? (float)$value : (string)$value,
                    is_numeric($value) && !str_starts_with((string)$value, '0')
                        ? \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                        : \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
        }

        // Auto-size kolom
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension($this->columnLetter($i))->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function columnLetter(int $n): string {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($n);
    }
}

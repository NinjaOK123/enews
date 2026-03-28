<?php
require 'vendor/autoload.php';
$file = 'E:/laragon/www/enews/NhuanBut_2.2026_[2026-03-12_09.38.07].xlsx';
try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    echo "Sheets: " . implode(', ', $spreadsheet->getSheetNames()) . "\n\n";
    foreach ($spreadsheet->getSheetNames() as $index => $name) {
        echo "=== Sheet: $name ===\n";
        $sheet = $spreadsheet->getSheet($index);
        $data = $sheet->toArray();
        for ($i = 0; $i < min(20, count($data)); $i++) {
            echo implode(" | ", array_map(function($v) { return substr((string)$v, 0, 50); }, $data[$i])) . "\n";
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}

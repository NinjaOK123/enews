<?php
require __DIR__.'/vendor/autoload.php';

$filepath = 'D:\laragon\www\enews\NhuanBut_2.2026_[2026-03-12_09.38.07].xlsx';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($filepath);

$sheet = $spreadsheet->getSheet(1);
echo "Sheet Title: " . $sheet->getTitle() . "\n";
$maxCol = $sheet->getHighestColumn();
$maxRow = min(30, $sheet->getHighestRow());
$data = $sheet->rangeToArray('A1:' . $maxCol . $maxRow, null, true, true, true);

foreach ($data as $rowNum => $rowData) {
    if (count(array_filter($rowData)) === 0) continue;
    echo "Row $rowNum: ";
    $cells = [];
    foreach ($rowData as $col => $val) {
        if ($val !== null && $val !== '') {
            $cells[] = "$col: $val";
        }
    }
    echo implode(" | ", $cells) . "\n";
}

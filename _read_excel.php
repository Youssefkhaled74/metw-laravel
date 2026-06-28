<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/database/sql/Governorates & Cities.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

foreach ($data as $i => $row) {
    echo 'Row ' . $i . ': ' . implode(' | ', $row) . PHP_EOL;
}

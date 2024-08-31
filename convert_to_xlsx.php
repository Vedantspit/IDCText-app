<?php


require 'vendor/autoload.php';

$tableData = $_POST['table_data'];
$studyId = $_POST['sid']; 


$dom = new DOMDocument();
$dom->loadHTML($tableData, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);


$table = $dom->getElementsByTagName('table')[0];


$worksheet = XLSX::documentobject_to_sheet($table);


$workbook = new XLSXWriter();
$workbook->addSheet($worksheet);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=all_results_' . $studyId . '.xlsx');


$workbook->write('php://output');

exit;


<?php

$data = array(
    array('Name', 'Age', 'Location'),
    array('John Doe', 30, 'New York'),
    array('Jane Smith', 25, 'Los Angeles')
);

$csvContent = '';
foreach ($data as $row) {
    $csvContent .= implode(',', $row) . "\n";
}


header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="all_results.csv"');


echo $csvContent;
?>

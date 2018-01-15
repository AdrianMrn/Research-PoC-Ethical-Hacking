<?php
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="data.csv";');
$data = file_get_contents('formdata.txt');

print($data);
?>

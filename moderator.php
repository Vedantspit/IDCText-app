<?php
require 'config/_dbconnect1.php';

date_default_timezone_set("Asia/Bangkok");
$sid = "1";//$date = "all";
if (isset($_GET['studyid'])) {
    $sid = $_GET['studyid'];
}
if(strlen($sid)==0)
    $sid = "1";

mysqli_set_charset($conn, "utf8");

$tablePrefix = "<table><tr><th colspan='1'>STUDY ID</th><th colspan='1'>USER ID</th><th colspan='1'>Session URL</th><th colspan='1'>Results</th></tr>";
$tableSuffix = "</table>";
$tableBody="";

$query = "SELECT DISTINCT userid from nusers where studyid='$sid'";
$result = mysqli_query($conn, $query);
if ($result === false)
    die("Query failed: " . mysqli_error($conn).$query);

while ($row = mysqli_fetch_array($result)) {
    $uid = $row[0];
    $baseUrl = "main.php?userid=$uid&studyid=$sid&p=&s=4";
    $results = "modresult.html?study_id=$sid";

    // Fetching p and s values
    $pQuery = "SELECT nmph, MAX(sessions) m FROM nusers WHERE userid='$uid' AND studyid='$sid' LIMIT 1";
    $pResult = mysqli_query($conn, $pQuery);
    if ($pResult === false)
        die("Query failed: " . mysqli_error($conn).$pQuery);
    $pRow = mysqli_fetch_assoc($pResult);
    $p = $pRow['nmph'];
    $s = $pRow['m'];
    $baseUrl = "main.php?userid=$uid&studyid=$sid&p=$p&s=$s";
    $tableBody .= "\n<tr>";
    $tableBody .= "<td colspan='1'>" . $sid . "</td>";
    $tableBody .= "<td colspan='1'>" . $uid . "</td>";
    $tableBody .= "<td colspan='1'><a href='$baseUrl'>USER URL</a></td>";
    $tableBody .= "<td colspan='1'><a href='$results'>Results</a></td>";
    $tableBody .= "</tr>\n";
}
mysqli_close($conn);
echo $tablePrefix.$tableBody.$tableSuffix;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Micro+5+Charted&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Teko:wght@300..700&display=swap" rel="stylesheet">
   
</head>
<body>
    
</body>
</html>
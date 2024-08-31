<?php
require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Bangkok");
$uid = '1';
$sid = '1';
if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}
if (isset($_POST['userid'])) {
    $uid = $_POST['userid'];
}

if (isset($_POST['currsesh'])) {
    $currsesh = $_POST['currsesh'];
}
if (isset($_POST['kboard'])) {
    $kboard = $_POST['kboard'];
}

if (strlen($uid) == 0)
    $uid = "1";
if (strlen($sid) == 0)
    $sid = "1";

mysqli_set_charset($conn, "utf8");

$query = "SELECT phraseTyped, phraseShown, editdistance3, typingTime, backspace, IncorrNF3, IncorrF, Correct3, Fixed, round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM nusers where userid='$uid' AND studyid='$sid' AND sessions=$currsesh AND keyboard='$kboard'";

$result = mysqli_query($conn, $query);
if ($result === false)
    die("Query failed: " . mysqli_error($conn));

$totalErrorRate = 0;
$minErrorRate = PHP_INT_MAX;
$maxErrorRate = 0;
$totalCPM = 0;
$minCPM = PHP_INT_MAX;
$maxCPM = 0;
$count = 0;
$cpm = 0;

while ($row = mysqli_fetch_array($result)) {
    //print_r($row);
    $phraseTyped = $row['phraseTyped'];
   // echo "phrase typed is $phraseTyped and count is $count ";
    $maxStrLen = $row['maxLen'];
    $minStrLen = $row['minLen'];

    // Calculate error rate
    if ($maxStrLen > 0) {
        if ($row['editdistance3'] > $minStrLen) {
            $errorRate = 100;
        } else {
            $errorRate = round(($row['editdistance3'] / $maxStrLen) * 100, 2);
        }
    } else {
        $errorRate = 0;
    }

    // Update error rate statistics
    $minErrorRate = min($minErrorRate, $errorRate);
    $maxErrorRate = max($maxErrorRate, $errorRate);
    $totalErrorRate += $errorRate;

    // Update CPM statistics
    $cpm = $row['cpm'];
    $minCPM = min($minCPM, $cpm);
    $maxCPM = max($maxCPM, $cpm);
    $totalCPM += $cpm;

    $count++;
}

$averageErrorRate = $totalErrorRate / $count;
$averageCPM = $totalCPM / $count;

$response = [
    'averageErrorRate' => round($averageErrorRate),
    'minErrorRate' => $minErrorRate,
    'maxErrorRate' => $maxErrorRate,
    'averageCPM' => round($averageCPM, 2),
    'minCPM' => $minCPM,
    'maxCPM' => $maxCPM,
    'count' => $count,
    'keyboard' => $kboard,
    'currsesh' => $currsesh,
    'total cpm' => $totalCPM
];

echo json_encode($response);
?>

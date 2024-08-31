<?php

require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Kolkata");
$uid = '1';
$sid = '1';
$currKeyboard = '';

if (isset($_POST['userid'])) {
    $uid = $_POST['userid'];
}

if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}
if (isset($_POST['keyboard'])) {
    $currKeyboard = $_POST['keyboard'];
}
$count1=0;
$query = "SELECT sessions,keyboard,kbseq, phraseTyped, phraseShown, editdistance3, typingTime, backspace, IncorrNF3, IncorrF, Correct3, Fixed, round((char_length(phraseTyped)-1)/(typingTime/60000.0)) AS cpm, least(char_length(phraseTyped),char_length(phraseShown)) AS minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) AS maxLen, typedDate FROM nusers 
WHERE userid='$uid' 
AND studyid='$sid' 
ORDER BY kbseq DESC,sessions DESC"; 


$result = mysqli_query($conn, $query);
if ($result === false) {
    die("Query failed: " . mysqli_error($conn));
}

$sessionStatistics = [];

$keyboardStatistics = [];

while ($row = mysqli_fetch_array($result)) {
    $count1++;
    $phraseTyped = $row['phraseTyped'];

    if ($phraseTyped == '') {
        continue;
    }

    $session = $row['sessions'];
    $keyboard = $row['keyboard'];

    // If statistics for this session and keyboard type don't exist, initialize them
    if (!isset($keyboardStatistics[$session][$keyboard])) {
        $keyboardStatistics[$session][$keyboard] = [
            'minCPM' => PHP_INT_MAX,
            'maxCPM' => 0,
            'totalCPM' => 0,
            'totalCount' => 0,
            'minErrorRate' => PHP_INT_MAX,
            'maxErrorRate' => 0,
            'totalErrorRate' => 0,
            'lastTypedDate' => $row['typedDate'], // Add lastTypedDate to session statistics
        ];
    }

    // Calculate error rate
    $maxStrLen = $row['maxLen'];
    $minStrLen = $row['minLen'];
    $cpm=$row['cpm'];
    if ($maxStrLen > 0) {
        if ($row['editdistance3'] > $minStrLen) {
            $errorRate = 100;
        } else {
            $errorRate = round(($row['editdistance3'] / $maxStrLen) * 100, 2);
        }
    } else {
        $errorRate = 0;
    }

    // Update session statistics for the current keyboard type
    $keyboardStatistics[$session][$keyboard]['minCPM'] = min($keyboardStatistics[$session][$keyboard]['minCPM'], $cpm);
    $keyboardStatistics[$session][$keyboard]['maxCPM'] = max($keyboardStatistics[$session][$keyboard]['maxCPM'], $cpm);
    $keyboardStatistics[$session][$keyboard]['totalCPM'] += $cpm;
    $keyboardStatistics[$session][$keyboard]['totalCount']++;
    $keyboardStatistics[$session][$keyboard]['minErrorRate'] = min($keyboardStatistics[$session][$keyboard]['minErrorRate'], $errorRate);
    $keyboardStatistics[$session][$keyboard]['maxErrorRate'] = max($keyboardStatistics[$session][$keyboard]['maxErrorRate'], $errorRate);
    $keyboardStatistics[$session][$keyboard]['totalErrorRate'] += $errorRate;
}

// Calculate average statistics for each session and keyboard type
foreach ($keyboardStatistics as $session => $sessionStats) {
    foreach ($sessionStats as $keyboard => $stats) {
        $keyboardStatistics[$session][$keyboard]['averageCPM'] = $stats['totalCPM'] / $stats['totalCount'];
        $keyboardStatistics[$session][$keyboard]['averageErrorRate'] = $stats['totalErrorRate'] / $stats['totalCount'];
    }
}

// Prepare the array to be encoded as JSON
$jsonArray = [];
foreach ($keyboardStatistics as $session => $sessionStats) {
    foreach ($sessionStats as $keyboard => $stats) {
        $jsonArray[] = [
            'date' => $stats['lastTypedDate'],
            'session' => $session,
            'keyboard' => $keyboard,
            'min_cpm' => round($stats['minCPM'], 2),
            'max_cpm' => round($stats['maxCPM'], 2),
            'avg_cpm' => round($stats['averageCPM'], 2),
            'min_error_rate' => round($stats['minErrorRate'], 2),
            'max_error_rate' => round($stats['maxErrorRate'], 2),
            'avg_error_rate' => round($stats['averageErrorRate'], 2)
        ];
    }
}

// Sort the array based on keyboard, with current keyboard first if provided, and then by sessions
usort($jsonArray, function($a, $b) use ($currKeyboard) {
    // First, sort by keyboard
    if ($currKeyboard) {
        // If current keyboard is provided, sort to bring its entries first
        if ($a['keyboard'] == $currKeyboard && $b['keyboard'] != $currKeyboard) {
            return -1; // $a comes first
        } elseif ($a['keyboard'] != $currKeyboard && $b['keyboard'] == $currKeyboard) {
            return 1; // $b comes first
        } else {
            // Sort alphabetically if both or neither are the current keyboard
            $keyboardComparison = strcmp($a['keyboard'], $b['keyboard']);
            if ($keyboardComparison !== 0) {
                return $keyboardComparison;
            }
        }
    } else {
        // If current keyboard is not provided, sort alphabetically by keyboard
        $keyboardComparison = strcmp($a['keyboard'], $b['keyboard']);
        if ($keyboardComparison !== 0) {
            return $keyboardComparison;
        }
    }
    
    return $b['session'] - $a['session']; // sorting sessions in descending order
});

// Encode the array as JSON
$jsonString = json_encode($jsonArray);

echo $jsonString;


mysqli_close($conn);

?>

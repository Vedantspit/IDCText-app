<?php
require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Bangkok");

$uid = isset($_GET['uid']) ? $_GET['uid'] : '1';
$sid = isset($_GET['sid']) ? $_GET['sid'] : '1';

mysqli_set_charset($conn, "utf8");

$query = "SELECT userid, studyid, keyboard, language, sessions, nmph, phraseNumber, phraseShown, phraseTyped, editdistance1, editdistance2, editdistance3, typingTime, backspace, IncorrNF1, IncorrNF2, IncorrNF3, IncorrF, Correct1, Correct2, Correct3, Fixed, round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped), char_length(phraseShown)) minLen, greatest(char_length(phraseTyped), char_length(phraseShown)) maxLen, typedDate, typedTime FROM nusers WHERE studyid='$sid' ORDER BY userid, kbseq, sessions";

$result = mysqli_query($conn, $query);
if ($result === false) {
    die("Query failed: " . mysqli_error($conn));
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=detailed_summary.csv');

// Include BOM for UTF-8 encoding
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Study ID', 'UserID ID', 'Date', 'Time', 'Keyboard', 'Language', 'Session#', 'Phrase#', 'Shown phrase', 'Typed phrase', 'Error rate without any exceptions', 'Error rate with unicode exceptions', 'Error rate with unicode and Keyboard exceptions ', 'Average Speed(cpm)', 'Backspace Count', 'Corrected Error Rate1(without unicode and keyboard exceptions)', 'Uncorrected Error rate1(without unicode and keyboard exceptions)', 'Total Error rate1(without unicode and keyboard exceptions)', 'Corrected Error Rate2(with unicode exceptions)', 'Uncorrected Error rate2(with unicode exceptions)', 'Total Error rate2(with unicode exceptions)', 'Corrected Error Rate3(**with unicode and keyboard exceptions)', 'Uncorrected Error rate3(**with unicode and keyboard exceptions)', 'Total Error rate3(**with unicode and keyboard exceptions)', 'Incorrect but Fixed Keystrokes (IF)', 'Fixes Keystrokes(F)', 'Incorrect Not Fixed Keystrokes 1(INF)', 'Correct Keystrokes 1(C)', 'Incorrect Not Fixed Keystrokes 2(INF)', 'Correct Keystrokes 2(C)', 'Incorrect Not Fixed Keystrokes 3(INF)', 'Correct Keystrokes 3(C)']);

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $timeInMins = ($row['typingTime'] / 60000.0);
    $cpm = $row['cpm'] ?: 0;
    $backspacecount = $row['backspace'];
    $minStrLen = $row['minLen'];
    $maxStrLen = $row['maxLen'];

    $inf1 = $row['IncorrNF1'];
    $inf2 = $row['IncorrNF2'];
    $inf3 = $row['IncorrNF3'];
    $ifc = $row['IncorrF'];
    $corr1 = $row['Correct1'];
    $corr2 = $row['Correct2'];
    $corr3 = $row['Correct3'];
    $fix = $row['Fixed'];

    $cer1 = $uer1 = $ter1 = $cer2 = $uer2 = $ter2 = $cer3 = $uer3 = $ter3 = 0;

    if ($ifc != 0 || $inf1 != 0 || $corr1 != 0) {
        $x = ($ifc) / ($ifc + $inf1 + $corr1);
        $cer1 = round($x * 100, 2);
        $y = ($inf1) / ($ifc + $inf1 + $corr1);
        $uer1 = round($y * 100, 2);
        $z = ($ifc + $inf1) / ($ifc + $inf1 + $corr1);
        $ter1 = round($z * 100, 2);
    }
    if ($ifc != 0 || $inf2 != 0 || $corr2 != 0) {
        $x = ($ifc) / ($ifc + $inf2 + $corr2);
        $cer2 = round($x * 100, 2);
        $y = ($inf2) / ($ifc + $inf2 + $corr2);
        $uer2 = round($y * 100, 2);
        $z = ($ifc + $inf2) / ($ifc + $inf2 + $corr2);
        $ter2 = round($z * 100, 2);
    }
    if ($ifc != 0 || $inf3 != 0 || $corr3 != 0) {
        $x = ($ifc) / ($ifc + $inf3 + $corr3);
        $cer3 = round($x * 100, 2);
        $y = ($inf3) / ($ifc + $inf3 + $corr3);
        $uer3 = round($y * 100, 2);
        $z = ($ifc + $inf3) / ($ifc + $inf3 + $corr3);
        $ter3 = round($z * 100, 2);
    }

    $errorrate1 = $row['editdistance1'] > $minStrLen ? 100 : round(($row['editdistance1'] / $maxStrLen) * 100, 2);
    $errorrate2 = $row['editdistance2'] > $minStrLen ? 100 : round(($row['editdistance2'] / $maxStrLen) * 100, 2);
    $errorrate3 = $row['editdistance3'] > $minStrLen ? 100 : round(($row['editdistance3'] / $maxStrLen) * 100, 2);

    fputcsv($output, [
        $row['studyid'], $row['userid'], $row['typedDate'], $row['typedTime'], $row['keyboard'], $row['language'], $row['sessions'], $row['phraseNumber'], $row['phraseShown'], $row['phraseTyped'],
        $errorrate1 . "%", $errorrate2 . "%", $errorrate3 . "%", $cpm, $backspacecount,
        $cer1 . '%', $uer1 . '%', $ter1 . '%', $cer2 . '%', $uer2 . '%', $ter2 . '%',
        $cer3 . '%', $uer3 . '%', $ter3 . '%', $ifc, $fix, $inf1, $corr1, $inf2, $corr2, $inf3, $corr3
    ]);
}

mysqli_close($conn);
?>

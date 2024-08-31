<?php
require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Bangkok");
$uid = '1';
$sid = '1';
$cer1=0;
$uer1=0;
$ter1=0;
$cer2=0;
$uer2=0;
$ter2=0;
$cer3=0;
$uer3=0;
$ter3=0;
if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}

if (strlen($uid) == 0)
    $uid = "1";
if (strlen($sid) == 0)
    $sid = "1";

mysqli_set_charset($conn, "utf8");

$query = "SELECT userid,studyid,keyboard,language,sessions,nmph,phraseNumber,phraseShown,phraseTyped,editdistance1,editdistance2,editdistance3,typingTime,backspace,IncorrNF1,IncorrNF2,IncorrNF3,IncorrF,Correct1,Correct2,Correct3,Fixed,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM nusers where  studyid='$sid' AND userid='$uid' ORDER BY userid,kbseq,sessions";


$result = mysqli_query($conn, $query);
if ($result === false)
    die("Query failed: " . mysqli_error($conn));

$tablePrefix = "<h3>Detailed Summary</h3><br> <table><tr><th colspan='1'>Study ID</th><th colspan='1'>UserID ID</th><th colspan='1'>Keyboard</th><th colspan='1'>Language</th><th colspan='1'>Session#</th><th colspan='1'>Phrase#</th><th colspan='1'>Shown phrase</th><th colspan='1'>Typed phrase</th><th colspan='1'>Error rate without Cleanup</th><th colspan='1'>Error rate with unicode Cleanup</th><th colspan='1'>Error rate with unicode and Keyboard exceptions Cleanup</th><th colspan='1'>Speed(cpm)</th><th colspan='1'>Backspace Count</th><th colspan='1'>Corrected Error Rate1</th><th colspan='1'>Uncorrected Error rate1</th><th colspan='1'>Total Error rate1</th><th colspan='1'>Corrected Error Rate2</th><th colspan='1'>Uncorrected Error rate2</th><th colspan='1'>Total Error rate2</th><th colspan='1'>Corrected Error Rate3</th><th colspan='1'>Uncorrected Error rate3</th><th colspan='1'>Total Error rate3</th><th colspan='1'>Incorrect but Fixed Keystrokes (IF)</th><th colspan='1'>Fixes Keystrokes(F)</th><th colspan='1'>Incorrect Not Fixed Keystrokes 1(INF)</th><th colspan='1'>Correct Keystrokes 1(C)</th><th colspan='1'>Incorrect Not Fixed Keystrokes 2(INF)</th><th colspan='1'>Correct Keystrokes 2(C)</th><th colspan='1'>Incorrect Not Fixed Keystrokes 3(INF)</th><th colspan='1'>Correct Keystrokes 3(C)</th></tr>";
$tableSuffix = "</table>";
$tableBody = "";
$avgCPM = 0;
$minCPM=0;
$maxCPM=0;
$currentSession = null;
$sessionPhrases = [];
$sessionCPM = [];
$sessionErrorRates = [];

while ($row = mysqli_fetch_array($result)) 
{

    // $session = $row[4];
    // if ($currentSession !== $session) {
      
    //     if ($currentSession !== null) {
    //         $avgCPM = round(array_sum($sessionCPM) / count($sessionCPM),2);

    //         $minCPM=min($sessionCPM);
    //         $maxCPM=max($sessionCPM);
    //         $minErrorRate = min($sessionErrorRates);
    //         $maxErrorRate = max($sessionErrorRates);

    //         $tableBody .= "<td>Avg CPM</td>";
    //         $tableBody .= "<td>$avgCPM</td>";
    //         $tableBody .= "<td>Min CPM</td>";
    //         $tableBody .= "<td>$minCPM</td>";
    //         $tableBody .= "<td>Max CPM</td>";
    //         $tableBody .= "<td>$maxCPM</td>";
    //         $tableBody .= "<td>Min Error Rate</td>";
    //         $tableBody .= "<td>$minErrorRate</td>";
    //         $tableBody .= "<td>Max Error Rate</td>";
    //         $tableBody .= "<td>$maxErrorRate</td>";
       
    //         $sessionPhrases = [];
    //         $sessionCPM = [];
    //         $sessionErrorRates = [];
    //     }

    //     $currentSession = $session;
    // }

    $timeInMins = ($row[12] / 60000.0);
    $cpm = $row[22];

    if($cpm=='')
    {
        $cpm=0;
    }
    $backspacecount = $row[13];
    $minStrLen = $row[23];
    $maxStrLen = $row[24];

    $inf1 = $row[14];
    $inf2 = $row[15];
    $inf3 = $row[16];
    $ifc = $row[17];
    $corr1 = $row[18];
    $corr2 = $row[19];
    $corr3 = $row[20];
    $fix = $row[21];

    
    if ($ifc != 0 || $inf1 != 0 || $corr1!= 0) {
        $x = ($ifc) / ($ifc + $inf1 + $corr1);
        $cer1 = round($x * 100, 2);
        $y = ($inf1) / ($ifc + $inf1 + $corr1);
        $uer1 = round($y * 100, 2);
        $z = ($ifc + $inf1) / ($ifc + $inf1 + $corr1);
        $ter1 = round($z * 100, 2);
    } else {
        $cer1 = $uer1 = $ter1 = 0; 
    }
    if ($ifc != 0 || $inf2 != 0 || $corr2!= 0) {
        $x = ($ifc) / ($ifc + $inf2 + $corr2);
        $cer2 = round($x * 100, 2);
        $y = ($inf2) / ($ifc + $inf2 + $corr2);
        $uer2 = round($y * 100, 2);
        $z = ($ifc + $inf2) / ($ifc + $inf2 + $corr2);
        $ter2 = round($z * 100, 2);
    } else {
        $cer2 = $uer2 = $ter2 = 0; 
    }
    if ($ifc != 0 || $inf3 != 0 || $corr3!= 0) {
        $x = ($ifc) / ($ifc + $inf3 + $corr3);
        $cer3 = round($x * 100, 2);
        $y = ($inf3) / ($ifc + $inf3 + $corr3);
        $uer3 = round($y * 100, 2);
        $z = ($ifc + $inf3) / ($ifc + $inf3 + $corr3);
        $ter3 = round($z * 100, 2);
    } else {
        $cer3 = $uer3 = $ter3 = 0; 
    }
   

    if ($row[9] > $minStrLen)
        $errorrate1 = 100;
    else
        $errorrate1 = round(($row[9] / $maxStrLen) * 100, 2);

    if ($row[10] > $minStrLen)
        $errorrate2 = 100;
    else
        $errorrate2 = round(($row[10] / $maxStrLen) * 100, 2);

    if ($row[11] > $minStrLen)
        $errorrate3 = 100;
    else
        $errorrate3 = round(($row[11] / $maxStrLen) * 100, 2);

    // $sessionPhrases[] = $row;
    // $sessionCPM[] = $cpm;
    // $sessionErrorRates[] = $errorrate;

    $tableBody .= "\n<tr>";
    $tableBody .= "<td colspan='1'>" . $row[1] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[0] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[2] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[3] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[4] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[6] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[7] . "</td>";
    $tableBody .= "<td colspan='1'>" . $row[8] . "</td>";
    $tableBody .= "<td colspan='1'>" . $errorrate1 . "%</td>";
    $tableBody .= "<td colspan='1'>" . $errorrate2 . "%</td>";
    $tableBody .= "<td colspan='1'>" . $errorrate3 . "%</td>";
    $tableBody .= "<td colspan='1'>" . $cpm . "</td>";
    $tableBody .= "<td colspan='1'>" . $backspacecount . "</td>";
    $tableBody .= "<td colspan='1'>" . $cer1.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $uer1.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $ter1.'%'. "</td>";
    $tableBody .= "<td colspan='1'>" . $cer2.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $uer2.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $ter2.'%'. "</td>";
    $tableBody .= "<td colspan='1'>" . $cer3.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $uer3.'%' . "</td>";
    $tableBody .= "<td colspan='1'>" . $ter3.'%'. "</td>";
    $tableBody .= "<td colspan='1'>" . $ifc . "</td>";
    $tableBody .= "<td colspan='1'>" . $fix . "</td>";
    $tableBody .= "<td colspan='1'>" . $inf1 . "</td>";
    $tableBody .= "<td colspan='1'>" . $corr1 . "</td>";
    $tableBody .= "<td colspan='1'>" . $inf2 . "</td>";
    $tableBody .= "<td colspan='1'>" . $corr2 . "</td>";
    $tableBody .= "<td colspan='1'>" . $inf3 . "</td>";
    $tableBody .= "<td colspan='1'>" . $corr3 . "</td>";
    $tableBody .= "</tr>\n";
}

mysqli_close($conn);

echo $tablePrefix . $tableBody . $tableSuffix;
?>

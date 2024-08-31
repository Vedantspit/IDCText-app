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

if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}

if (strlen($uid) == '')
    $uid = "1";
if (strlen($sid) == '')
    $sid = "1";
$query_distinct_keyboards = "SELECT DISTINCT keyboard FROM nusers WHERE studyid='$sid'  ORDER BY kbseq";
$result_distinct_keyboards = mysqli_query($conn, $query_distinct_keyboards);

if ($result_distinct_keyboards === false)
    die("Query failed: " . mysqli_error($conn));

$distinct_keyboards = array();

while ($row_kb = mysqli_fetch_array($result_distinct_keyboards)) 
{
    $distinct_keyboards[] = $row_kb['keyboard'];
    //echo "keyboard ".$row_kb['keyboard'];
}
$tablePrefix = "<h3>OVERALL SUMMARY</h3><table><tr><th colspan='1'>Study ID</th><th colspan='1'>UserID ID</th><th colspan='1'>Keyboard</th><th colspan='1'>Error rate without any exceptions</th><th colspan='1'>Error rate with unicode exceptions</th><th colspan='1'>Error rate with unicode and Keyboard exceptions </th><th colspan='1'>Average Speed(cpm)</th><th colspan='1'>Corrected Error Rate1 (without unicode and keyboard exceptions)</th><th colspan='1'>Uncorrected Error rate1(without unicode and keyboard exceptions)</th><th colspan='1'>Total Error rate1(without unicode and keyboard exceptions)</th><th colspan='1'>Corrected Error Rate2(with unicode exceptions)</th><th colspan='1'>Uncorrected Error rate2(with unicode exceptions)</th><th colspan='1'>Total Error rate2(with unicode exceptions)</th><th colspan='1'>Corrected Error Rate3(**with unicode and keyboard exceptions)</th><th colspan='1'>Uncorrected Error rate3(**with unicode and keyboard exceptions)</th><th colspan='1'>Total Error rate3(**with unicode and keyboard exceptions)</th></tr>";
$tableSuffix = "</table>";
$tableBody = "";
$averageErrorRate1 = 0;
$averageErrorRate2 = 0;
$averageErrorRate3 = 0;
$averageCPM = 0;
foreach ($distinct_keyboards as $keyboard) 
{
    $query_distinct_users = "SELECT DISTINCT userid FROM nusers WHERE studyid='$sid' AND keyboard= '$keyboard'  ";
$result_distinct_users = mysqli_query($conn, $query_distinct_users);

if ($result_distinct_users === false)
    die("Query failed: " . mysqli_error($conn));

$distinct_users = array();

while ($row_kb = mysqli_fetch_array($result_distinct_users)) 
{
    $distinct_users[] = $row_kb['userid'];
    //echo "user ids  ".$row_kb['userid'];
}
 foreach($distinct_users as $user)
 {

    $totalErrorRate1 = 0;
    $totalErrorRate2 = 0;
    $totalErrorRate3 = 0;
    $totalCPM = 0;
    $count = 0;
    $averageUER1 = 0;
    $averageCER1 = 0;
    $averageTER1 = 0;
    $averageUER2 = 0;
    $averageCER2 = 0;
    $averageTER2 = 0;
    $averageUER3 = 0;
    $averageCER3 = 0;
    $averageTER3 = 0;
    $keyboard = $keyboard;

    // Fetch data for the current keyboard
    $query = "SELECT keyboard, phraseTyped, phraseShown, editdistance1, editdistance2, editdistance3, typingTime, backspace, IncorrNF1, IncorrNF2, IncorrNF3, IncorrF, Correct1, Correct2, Correct3, Fixed, round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped), char_length(phraseShown)) minLen, greatest(char_length(phraseTyped), char_length(phraseShown)) maxLen  FROM nusers WHERE userid='$user' AND studyid='$sid' AND keyboard='$keyboard'  ";

    $result = mysqli_query($conn, $query);

    if ($result === false)
        die("Query failed: " . mysqli_error($conn));

    while ($row = mysqli_fetch_array($result)) 
    {
        $maxStrLen = $row[18];
        $minStrLen = $row[17];
        $inf1 = $row[8];
        $inf2 = $row[9];
        $inf3 = $row[10];
        $ifc = $row[11];
        $corr1 = $row[12];
        $corr2 = $row[13];
        $corr3 = $row[14];
        $fix = $row[15];
        // echo " inf keystores are $inf1,$inf2,$inf3 ";
        // echo " ifc keystores are $ifc";
        // echo " correct keystores are $corr1,$corr2,$corr3 ";
        // echo " fix keystores are $fix";
        if ($ifc != 0 || $inf1 != 0 || $corr1 != 0) 
        {
            $uer1 = round(($inf1) / ($ifc + $inf1 + $corr1) * 100, 2);
            $cer1 = round(($ifc) / ($ifc + $inf1 + $corr1) * 100, 2);
            $ter1 = round(($ifc + $inf1) / ($ifc + $inf1 + $corr1) * 100, 2);
        } 
        else 
        {
            $uer1 = $cer1 = $ter1 = 0;
        }
        if ($ifc != 0 || $inf2 != 0 || $corr2 != 0) 
        {
            $uer2 = round(($inf2) / ($ifc + $inf2 + $corr2) * 100, 2);
            $cer2 = round(($ifc) / ($ifc + $inf2 + $corr2) * 100, 2);
            $ter2 = round(($ifc + $inf2) / ($ifc + $inf2 + $corr2) * 100, 2);
        } 
        else 
        {
            $uer2 = $cer2 = $ter2 = 0;
        }
    
        if ($ifc != 0 || $inf1 != 0 || $corr1 != 0) 
        {
            $uer3 = round(($inf3) / ($ifc + $inf3 + $corr3) * 100, 2);
            $cer3 = round(($ifc) / ($ifc + $inf3 + $corr3) * 100, 2);
            $ter3 = round(($ifc + $inf3) / ($ifc + $inf3 + $corr3) * 100, 2);
        } 
        else 
        {
            $uer3 = $cer3 = $ter3 = 0;
        }
    
        $errorRate1 = ($row['editdistance1'] / $maxStrLen) * 100;
        $errorRate2 = ($row['editdistance2'] / $maxStrLen) * 100;
        $errorRate3 = ($row['editdistance3'] / $maxStrLen) * 100;
    
       
    
        $totalErrorRate1 += $errorRate1;
        $totalErrorRate2 += $errorRate2;
        $totalErrorRate3 += $errorRate3;
    
        $averageUER1 += $uer1;
        $averageCER1 += $cer1;
        $averageTER1 += $ter1;
        
        $averageUER2 += $uer2;
        $averageCER2 += $cer2;
        $averageTER2 += $ter2;
    
        $averageUER3 += $uer3;
        $averageCER3 += $cer3;
        $averageTER3 += $ter3;
    
        $totalCPM += $row[16];
    
        $count++;
       
    }

    if($count>0)
    {
    $averageErrorRate1 = $totalErrorRate1 / $count;
    $averageErrorRate2 = $totalErrorRate2 / $count;
    $averageErrorRate3 = $totalErrorRate3 / $count;
    $averageUER1 /= $count;
    $averageCER1 /= $count;
    $averageTER1 /= $count;
    
    $averageUER2 /= $count;
    $averageCER2 /= $count;
    $averageTER2 /= $count;
    
    $averageUER3 /= $count;
    $averageCER3 /= $count;
    $averageTER3 /= $count;
    
    $averageCPM = $totalCPM / $count;
    }
    

    $tableBody .= "\n<tr>";
    $tableBody .= "<td colspan='1'>" . $sid . "</td>";
    $tableBody .= "<td colspan='1'>" . $user . "</td>";
    $tableBody .= "<td colspan='1'>" . $keyboard . "</td>";
    $tableBody .= "<td colspan='1'>" . round($averageErrorRate1,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageErrorRate2,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" .round($averageErrorRate3,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageCPM,2) . "</td>";
    $tableBody .= "<td colspan='1'>" . round($averageCER1,2). "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageUER1,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageTER1,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageCER2,2). "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageUER2,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageTER2,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageCER3,2). "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageUER3,2) . "%</td>";
    $tableBody .= "<td colspan='1'>" . round($averageTER3,2) . "%</td>";
    $tableBody .= "</tr>\n";
}
}

mysqli_close($conn);

echo $tablePrefix . $tableBody . $tableSuffix;
?>

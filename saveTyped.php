<?php

require 'config/_dbconnect1.php';

date_default_timezone_set("Asia/Kolkata");

// Get the current date and time in Indian Standard Time
$currentDateIST = date('Y-m-d');
$currentTimeIST = date('H:i:s');

$uid = '1';
$sid = '1';

if (isset($_POST['userid'])) {
    $uid = $_POST['userid'];
}

if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}

if (isset($_POST['phrases'])) {
    $tapsJSON = $_POST['phrases'];
} else {
    return;
}

if (isset($_POST['backspace'])) {
    $backspaces = $_POST['backspace'];
}

if (isset($_POST['corrected'])) {
    $corr = $_POST['corrected'];
}

if (isset($_POST['incorrfix'])) {
    $ifc = $_POST['incorrfix'];
}

if (isset($_POST['incorrnot'])) {
    $inf = $_POST['incorrnot'];
}

if (isset($_POST['fixes'])) {
    $fstroke = $_POST['fixes'];
}

if (isset($_POST['keyboard'])) {
    $keyboard = $_POST['keyboard'];
}

$taps = json_decode($tapsJSON);
$backpresses = json_decode($backspaces);
$corr = json_decode($corr);
$ifc = json_decode($ifc);
$inf = json_decode($inf);
$fstroke = json_decode($fstroke);

$count = 0;

mysqli_set_charset($conn, "utf8");
$flagsql = "SELECT flag, sessions, phraseNumber FROM nusers WHERE userid = '$uid' AND studyid = '$sid' AND keyboard='$keyboard' ORDER BY sessions,phraseNumber";
$flagres = mysqli_query($conn, $flagsql);

if ($flagres) {
    // Loop through each row in the result set
    while ($flagrow = mysqli_fetch_assoc($flagres)) {
        $flagValue = $flagrow['flag'];

        if ($flagValue == 1) {
            // Data already saved
        } else {
            $currentSession = $flagrow['sessions'];
            $phraseNumber = $flagrow['phraseNumber'];
            
            $update_body = "";

            for ($i = 0; $i < count($taps); $i++) {
                if (strlen($update_body) > 0)
                    $update_body .= "; ";

                $x = $phraseNumber + $i;
                $phraseTyped = mysqli_real_escape_string($conn, $taps[$i]->phraseTyped);

                // Construct UPDATE query
                $update_body = "UPDATE nusers SET typedDate='$currentDateIST', typedTime='$currentTimeIST', phraseTyped='$phraseTyped', flag=1, editdistance1={$taps[$i]->editdistance1}, editdistance2={$taps[$i]->editdistance2}, editdistance3={$taps[$i]->editdistance3}, typingTime={$taps[$i]->timeTaken}, backspace={$backpresses[$i]}, IncorrNF1={$inf[$i]}, IncorrNF2={$inf[$i+1]}, IncorrNF3={$inf[$i+2]}, IncorrF={$ifc[$i]}, Correct1={$corr[$i]}, Correct2={$corr[$i+1]}, Correct3={$corr[$i+2]}, Fixed={$fstroke[$i]} WHERE userid='$uid' AND studyid='$sid' AND flag=0 AND sessions=$currentSession AND keyboard='$keyboard' AND phraseNumber=$x ORDER BY sessions";
                
                $result = mysqli_query($conn, $update_body);

                if ($result) {
                    echo "Data saved successfully";
                    return;
                }
            }

            echo "Queries are: " . $update_body;

            if (!$conn) {
                die("Database connection failed: " . mysqli_error());
            }
        }
    }
} else {
    echo "Error fetching flag value from database";
}
?>

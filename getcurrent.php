<?php

require 'config/_dbconnect1.php';

$uid = '1';
$sid = '1';
$nmph = 0;


if (isset($_POST['userid'])) {
    $uid = $_POST['userid'];
}

if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}
if (isset($_POST['nmph'])) {
    $nmph = $_POST['nmph'];
}
if (isset($_POST['kbseq'])) {
    $kbseq = $_POST['kbseq'];
}


mysqli_set_charset($conn, "utf8");

    $sql = "SELECT keyboard,sessions,phraseNumber FROM nusers WHERE userid = '$uid' AND studyid = '$sid' AND kbseq= '$kbseq' AND  flag=0 ORDER BY sessions";
    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }

    $result = mysqli_query($conn, $sql);

    if ($result)
     {
    
        $currentSession = null;
        $currentPhrase = null;
        $keyboard = null;

        while ($row = mysqli_fetch_assoc($result)) {
        
            if ($currentSession === null) 
            {
                $keyboard = $row['keyboard'];
                $currentSession = $row['sessions'];
                $currentPhrase = $row['phraseNumber'];
            }

          
        }

       
        $response = [
            'userID' => $uid,
            'studyID' => $sid,
            'keyboard' => $keyboard,
            'currentSession' => $currentSession,
            'currentPhrase' => $currentPhrase
          
        ];

        echo json_encode($response);
    } else {
   
        echo json_encode(['error' => 'Failed to fetch phrases from the database']);
    }

 
?>

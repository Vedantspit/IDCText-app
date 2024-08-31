<?php

// Include database connection file
require 'config/_dbconnect1.php';

// Initialize user ID, study ID, and other variables
$uid = '1';
$sid = '1';
$nmph = 0;

// Check if user ID is set in POST data
if (isset($_POST['userid'])) 
{
    $uid = $_POST['userid'];
}

// Check if study ID is set in POST data
if (isset($_POST['studyid'])) 
{
    $sid = $_POST['studyid'];
}
if (isset($_POST['nmph'])) 
{
    $nmph = $_POST['nmph'];
}
if (isset($_POST['currsesh'])) 
{
    $currsesh = $_POST['currsesh'];

}
if (isset($_POST['keyboard'])) 
{
    $keyboard = $_POST['keyboard'];
}
if (isset($_POST['kbseq'])) 
{
    $kbseq = $_POST['kbseq'];
}


    $sql = "SELECT phraseShown, sessions, phraseNumber FROM nusers WHERE userid = '$uid' AND studyid = '$sid'  AND  sessions = $currsesh AND keyboard='$keyboard' AND flag=0 ORDER BY sessions ";
    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }
    mysqli_set_charset($conn, "utf8");
    $result = mysqli_query($conn, $sql);
    if ($result) 
    {
        $phrasesArray = array();
        $currentSession = null;
        $currentPhrase = null;
    
        while ($row = mysqli_fetch_assoc($result))
         {
            if ($currentSession === null) 
            {
                $currentSession = $row['sessions'];
                $currentPhrase = $row['phraseNumber'];
            }
    
            $phrasesArray[] = $row['phraseShown'];
        }
    
        // Output user ID, study ID, current session, current phrase, and phrases array in JSON format
        $response = [
            'userID' => $uid,
            'studyID' => $sid,
            'phrases' => $phrasesArray,
            'currentSession' => $currentSession,
            'currentPhrase' => $currentPhrase
        ];
    
        echo json_encode($response);
    } else 
    {
       
        echo json_encode(['error' => 'Failed to fetch phrases from the database: ' . mysqli_error($conn)]);
    }
 



?>
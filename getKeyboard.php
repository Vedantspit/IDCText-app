<?php

require 'config/_dbconnect1.php';

$uid = '1';
$sid = '1';
$session;
$phrasesperSession;
$nextKeyboard=null;
if (isset($_POST['userid'])) 
{
    $uid = $_POST['userid'];
}
if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}



mysqli_set_charset($conn, "utf8");

    $sql = "SELECT DISTINCT kbseq ,keyboard from nusers where studyid='$sid'  and userid ='$uid' and flag=0 ORDER by kbseq asc LIMIT 1";
    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }

    $result = mysqli_query($conn, $sql);

    if ($result)
     {
        
        while ($row = mysqli_fetch_assoc($result)) 
        {
          $nextKeyboard=$row['keyboard'];   
        }

       
        $response = [
            'nextKeyboard' => $nextKeyboard
        ];

        echo json_encode($response);
    } else {
   
        echo json_encode(['error' => 'Failed to fetch keyboard from the database']);
    }

 
?>

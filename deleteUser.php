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


mysqli_set_charset($conn, "utf8");

    $sql = "DELETE FROM nusers WHERE userid = '$uid' AND studyid = '$sid'";
    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }

    $result = mysqli_query($conn, $sql);

    if ($result)
     {
        echo "Deleted Successfully";
    } 
    else
     {
    echo "Failed to Delete: " . mysqli_error($conn); 
        
    }

 
?>

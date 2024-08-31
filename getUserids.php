<?php
    require 'config/_dbconnect1.php';
    date_default_timezone_set("Asia/Bangkok");
    $uid = '1';
    $sid = '1';
    if (isset($_GET['sid'])) {
        $sid = $_GET['sid'];
    }
   


    if(strlen($sid)==0)
        $sid = "1";
    
    mysqli_set_charset($conn, "utf8");
    $sql="SELECT DISTINCT userid from nusers where studyid='$sid' ORDER BY sessions";
    
    $result = mysqli_query($conn, $sql);
    if($result)
    { 
        $userids = array();
    while ($row = mysqli_fetch_array($result)) 
    {
        $userids[] = $row['userid'];
    } 
    $response = [
        'userids' => $userids
    ];
    echo json_encode($response);
   }
   else
   {
    echo json_encode(['error' => 'Failed to fetch phrases from the database: ' . mysqli_error($conn)]);
   }


   
   ?>
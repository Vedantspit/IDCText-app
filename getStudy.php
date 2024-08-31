<?php

require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Bangkok");
$sid = '1';
$noKeyboard=-1;
if (isset($_POST['sid'])) {
    $sid = $_POST['sid'];
}
if(strlen($sid)==0)
$sid = "1";

mysqli_set_charset($conn, "utf8");
$sql= "SELECT noKeyboard , randomization , sessions , phrasesps ,unicodeLookup ,keyboardLookup from studies where studyid ='$sid'";

$result = mysqli_query($conn, $sql);
if($result)
{ 
    

    while ($row = mysqli_fetch_array($result)) 
    {
        $noKeyboard=$row['noKeyboard'];
        $random=$row['randomization'];
        $sess=$row['sessions'];
        $phrasesps=$row['phrasesps'];
        $unicodeLookup=$row['unicodeLookup'];
        $keyboardLookup=$row['keyboardLookup'];
    } 
    $unicodeLookup=unserialize($unicodeLookup);
    $keyboardLookup=unserialize($keyboardLookup);
    $response =[
        'noKeyboard' => $noKeyboard,
        'randomization' => $random,
        'maxSess' => $sess,
        'phrasesps' => $phrasesps,
        'unicodeLookup' => $unicodeLookup,
        'keyboardLookup' => $keyboardLookup
    ];
echo json_encode($response);
}
else
{
echo ('error Failed to fetch phrases from the database: ' . mysqli_error($conn));
}

?>
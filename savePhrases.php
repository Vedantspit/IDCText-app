<?php

require 'config/_dbconnect1.php';
date_default_timezone_set("Asia/Kolkata");

// Get the current date and time in Indian Standard Time
$currentDateIST = date('Y-m-d');
$currentTimeIST = date('H:i:s');
	$id = "";
	$dependentVariable="var1";

	//$tapsJSON='[{"tapSequenceNumber":1,"startTimestamp":1531976733779,"endTimestamp":1531976733895},{"tapSequenceNumber":2,"startTimestamp":1531976734286,"endTimestamp":1531976734413},{"tapSequenceNumber":3,"startTimestamp":1531976734455,"endTimestamp":1531976734571},{"tapSequenceNumber":4,"startTimestamp":1531976734612,"endTimestamp":1531976734714},{"tapSequenceNumber":5,"startTimestamp":1531976734759,"endTimestamp":1531976734891},{"tapSequenceNumber":6,"startTimestamp":1531976734918,"endTimestamp":1531976735028},{"tapSequenceNumber":7,"startTimestamp":1531976735060,"endTimestamp":1531976735183},{"tapSequenceNumber":8,"startTimestamp":1531976863014,"endTimestamp":1531976863098}]';

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id=$id;
        //echo "got the id ".$id;

    }

    if (isset($_POST['phrases'])) 
    {
        $tapsJSON = $_POST['phrases'];
    }
    else
    {
    	echo "No phrase logs found";
    	return;
    }
    $taps = json_decode($tapsJSON);

   

    mysqli_set_charset($conn, "utf8");
    $insert_query = "INSERT INTO nusers(userid,studyid,typedDate,typedTime,keyboard,kbseq,language,sessions,nmph,phraseNumber,phraseShown) VALUES ";
    $insert_body ="";

    for($i = 0; $i < count($taps); $i++) {
        if(strlen($insert_body) > 0)
            $insert_body .= ",";
    
        // Convert object properties to strings before concatenating
        $insert_body .= "('" . $id . "','" . $taps[$i]->studyid . "','" . $currentDateIST . "','" . $currentTimeIST . "','" . $taps[$i]->keyboard ."'," . $taps[$i]->kbseq . ",'" . (string)$taps[$i]->language . "'," . $taps[$i]->sessions . "," . (string)$taps[$i]->nmph . "," . (string)$taps[$i]->phraseNumber . ",'" . (string)$taps[$i]->phraseShown . "')";

    }

   

    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }
 

  
     $result = mysqli_query($conn,$insert_query.$insert_body);

    if($result)
    {
    	echo "Data saved successfully";
        return;
    }
    else
    {
    	die("Data could not be saved: " . mysqli_error($conn)." ".$insert_query.$insert_body);
    }



?>

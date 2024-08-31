<?php
error_reporting(E_ERROR | E_PARSE);


$userid = $_GET['userid'];
$studyid = $_GET['studyid'];
$currKeyboard = $_GET['kb'];



echo "<script>var uid = '$userid'; var sid = '$studyid';  var sid = '$studyid';  var currKeyboard = '$currKeyboard';</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/congo.css">

    <title>Congrats</title>
</head>
<body onload="getKeyboards()">


<div class="container">
<div class="trophy-container">
    <img src="trophy.jpg" alt="Trophy" class="trophy-image">
</div>
    <h2 id="current"></h2>
  
    <p id="nextKeyboard"></p>
    
    <div class="instructions">
        <ul>
            <li id="make"></li>
            <li id="pt1">Remember to take break between typing sessions.</li>
            <li id="pt2">For optimal results, limit sessions to 10 per day, splitting them into two sets of 5 with breaks in between.</li>
            <li id="pt3">Exceeding this limit may lead to fatigue and reduced effectiveness.</li>
        </ul>
    </div>
    <div class="button-container">
            <button id="proceedButton" onclick="proceedWithNewKeyboard()">Proceed</button>
    </div>

</div>
    <!-- <div class="js-container container" style="top:0px !important;"></div>
    <script src="congrats.js"></script> -->
    <script>
        var sid=sid;
        var uid=uid;
        var currKeyboard=currKeyboard;
        var nextKeyboard=null;
        var maxSess = 0;
        var phrasesps =0;
        function proceedWithNewKeyboard() 
        {
            if(nextKeyboard!=null)
            {
            window.location.href = "main.php?userid="+uid+"&studyid="+sid+"&p="+phrasesps+"&s="+maxSess;
            }
            else
            {
                window.location.href="userresult.html?study_id="+sid+"&userid="+uid;
            }
        }

      
         function getKeyboards()
         {
            console.log("in get keyboards function ");
            var xmlhttp;
            var xmlhttp2;


       if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
       } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
       }

        xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            console.log("in xml response space ");
            console.log("Response "+response);
            nextKeyboard = response.nextKeyboard;
           
            
			if(nextKeyboard!=null)
            {
                document.getElementById('current').innerHTML='Dear '+uid + ' , Congratulations🎉🎉 for completing sessions with '+currKeyboard+' Keyboard';
                document.getElementById('proceedButton').innerText = 'Proceed with ' +nextKeyboard+  ' Keyboard';
                document.getElementById('make').innerText = 'Make sure you have APK installed for the  ' +nextKeyboard+  ' Keyboard';

            }
            else
            {
                document.getElementById('current').innerHTML='Dear '+uid + ' , Congratulations🎉🎉 for completing sessions with '+currKeyboard+' Keyboard';
                
                document.getElementById('make').innerText = 'All sessions are successfully completed by you.';
                document.getElementById('pt1').innerText = null;
                document.getElementById('pt2').innerText = null;
                document.getElementById('pt3').innerText = null;
                document.getElementById('pt1').style.display = 'none';
                document.getElementById('pt2').style.display = 'none';
                document.getElementById('pt3').style.display = 'none';
                document.getElementById('proceedButton').innerText = 'Check Analytics ';
            }
        } else if (this.readyState == 4 && this.status != 200) 
        {
            console.log("Something went wrong");
            document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:' + this.status + '.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
        }
    };

    var url = "getKeyboard.php";
    var params = "userid=" + uid + "&studyid=" + sid ;
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);


    if (window.XMLHttpRequest) {
        xmlhttp2 = new XMLHttpRequest();
       } else {
        xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
       }

        xmlhttp2.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            console.log("in xml response space ");
            console.log("Response "+response);
            maxSess = response.maxSess;
            phrasesps = response.phrasesps;
            console.log("");
           
        } else if (this.readyState == 4 && this.status != 200) 
        {
            console.log("Something went wrong");
            document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:' + this.status + '.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
        }
    };

    var url = "getStudy.php";
    var params = "sid=" + sid ;
    xmlhttp2.open("POST", url, true);
    xmlhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp2.send(params);
         }

    </script>
</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
  $uid=0;
  $sid=0;
 
   require 'config/_dbconnect1.php';
   mysqli_set_charset($conn, "utf8");

if (isset($_GET['study_id'])) 
{
  
  $sid = $_GET['study_id'];
  $presid=$sid;
  $sid = substr($sid, 2, -2);
  // echo $sid;

  $sql="SELECT * from studies where studyID='$sid' ";


  $result = mysqli_query($conn, $sql);
  $num=mysqli_num_rows($result);
  if($num==1)
  {
  while($row=mysqli_fetch_assoc($result))
  {
    $task = $row['task'];
    $language = $row['language'];
    $sessions = $row['sessions'];
    $phrasesps = $row['phrasesps'];
    $rf = $row['rf'];
    $randomization=$row['randomization'];

    $phrases = unserialize($row['phrases']);      
    $rampingData = unserialize($row['difficulty']);  
    $studyType = $row['studyType'];
    $noKeyboard = $row['noKeyboard'];
    $keyboardArray=unserialize($row['keyboards']);  
    $unilookupData=unserialize($row['unicodeLookup']);  
    $keyboardLookup=unserialize($row['keyboardLookup']);  
  }
  }

echo "<script>"; 
echo "var presid='$presid';";
echo "var sid='$sid';";
echo "var task='$task';";
echo "var language='$language';";
echo "var sessions=$sessions;";
echo "var phrasesps=$phrasesps;";
echo "var rf=$rf;";
echo "var randomization='$randomization';";
// Convert PHP array to JavaScript array
echo "var phrases=" . json_encode($phrases) . ";";
echo "var rampingData=" . json_encode($rampingData) . ";";
echo "var studyType='$studyType';";
echo "var noKeyboard=$noKeyboard;";
echo "var keyboardArray=" . json_encode($keyboardArray) . ";";
echo "var unilookupData=" . json_encode($unilookupData) . ";";
echo "</script>";


}





?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create User</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Micro+5+Charted&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Teko:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles\index3.css">
  <style>
  
  </style>
</head>
<body onload="fetchData()">
<div id="container" class="container">
  <h2>Add Participants</h2>
  <h4>Enter USER ID</h4>
  <form action="" method="post" onsubmit="return getInfo()">
    <input type="text" id="userID" name="userID" required autofocus>
    <input type="hidden" name="study_id" value="<?php echo $sid; ?>">
    <p id="desc"></p>
    <div id="keyboard-options"></div>
    <button type="submit" name="submit">SUBMIT</button>
  </form>
  <div id="containerdisp" class="containerdisp"></div>
  <button onclick="generateURL()">Generate URL</button>
  <br>
  <label for="studyurl">Study URL:</label>
  <p id="studyurl"></p>
  <label for="sessionurl">Session URL:</label>
  <p id="sessionurl"></p>


  <div id="container2" class="container2"></div>
  <div id="cont3"></div>
  <div id="cont4"></div>

  </div>
</body>
<script>
   // Your JavaScript code goes here
   var sessionPhrases = {};
   var phraseCount = {};
   var tapLogsArray=[];
var presid=presid;
var task=task;
var language=language;
var sessions=sessions;
var phrasesps=phrasesps;
var rf=rf;
var randomization=randomization;
var phrases=phrases;
var rampingData=rampingData;
var studyType=studyType;
var noKeyboard=noKeyboard;
var keyboardArray=keyboardArray;
var unilookupData=unilookupData;
var uid=-1;
var modifiedPhrases = [];
var phraseCount={};
var difficulties = [];
var languages = [];
var currentRank = 1;
var sid=sid;
var selectedOrder = {};

// console.log("Task:", task);
// console.log("USER ID:", uid);
// console.log("studyid:", sid);
// console.log("Language:", language);
// console.log("Sessions:", sessions);
// console.log("Phrases Per Session:", phrasesps);
// console.log("Repetition Factor:", rf);
// console.log("randomization :",randomization );
// console.log("Phrases:", phrases);
// console.log("rampingData: ",rampingData);
// console.log("Study Type is :", studyType);
// console.log("No. of keyboards :", noKeyboard);
// console.log("keyboards are "+ keyboardArray);
// console.log("Lookup Data : "+ unilookupData);
// console.log(unilookupData[0]);
// console.log(phrases[0]);
// console.log(phrases);

const newPhrases = [];
const newRamp ={};

const selectedPhrases = phrases.slice(0, phrases.length);

selectedPhrases.forEach((phraseString) => {

    const parts = phraseString.split(" ");

 
    const difficulty = parseInt(parts[0]);

    const phrase = parts.slice(1, -1).join(" ").trim();
    
    const language = parts.slice(-1)[0];

  
    newPhrases.push({
        phrase: phrase,
        difficulty: difficulty,
        language: language
    });
});
console.log("NEW PHRASES ARE " + typeof(newPhrases));

let maxDifficultyPhrase = Number.MIN_VALUE;

newPhrases.forEach((phraseObject, index) => {
    console.log(`Phrase ${index + 1}:`);
    console.log(`  Phrase: ${phraseObject.phrase}`);
    console.log(`  Difficulty: ${phraseObject.difficulty}`);
    console.log(`  Language: ${phraseObject.language}`);
    
    const difficulty = parseFloat(phraseObject.difficulty);
    if (!isNaN(difficulty) && difficulty > maxDifficultyPhrase) {
      maxDifficultyPhrase = difficulty;
    }
});

console.log("Maximum Difficulty: " + maxDifficultyPhrase);


if(rampingData)
{
rampingData.forEach((item) => {
  
  const parts = item.split(" ");
    const [start, end] = parts[0].split("-");
    const difficulty = parseInt(parts[1]);
    const key = `${start}-${end}`;
    newRamp[key] = difficulty;
});
}
// Output the constructed ramping data object
console.log("constructed ramping object " + newRamp);
//getRamp();


function generateURL() 
{
  console.log("in the generate url fuction");
  var mystr=JSON.stringify(selectedOrder);
    var url = 'main.php?userid=' + encodeURIComponent(uid) + '&studyid=' + encodeURIComponent(sid) + '&p=' + encodeURIComponent(phrasesps) +'&s=' + encodeURIComponent(sessions);
    var studyUrl = 'index3.php?study_id='+ encodeURIComponent(presid) ;
    console.log("the url is " +url);
    document.getElementById('studyurl').innerHTML = '<a href="' + studyUrl + '">' +  studyUrl  + '</a>';
    document.getElementById('sessionurl').innerHTML = '<a href="' + url + '">' +  url   + '</a>';
  
}
function generateKeyboardInputs() {
    var keyboardOptionsHTML = "<form id='keyboardOrderForm'>";

    for (var i = 0; i < keyboardArray.length; i++) {
        var keyboardName = keyboardArray[i];
        keyboardOptionsHTML += `<label for="keyboard${i}">${keyboardName}:</label>`;
        keyboardOptionsHTML += `<input type="number" id="keyboard${i}" name="${keyboardName}" value="${i + 1}" min="1" max="${keyboardArray.length}" required>`;
        keyboardOptionsHTML += "<br>";
    }

    keyboardOptionsHTML += "</form>";

    document.getElementById('keyboard-options').innerHTML = keyboardOptionsHTML;
}

function generateKeyboardOptions() {
    var keyboardOptionsHTML = "";

    for (var i = 0; i < keyboardArray.length; i++) {
        keyboardOptionsHTML += `<input type="radio" name="keyboard-selection" value="${i}" id="keyboard${i}" required>`;
        keyboardOptionsHTML += `<label for="keyboard${i}">${keyboardArray[i]}</label><br>`;
    }
    document.getElementById('keyboard-options').innerHTML = keyboardOptionsHTML;
}
if (studyType === "within") 
{
  document.getElementById('desc').innerHTML = "Select Order of the  Keyboard";

  generateKeyboardInputs();
} 
else if (studyType === "between") 
{
  document.getElementById('desc').innerHTML = "Select Any one Keyboard";
  generateKeyboardOptions();
}
function submitOrder() {
    var form = document.getElementById("keyboardOrderForm");
    //console.log("Form :", form);

    if (studyType === "within") {
        // Retrieve the values of the input fields
        for (var i = 0; i < noKeyboard; i++) {
            var inputField = document.getElementById(`keyboard${i}`);
            selectedOrder[inputField.name] = parseInt(inputField.value);
        }

        console.log("Selected keyboard order:", selectedOrder);
        for (var key in selectedOrder) {
            if (selectedOrder.hasOwnProperty(key)) {
                var value = selectedOrder[key];
                console.log("Keyboard:", key, "Order:", value);
            }
        }
    } else if (studyType === "between") {
       
      var selectedKeyboard = document.querySelector('input[name="keyboard-selection"]:checked');
    if (selectedKeyboard) {
      console.log("Selected keyboard:", keyboardArray[selectedKeyboard.value]);
      selectedOrder[keyboardArray[selectedKeyboard.value]] = 1;
      for (var key in selectedOrder) {
        if (selectedOrder.hasOwnProperty(key)) {
          var value = selectedOrder[key];
          console.log("Keyboard:", key, "Order:", value);
        }
      }
    } else {
      console.error("No keyboard selected");
    }
    }
}

function getDifficultyForSession(session, rampingData) {
    for (let range in rampingData) {
        const [startSession, endSession] = range.split("-");
        if (session >= parseInt(startSession) && session <= parseInt(endSession)) {
            return rampingData[range];
        }
    }
    return maxDifficultyPhrase; 
}



function newdividePhrases(numSessions, phrasesPerSession, repetitionFactor, phrases, shuffleParam, rampingData) 
{
   phraseCount = {};
   sessionPhrases={};
    // console.log("I AM IN THE DIVIDE PHRASES FUNCTION ");
    // console.log("phrases are " + JSON.stringify(newPhrases));
    // console.log("got the shuffle parameter as " + shuffleParam);
    

    
    for (let session = 1; session <= numSessions; session++) 
    {
     // console.log("NEW PHRASES ARE"+newPhrases);
        const availablePhrases = newPhrases.slice();
        //console.log("AVAILABLE phrases duplicate  ARE"+newPhrases);
        availablePhrases.forEach((phraseObject, index) => {
    // console.log(`Phrase ${index + 1}:`);
    // console.log(`  Phrase: ${phraseObject.phrase}`);
    // console.log(`  Difficulty: ${phraseObject.difficulty}`);
    // console.log(`  Language: ${phraseObject.language}`);
  
});
        if (shuffleParam)
        {
            console.log("in the if statement with shuffle param as " + shuffleParam);
            shuffleArray(availablePhrases);
        }
        let arrph = [];
        const maxAllowedDifficulty = getDifficultyForSession(session, rampingData);
        for (let i = 0; i < phrasesPerSession; i++) {
            let selectedPhrase;
            do 
            {
                selectedPhrase = availablePhrases.pop();
            } while (((phraseCount[selectedPhrase.phrase] || 0) >= repetitionFactor || selectedPhrase.difficulty > maxAllowedDifficulty) && availablePhrases.length > 0);
            phraseCount[selectedPhrase.phrase] = (phraseCount[selectedPhrase.phrase] || 0) + 1;
            arrph.push(selectedPhrase.phrase);
            console.log(`Phrase: ${selectedPhrase.phrase}, Difficulty: ${selectedPhrase.difficulty}`);
        }
        sessionPhrases[session] = arrph;
        console.log("ARRAY OF SESSION " + session + " is  " + JSON.stringify(sessionPhrases[session])  );
    }
    return sessionPhrases;

}


function SaveOrder()
{
  var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log("Selected order saved successfully.");
    }
};
xmlhttp.open("POST", "saveSelectedOrder.php", true);
xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
xmlhttp.send(JSON.stringify(selectedOrder));
}
function getInfo()
 {
    submitOrder();
    console.log("IN getInfo function");
    uid = document.getElementById('userID').value;
    console.log("User ID:", uid);
    if(studyType==="between" && randomization ==="random")
    {
      sessionPhrases =newdividePhrases(sessions, phrasesps, rf, newPhrases, 1, newRamp) ;
      console.log("REPETITON COUNT OF EACH PHRASE "+JSON.stringify(phraseCount));
      // dividePhrases(sessions, phrasesps, rf, phrases,1);
      preSync();
      syncToServer();
      tapLogsArray=[];

    }
    else if(studyType=="within" && randomization =="random")
    {
      for(let i=0;i<noKeyboard;i++)
      {
        sessionPhrases =newdividePhrases(sessions, phrasesps, rf, newPhrases, 1, newRamp) ;
        console.log("REPETITON COUNT OF EACH PHRASE "+JSON.stringify(phraseCount));
        preSync();
        syncToServer();
        tapLogsArray=[];
      }
    }
    else if(studyType=="within" && randomization =="norandom")
    {
      for(let i=0;i<noKeyboard;i++)
      {
        console.log("REPETITON COUNT OF EACH PHRASE "+JSON.stringify(phraseCount));
        sessionPhrases = newdividePhrases(sessions, phrasesps, rf, newPhrases, 0, newRamp) ;
        preSync();
        syncToServer();
        tapLogsArray=[];
       
      }
    }
    else
    {
        console.log("REPETITON COUNT OF EACH PHRASE "+JSON.stringify(phraseCount));
        sessionPhrases = newdividePhrases(sessions, phrasesps, rf, newPhrases, 0, newRamp) ;
        preSync();
        syncToServer();
        tapLogsArray=[];
    }
 
    
    return false; // Prevent form submission
}
          function shuffleArray(sourceArray) 
         {
          for (var i = 0; i < sourceArray.length - 1; i++) 
          {
              var j = i + Math.floor(Math.random() * (sourceArray.length - i));
         
              var temp = sourceArray[j];
              sourceArray[j] = sourceArray[i];
              sourceArray[i] = temp;
          }
          return sourceArray;
         }

function preSync(loc)
{
  console.log("I AM IN THE PRE SYNC FUNCTION ");
  
 for(let i=1;i<=sessions;i++)
 {
  console.log( "in preSync function for SESSION "+(i));
   var taps={};
   
   arrph=sessionPhrases[i];
 
   for(let j=0;j<phrasesps;j++)
   {
    taps['studyid']=sid;
    var currentKeyboard = Object.keys(selectedOrder).find(key => selectedOrder[key] === currentRank);
    if (currentKeyboard)
     {
      console.log("keyboard getting inserted is "+currentKeyboard);
      taps['keyboard'] = currentKeyboard;
      console.log("keyboard getting inserted is "+ taps['keyboard']);
      taps['kbseq']=currentRank;
      console.log("keyboard getting inserted is "+  taps['kbseq']);
     }
    // taps['keyboard'] = 'xyz';
     console.log("Session number is "+ i);
    taps['language']='hn';
    taps['sessions']=i;
    taps['nmph']=phrasesps;
    taps['phraseNumber']=j+1;
    taps['phraseShown']=arrph[j];
    console.log("Taps array pushing into taplogsarray " + JSON.stringify(taps));
    tapLogsArray.push(JSON.stringify(taps));
    
    
   }
  
 }
  currentRank++;
 // console.log("STRINGIFIED TAPS ARRAY "+tapLogsArray);
 return tapLogsArray;
}
     function syncToServer()
     {
        console.log("I AM IN THE SYNC TO SERVER FUNCTION ");
           
				var xmlhttp;
			    
			    if (window.XMLHttpRequest) {
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				xmlhttp.onreadystatechange = function() 
        {

				    if (this.readyState == 4 && this.status == 200) 
            {
				    	
				        var responseText = this.responseText;
				        console.log("Response:"+responseText);

						
				        if(responseText == "Data saved successfully")
                {
                  document.getElementById("containerdisp").innerHTML = 'Data saved.'
                }
				        else
                {
                document.getElementById("containerdisp").innerHTML = 'Server could not save data. Please try again.'+responseText+'<br><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';
              }
				    }else if(this.readyState == 4 && this.status != 200){

				    	document.getElementById("containerdisp").innerHTML = 'Server went away. Data not saved. Error code:'+this.status+'.><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';

				    }
				};
				var url = "savePhrases.php";
        console.log("LENGTH OF ALL PHRASES ARRAY" + tapLogsArray.length);
        console.log("IN SYNC TO SERVER Sending "+tapLogsArray)
				var params ="id="+uid+"&phrases=["+tapLogsArray+"]";
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);


        document.getElementById("containerdisp").innerHTML = "Saving data...";
      }


        function callUser() {
            window.location.href = "index3.php?study_id=" + sid;
        }
    
        function getRamp()
        {
          newRamp.forEach((item) => {
           const [range, difficulty] = item.split(" ");
           const [start, end] = range.split("-");
           console.log(`Start: ${start}, End: ${end}, Difficulty: ${difficulty}`);
      });
        }
        function fetchData() 
        {
            var xmlhttp;

            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

         
            document.getElementById("cont3").innerHTML = "";
            document.getElementById("cont4").innerHTML = ""; // Clear the cont2 element

            var getData = "moderator.php?studyid=" + sid;
            console.log("url:" + getData);
            xmlhttp.open("GET", getData, true);
            xmlhttp.send();

            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseText = this.responseText;
                    document.getElementById("cont3").innerHTML = responseText;
                    document.getElementById("cont4").innerHTML = '<br><button type="button" class="symbol" onclick="callUser()">+</button>';
                }
            };
        }
</script>
</html>

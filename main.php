<?php
error_reporting(E_ERROR | E_PARSE);
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles/main.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Micro+5+Charted&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Teko:wght@300..700&display=swap" rel="stylesheet">
    <title>Session TYPING</title>

	
</head>
<body onload="onLoad()">
          <h3 id="useKeyboard"></h3>
        <div id="container" class="container" style="width: 80%;">
			<form autocomplete="off">
				<label id="toType"></label>
				<br/>
				<input type="text" onkeyup="calculate()" id="tt" onKeyPress="return disableEnterKey(event)" autocomplete="off" autofocus required/>
			</form>
			
				<div style="display:inline-block;width:100%;padding:0;margin:0;align:right;">
				</div>
				<p id="cpm">SPEED: </p>
				<p id="error">ERROR: </p>
			
				
			<button type="button" onclick="showNextPhrase()" enabled="false" id="go">Next</button>
				
			<style>
			body  
			{
            background: white;
            font-family: "Roboto Condensed", sans-serif;
	        font-optical-sizing: auto;
            }
			
			</style>
				<audio id="myAudio">
				  <source src="" type="audio/wav">
				  <!-- <source src="horse.mp3" type="audio/mpeg"> -->
				  Your browser does not support the audio element.
				</audio>
			
		</div>
		<div id="res" class="res">
        </div>

        <div id="feedbackTable"></div>
		<div> 
		<div>
		    <canvas id="graph" width="50" height="50"></canvas>
		</div>

		<script>
			
    	    var startTimestamp=-1;
			var endTimestamp=-1;
			var ksLog = "";
            var uid="";
            var sid="";
			var nmph=-1;
			var noKeyboard=-1;
			var counter=0;
			var checkin=0;
            var phrases=[];
			var backspaceCount = 0;
			var toTypeArray;
			var toTypeArrayIndex=0;
			var dependentVariable="";
			var maxSession=0;
			var uCode = -1;
			var sCode = -1;
			var oldText="";
            var currsesh=0;
			var kbseq=1;
			var currph=0;
			var totype=''; 
            var lookupData;
			var keyboardLookupData;
            var textSoFar =''
			var trainingWords;
			var previousValue='';
			var prevKeyboard=null;
			var countdis=0;
            var prevPosition=-1;
            var backarray=[];
			var practiceWords;
			var practiceSession;
			var mainTask1; 
			var mainTask2;
			var infarray=[]
		    var farray=[]
		    var ifcarray=[]
		    var corrarray=[]
		   	var ifc=0;
		    var fix=0;
		    var inf=0;
		    var corr=0;

	
            ////console.log("hi")
			
			
			document.addEventListener('keydown', function(event) {
            //console.log('Key pressed:', event.key);
            //console.log('Key code:', event.keyCode);
            //console.log('Key location:', event.location);
            //console.log('Shift key pressed:', event.shiftKey);
            //console.log('Ctrl key pressed:', event.ctrlKey);
            //console.log('Alt key pressed:', event.altKey);
            //console.log('Meta key pressed:', event.metaKey);
        });

			var tapLogsArray=[];
			var d;
            const urlParams = new URLSearchParams(window.location.search);
              uid = urlParams.get('userid');
              sid = urlParams.get('studyid');
			  sid = sid.substring(2, sid.length - 2);
			  nmph = urlParams.get('p');
			  sessions=urlParams.get('s');
          
			  //console.log('selected order is ');
              //console.log("User ID:", uid);
              //console.log("Study ID:", sid);
			  //console.log("nmph:", nmph);
			  //console.log("NO. of sessions", sessions);
			//   if(uid!=null && sid!=null)
			//   {
			// 	getKbseq();
			//   }
            function getKbseq()
			{
				
           var xmlhttp;

           if (window.XMLHttpRequest) {
               xmlhttp = new XMLHttpRequest();
           } else {
               xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
           }

           xmlhttp.onreadystatechange = function () {
               if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
				   kbseq = response.kbseq;
				   getCurrent();
			       //console.log("NEW Kbseq obtained " + kbseq);
                
               } else if (this.readyState == 4 && this.status != 200) {
                   //console.log("Something went wrong");
                   document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:' + this.status + '.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
               }
           };

           var url = "getKbseq.php";
           var params = "userid=" + uid + "&studyid=" + sid;
           xmlhttp.open("POST", url, true);
           xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
           xmlhttp.send(params);
			}
			function onLoad()
            {
			
                //console.log("IN THE ONLOAD FUNCTION");
              uid = urlParams.get('userid');
              sid = urlParams.get('studyid');
              //console.log("User ID:", uid);
              //console.log("Study ID:", sid);
			  //console.log("nmph:", nmph);

      if (uid && sid) 
	  {
  
    //console.log("User ID:", uid);
    //console.log("Study ID:", sid);

     } else 
	 {
 
    //console.error("User ID or Study ID parameter is missing.");
    }           getStudyInfo();
                getKbseq();
				//console.log("CURRENT SESSION IS betwwen onload and loadphrases "+currsesh);
				//console.log("load");
				d = new Date();

				uniqueIdentifier = d.getTime();
				var randomNumber = Math.floor(Math.random() * 42);
				uniqueIdentifier=uniqueIdentifier+""+randomNumber;
	

      document.getElementById('tt').addEventListener('click', function() 
	  {
         calcFixes();
		 
      });
	  document.getElementById("tt").addEventListener("input", function(event) 
	 {
           calcIncorrectFixed();
     });

				document.getElementById("tt").addEventListener("input",logg);
				return;

			} 

			function logg(){
         
		 var txt = document.getElementById("tt").value;
		 //document.getElementById("counter").text += "#"+ txt;
		 var diff = "";
	 
	 
		 if(txt.length > oldText.length){
	 
			 message = "A";
			 diff = txt.substring(oldText.length);
	 
		 }
		 else{
			 message = "B";
			 diff = oldText.substring(txt.length);
		 }
	 
		 //console.log("new Txt len:"+txt.length + ", old text length:" + oldText.length);
	 
		 
		 ksLog += "#"+message+";"+txt+";"+diff;
		 //console.log(ksLog);
		 ////console.log(message);
		 //document.getElementById("counter").innerHTML = ksLog;
		 oldText = txt;
	 }
	function calcIncorrectNotFixed(totype,textSoFar) 
	{
     inf = 0;
 
	inf=damerauLevenshteinDistance(totype,textSoFar);
	return inf;

 }
 function segmentIntoCustomGraphemes(text, label) {
    const customGraphemes = ["क्ष", "त्र", "ज्ञ"];
    let segments = [];
    let i = 0;

    while (i < text.length) {
        let matched = false;

        // Check for custom graphemes
        for (let grapheme of customGraphemes) {
            if (text.startsWith(grapheme, i)) {
                segments.push(grapheme);
                i += grapheme.length;
                matched = true;
                break;
            }
        }

        // If no custom grapheme matches, segment normally
        if (!matched) {
            let segmenter = new Intl.Segmenter('hi', { granularity: 'grapheme' });
            let seg = Array.from(segmenter.segment(text.slice(i, i + 1)))[0].segment;
            segments.push(seg);
            i += seg.length;
        }
    }

    //console.log(`${label} Graphemes:`, segments);
    return segments;
}

function damerauLevenshteinDistance(source, target) {
    if (!source || source.length === 0) {
        return (!target || target.length === 0) ? 0 : target.length;
    } else if (!target) {
        return source.length;
    }

    let sourceGraphemes = segmentIntoCustomGraphemes(source, "Source");
    let targetGraphemes = segmentIntoCustomGraphemes(target, "Target");
    let sourceLength = sourceGraphemes.length;
    let targetLength = targetGraphemes.length;
    let score = Array.from({ length: sourceLength + 2 }, () => Array(targetLength + 2).fill(0));

    let INF = sourceLength + targetLength;
    score[0][0] = INF;
    for (let i = 0; i <= sourceLength; i++) {
        score[i + 1][1] = i;
        score[i + 1][0] = INF;
    }
    for (let j = 0; j <= targetLength; j++) {
        score[1][j + 1] = j;
        score[0][j + 1] = INF;
    }

    let sd = {};
    let combinedStrings = sourceGraphemes.join('') + targetGraphemes.join('');
    let combinedStringsLength = combinedStrings.length;
    for (let i = 0; i < combinedStringsLength; i++) {
        let letter = combinedStrings[i];
        if (!sd.hasOwnProperty(letter)) {
            sd[letter] = 0;
        }
    }

    for (let i = 1; i <= sourceLength; i++) {
        let DB = 0;
        for (let j = 1; j <= targetLength; j++) {
            let i1 = sd[targetGraphemes[j - 1]] || 0;
            let j1 = DB;

            if (sourceGraphemes[i - 1] === targetGraphemes[j - 1]) {
                score[i + 1][j + 1] = score[i][j];
                DB = j;
            } else {
                score[i + 1][j + 1] = Math.min(score[i][j], Math.min(score[i + 1][j], score[i][j + 1])) + 1;
            }

            if (score[i1] && score[i1][j1] !== undefined) {
                score[i + 1][j + 1] = Math.min(score[i + 1][j + 1], score[i1][j1] + (i - i1 - 1) + 1 + (j - j1 - 1));
            }
        }
        sd[sourceGraphemes[i - 1]] = i;
    }

    return score[sourceLength + 1][targetLength + 1];
}


 function countGraphemeClusters(text) {
    let segmenter = new Intl.Segmenter('hi', { granularity: 'grapheme' });
    let segments = Array.from(segmenter.segment(text));
    return segments.length;
}



function calcIncorrectFixed() {
   	//console.log("in the if fucntion the ifc count is "+ifc);
         
            var currentValue = document.getElementById("tt").value;
            //console.log("Input:", currentValue);
            currentValue=cleanUpUnicode(currentValue);
			currentValue=cleanUpKeyboard(currentValue);
       // Count backspaces
    if(previousValue.length>currentValue.length)
    {
     ifc += previousValue.length - currentValue.length;
	
    }

    previousValue = currentValue;
        return ifc;
}

		 
		
		 function calcCorrected(toType,textSoFar)     //Function for calculating C value (correct characters typed)
		 {
			corr=0;
		    var infixed=inf=damerauLevenshteinDistance(totype,textSoFar);
			var presentedLength=toType.length;
			var transcribedLength=textSoFar.length;
			var maxLength=Math.max(presentedLength,transcribedLength);
			corr = maxLength-infixed;
          
			//console.log("CORRECTED KEYSTROKES ARE " +corr);
			return corr;
		 }
		 function calcFixes()                     //function for calculating F value (Fixes calue comprising of backspace and left cursor key presses)
		 {	
		const inputBox = document.getElementById('tt');
        const cursorPosition = inputBox.selectionStart;
        //console.log("cursor position is "+cursorPosition);
        const inputLength = inputBox.value.length;
        //console.log("inputLength is "+inputLength);
        const distance = inputLength - cursorPosition;
        if(distance>0 &&prevPosition!=cursorPosition )
        {
            countdis++;
        }
        prevPosition=cursorPosition;
        return countdis;
		 }
	
		function resetinf()
		{
			inf=0;
			corr=0;
			ifc=0;
			fix=0;

		}

			function syncToServer()
			{
                //console.log("i am iN SYNC TO SERVER FUNCTION ");
				var xmlhttp;
			    
			    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				xmlhttp.onreadystatechange = function() {

				    if (this.readyState == 4 && this.status == 200) {
				    	
				        var responseText = this.responseText;
				        //console.log("Response:"+responseText);

				        if(responseText == "Data saved successfully")
						{
							//console.log("data saved ");
				        	return;
						}

				    }else if(this.readyState == 4 && this.status != 200){

				    	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';

				    }
				};
				var url = "saveTyped.php";
				var params ="userid="+uid+"&studyid="+sid+"&var="+dependentVariable+"&phrases=["+tapLogsArray+"]"+"&backspace=["+backarray+"]"+"&corrected=["+corrarray+"]"+"&incorrfix=["+ifcarray+"]"+"&incorrnot=["+infarray+"]"+"&fixes=["+farray+"]"+"&keyboard="+keyboard;
				//console.log("PARAMETERS SENDING TO SAVETYPED IS "+params);
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);

			}
			function userResult()
			{
				window.location.href = "userresult.html?study_id="+sid+"&userid="+uid;

			}
			function refresh()
			{
				//console.log("COUNTER IS "+counter);
				//console.log("in the refresh function Current session "+currsesh)+" and max session "+ maxSession;
				//console.log("current kbseq is "+kbseq);
				if(currsesh==maxSession)
				{
                    window.location.href = "Congrats.php?userid=" + uid + "&kb="+keyboard+"&studyid=" + sid;
				}
				else
				{
				location.replace(location.href);
				}
			}
			
			function getCurrent() 
{
				checkin++;
    var xmlhttp;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            uid = response.userID;
            sid = response.studyID;
            currsesh = response.currentSession;
            keyboard = response.keyboard;
			uid = uid.charAt(0).toUpperCase() + uid.slice(1);
			keyboard = keyboard.charAt(0).toUpperCase() + keyboard.slice(1);
			document.getElementById('useKeyboard').innerHTML="Dear " +uid+" , Make sure to use  "+ keyboard+ " Keyboard while typing";
            if ((currsesh==null) && (checkin==5))  
			{
                document.getElementById("useKeyboard").innerHTML = '';
				//window.location.href = "Congrats.php?userid=" + uid + "&kb="+keyboard+"&studyid=" + sid;
				document.getElementById("container").innerHTML = '<h1>ALL SESSIONS DONE </h1><br> <br><br>Check Analytics <button onClick="userResult()">results</button>';
            } 
		
        
            currph = response.currentPhrase;

			//console.log(" getcurrent User ID:", uid);
            //console.log("getcurrent  Study ID:", sid);
            //console.log("getcurrent  Current Session:" + currsesh);
            //console.log("getcurrent  Current Phrase:" + currph);
            //console.log("getcurrent Current Keyboard:" + keyboard);

            loadPhrases();
			
			if(uid!=null && keyboard!=null)
			{
			    var upperKeyboard=keyboard.toUpperCase();
			alert("Dear "+ uid +" , Make sure you are using "+upperKeyboard);
			}
            //console.log("toType array index is ", toTypeArrayIndex);

        } else if (this.readyState == 4 && this.status != 200) {
            //console.log("Something went wrong");
            document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:' + this.status + '.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
        }
    };

    var url = "getcurrent.php";
    var params = "userid=" + uid + "&studyid=" + sid + "&nmph=" + nmph + "&kbseq=" + kbseq;
	//console.log("Sending params "+params +" to getCurrent.php");
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}

function renderGraph(data) {
    var minSession = Math.min(...data.map(function(d) { return d.session; }));
    var maxSession = Math.max(...data.map(function(d) { return d.session; }));

    // Generate an array of sessions from minSession to maxSession
    var uniqueSessions = Array.from({ length: maxSession - minSession + 1 }, (_, i) => minSession + i);

    var uniqueKeyboards = [...new Set(data.map(function(d) { return d.keyboard; }))];

    var datasets = uniqueKeyboards.map(function(keyboard, index) {
        var avgCPM = [];

        uniqueSessions.forEach(function(session) {
            var sessionData = data.find(function(d) { return d.session === session && d.keyboard === keyboard; });

            if (sessionData) {
                avgCPM.push(sessionData.avg_cpm);
            } else {
                avgCPM.push(null);
            }
        });

        var colors = ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'];
        var darkerColors = ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'];

        return {
            label: keyboard,
            data: avgCPM,
            backgroundColor: colors[index % colors.length], 
            borderColor: darkerColors[index % darkerColors.length], 
            borderWidth: 1
        };
    });

    var ctx = document.getElementById('graph').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: uniqueSessions,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Session'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Average CPM'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Average CPM per Session', 
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                }
            }
        }
    });
}





					 
function getFeedback() 
{
    //console.log("in the GET FEEDBACK FUNCTION ");
    var xmlhttp;
	var xmlhttp2;

  

	if (window.XMLHttpRequest) { 
        xmlhttp2 = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
    }
	xmlhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var response = JSON.parse(this.responseText);
        //console.log("Session stats response is " + response);
        renderTable(response);
		renderGraph(response);


    } else if (this.readyState == 4 && this.status != 200) {
        // Handle error
    }
};

    var url = "sessionStats.php";
    var params = "userid=" + uid + "&studyid=" + sid + "&currsesh=" + currsesh +"&keyboard="+keyboard ;
    //console.log("in the sessionStats function and parameters which are sent are " + params);
    xmlhttp2.open("POST", url, true);
    xmlhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp2.send(params);

}

function renderTable(response)
{
	var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        var currentDate = yyyy + '-' + mm + '-' + dd;

        var tableHTML = "<h4>Feedback for all sessions till today</h4><table border='1'><tr><th>Sr No.</th><th>Session No.</th><th>Date</th><th>Keyboard</th><th>Average Error Rate (%)</th><th>Min Error Rate (%)</th><th>Max Error Rate (%)</th><th>Average CPM</th><th>Min CPM</th><th>Max CPM</th></tr>";

        for (var i = 0; i < response.length; i++) {
            var rowData = response[i];
            tableHTML += "<tr>";

           
            tableHTML += "<td>" + (i + 1) + "</td>";

           
            if (rowData.session == currsesh && rowData.keyboard == keyboard) {
                tableHTML += "<td style='font-weight: bold;'>" + rowData.session + " (Current)</td>";
            } else {
                tableHTML += "<td>" + rowData.session + "</td>";
            }

            
            if (rowData.date == currentDate) {
                tableHTML += "<td style='font-weight: bold;'>" + rowData.date + " (Today)</td>";
            } else {
                tableHTML += "<td>" + rowData.date + "</td>";
            }
            tableHTML += "<td>" + rowData.keyboard + "</td>";
            tableHTML += "<td>" + rowData.avg_error_rate + "</td>";
            tableHTML += "<td>" + rowData.min_error_rate + "</td>";
            tableHTML += "<td>" + rowData.max_error_rate + "</td>";
            tableHTML += "<td>" + rowData.avg_cpm + "</td>";
            tableHTML += "<td>" + rowData.min_cpm + "</td>";
            tableHTML += "<td>" + rowData.max_cpm + "</td>";
            tableHTML += "</tr>";
        }
        tableHTML += "</table>";

        var feedbackDiv = document.getElementById("feedbackTable");


        feedbackDiv.innerHTML = tableHTML;
}

			function getStudyInfo()
			{
				//console.log("in get study function");
				var xmlhttp;
			    
			    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				xmlhttp.onreadystatechange = function() {

				    if (this.readyState == 4 && this.status == 200) {
				    	//console.log("back");
				    	
                      var response = JSON.parse(this.responseText);
                      noKeyboard=response.noKeyboard;
					  maxSession=response.maxSess;
					  lookupData=response.unicodeLookup;
					  keyboardLookupData=response.keyboardLookup;
				
					  //console.log("Total No. of keyboards are "+noKeyboard);
					  //console.log("Max sessions are "+maxSession);
					  //console.log("Lookup data  is  "+lookupData);
					  //console.log("KeyboardLookup data  is  "+keyboardLookupData);
					  
			  }else if(this.readyState == 4 && this.status != 200){

				    	//console.log("Something went wrong");
				    	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
				    	
				    }
				};
				var url = "getStudy.php";				
				var params = "sid="+sid;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);
           
			}
			function loadPhrases()
            {
                var xmlhttp;
			    
			    if (window.XMLHttpRequest) {
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				xmlhttp.onreadystatechange = function() {

				    if (this.readyState == 4 && this.status == 200) {
				    	//console.log("back");
				    	
                      var response = JSON.parse(this.responseText);
                      uid = response.userID;
                      sid = response.studyID;
                      phrases = response.phrases;
					  currsesh=response.currentSession;
					  currph=response.currentPhrase;
					  //console.log("In the load PHRASES FUNCTION");
                     //console.log("User ID:", uid);
                     //console.log("Study ID:", sid);
                     //console.log("Phrases:", phrases);
					 //console.log("CURRENT SESSION "+currsesh + "and CURENT PHRASES IS "+currph);
                     trainingWords=phrases;
					
						
					 
               //console.log("ALL TRIANING WORDS ARE "+trainingWords);
	
			   window.onload = saveDependentVariable(currph);
               phrases.forEach(function(phrase, index) {
            //console.log("Phrase", index + 1 + ":", phrase);
	
        });
				       
				    }else if(this.readyState == 4 && this.status != 200){

				    	//console.log("Something went wrong");
				    	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
				    	

				    }
				};
		        //console.log("IN THE  LOAD FUNCTION current session  is "+currsesh);
				var url = "getPhrases.php";				
				var params = "userid=" + uid + "&studyid=" + sid + "&nmph=" + nmph + "&currsesh=" + currsesh + "&keyboard=" + keyboard + "&kbseq=" + kbseq;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);
           
			}

			function saveDependentVariable(currph)
            {
				
                 //console.log("in the save depenedent function");
	
				if(navigator.onLine ){

					
                    //console.log("in the save depenedent function");
	
					document.getElementById("container").style.display = "inline-block";

				
						document.getElementById("toType").innerHTML = trainingWords[0];
						toTypeArray = trainingWords;
					    toTypeArrayIndex=0;
						if((toTypeArrayIndex+1)==toTypeArray.length){
                        document.getElementById("go").innerHTML = "Done";
                        }

						//console.log("TO TYPE ARRAY INDEX IS"+toTypeArrayIndex);
						document.getElementById("tt").focus();
				}
				else
				{
					//console.log("browser is offline");
				}

			}


			function showNextPhrase(){
                totype = document.getElementById("toType").innerHTML.trim();
				textSoFar = document.getElementById("tt").value;
				//console.log("ShowNextPhrase ====> TextSoFar "+textSoFar+" totype"+totype);
				if(textSoFar.length<=totype.length/2)
				{
					alert("Please complete the presented text before proceeding");
					document.getElementById("tt").focus();
					return;
				}
				
				counter++;
				if(toTypeArrayIndex>=nmph)
				{
					document.getElementById("container").innerHTML = '<span class="spanWhite">ALL PHRASES DONEE </span>';
				}
	
				backarray=[];
				infarray=[];
			    farray=[];
			    ifcarray=[];
			    corrarray=[];
				   totype = document.getElementById("toType").innerHTML.trim();
                  //console.log(" totype:" + totype);
                   textSoFar = document.getElementById("tt").value;
                 //console.log("  text so far :" + textSoFar);
				calcCorrected(totype,textSoFar);
			    calcIncorrectNotFixed(totype,textSoFar);
				if(toTypeArrayIndex <toTypeArray.length)
                {
				  infarray.push(inf);	
			      corrarray.push(corr);
                }
                textSoFar=cleanUpUnicode(textSoFar);
				calcCorrected(totype,textSoFar);
			    calcIncorrectNotFixed(totype,textSoFar);
				if(toTypeArrayIndex <toTypeArray.length)
                {
				  infarray.push(inf);	
			      corrarray.push(corr);
                }
                textSoFar=cleanUpKeyboard(textSoFar);
				calcCorrected(totype,textSoFar);
			    calcIncorrectNotFixed(totype,textSoFar);
              if(toTypeArrayIndex <toTypeArray.length)
               {
		  		  backarray.push(ifc);
				  infarray.push(inf);
			      farray.push(ifc+countdis);
			      ifcarray.push(ifc);
			      corrarray.push(corr);
               }
			/*for(i=0;i<backarray.length;i++)
			{
				//console.log("backspace array of phrases["+(i+1)+"] "+backarray[i]);
			}
			for(i=0;i<infarray.length;i++)
			{
				//console.log("INF array of phrases["+(i+1)+"] "+infarray[i]);
			}
			for(i=0;i<ifcarray.length;i++)
			{
				//console.log("IF array of phrases["+(i+1)+"] "+ifcarray[i]);
			}
			for(i=0;i<farray.length;i++)
			{
				console.log("Fixes array of phrases["+(i+1)+"] "+farray[i]);
			}
			for(i=0;i<corrarray.length;i++)
			{
				console.log("Corrected C array of phrases["+(i+1)+"] "+corrarray[i]);
			}*/
			document.getElementById("tt").focus();
			document.getElementById("go").addEventListener("click", function() {
    var inputField = document.getElementById("tt");
   
});

            previousValue='';
			countdis=0;
             prevPosition=-1;
			resetinf();
            tapLogsArray=[];
               
			
				currph++;
				toTypeArrayIndex++;
				console.log("Array index is "+toTypeArrayIndex);
				var x = document.getElementById("myAudio");
				var accuracy = "";
				var ext = "wav";
				var toType = "";
				var typed = "";
			
				var phrase = {};
                    console.log("TAPS LOG ARRAY CONTENT "+tapLogsArray);
					phrase["phraseSequenceNumber"] = toTypeArrayIndex;

			
					toType = toTypeArray[toTypeArrayIndex-1];
					phrase["phraseShown"] = toType;
					
           
					
				//editdistance1 is (distance)error without no cleanups
				//editdistance2 is (distance)error considering unicode exceptions 
				//editdistance3 is (distance)error considering unicode exceptions + keyboard exceptions

					phrase["phraseTyped"] = document.getElementById("tt").value;	
					console.log("phrase typed by user is ");				
					phrase["editdistance1"] = getED(phrase["phraseTyped"],phrase["phraseShown"]);
					console.log("EDIT DISTANCE without cleanup IS " + getED(phrase["phraseTyped"],phrase["phraseShown"]));
					textSoFar = cleanUpUnicode(phrase["phraseTyped"]);
					phrase["editdistance2"] = getED(textSoFar,phrase["phraseShown"]);
					console.log("EDIT DISTANCE with cleanup UNICODE IS of  " + getED(textSoFar,phrase["phraseShown"]));
					textSoFar = cleanUpKeyboard(textSoFar);
					phrase["editdistance3"] = getED(textSoFar,phrase["phraseShown"]);
					console.log("EDIT DISTANCE with cleanup KEYBOARD IS " + getED(textSoFar,phrase["phraseShown"]));

					phrase["timeTaken"] = endTimestamp-startTimestamp;
					

					accuracy = getStarRating(getErrorRate(phrase["phraseShown"],phrase["phraseTyped"]));

					

					tapLogsArray.push(JSON.stringify(phrase));
					console.log("TAPS LOG ARRAY CONTENT "+tapLogsArray);
					syncToServer();
					
					//cleanup
					document.getElementById("cpm").innerHTML = " ";
					document.getElementById("error").innerHTML = " ";
					document.getElementById("tt").value = "";
					ksLog = "";
					oldText = "";
					
					x.src = "fb/"+accuracy+"."+ext;
				
					x.play();
				


				if(((toTypeArrayIndex)==toTypeArray.length) )
				{
					console.log("current keyboard is "+keyboard);
				   
					setTimeout(function() { getFeedback();}, 1000);

					document.getElementById("container").innerHTML = '<h1>ALL PHRASES DONE </h1><br><button onClick=refresh()>Next session</button><br>';
					if(currsesh==maxSession)
				    {
						document.getElementById("container").innerHTML = '<h1>ALL PHRASES DONE </h1><br><button onClick=refresh()>Done</button><br>';
				    }
                     toTypeArrayIndex = 0;
				}else{

					if((toTypeArrayIndex+1)==toTypeArray.length){

						document.getElementById("go").innerHTML = "Done";
					}
				
					
						document.getElementById("toType").innerHTML = toTypeArray[toTypeArrayIndex];
				

					//console.log(document.getElementById("toType").innerHTML);


					document.getElementById("cpm").innerHTML = " ";
					document.getElementById("error").innerHTML = " ";
					document.getElementById("tt").value = "";
					ksLog = "";
					oldText = "";

					startTimestamp = -1;
					endTimestamp = -1;


				}

			}


			
			function cleanUpUnicode(str)
			{
				if(lookupData)
			{
				////console.log("i m in cleanup Unicode function");
				while(str.search("  ") >0){
			
					str = str.replace("  "," ");
				}
				////console.log("String before cleanup "+ str);
				var samp=str.split('');
				////console.log("Breakdown before cleanup "+samp);
				lookupData.forEach(function(item) 
				{
						var pair = item.split(',');
                        var oldValue = pair[0];
                        var newValue = pair[1];
						str=str.replace(oldValue,newValue);
                });

                ////console.log("Returning string "+str +"after cleanup");
				samp=str.split('');
				////console.log("breakdown after cleanup "+samp);
				
				return str.toLowerCase();
			}
			else return str;
              
			}
			function cleanUpKeyboard(str)
			{
				if(keyboardLookupData)
			{
               //consp;e.log("i m in cleanup Keyboard function");
				while(str.search("  ") >0){
					//str = str.replace("sss","s");
					str = str.replace("  "," ");
				}
				////console.log("String before cleanup "+ str);
				var samp=str.split('');
				////console.log("Breakdown before cleanup "+samp);
				keyboardLookupData.forEach(function(item) 
				{
						var pair = item.split(',');
                        var oldValue = pair[0];
                        var newValue = pair[1];
						str=str.replace(oldValue,newValue);
                });
                ////console.log("Returning string "+str +"after cleanup");
				samp=str.split('');
				////console.log("breakdown after cleanup "+samp);
				
				return str.toLowerCase();
			}
			else return str;
			}
		function displayFeedbackTable(averageErrorRate, minErrorRate, maxErrorRate, averageCPM, minCPM, maxCPM) 
		{
			var table = document.createElement("table");
    
   
    var rows = [
        { label: "Average Error Rate", value: averageErrorRate },
        { label: "Minimum Error Rate", value: minErrorRate },
        { label: "Maximum Error Rate", value: maxErrorRate },
        { label: "Average CPM", value: averageCPM },
        { label: "Minimum CPM", value: minCPM },
        { label: "Maximum CPM", value: maxCPM }
    ];
    
    
    rows.forEach(function(rowData) {
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.innerHTML = rowData.label;
        cell1.className = "label"; 
        cell2.innerHTML = rowData.value;
    });

    document.getElementById("res").appendChild(table);
        }
		

			function calculate(){

				toType = document.getElementById("toType").innerHTML;
				////console.log("VEDANT HERE calc:"+toType);
				var textSoFar = document.getElementById("tt").value;
                ////console.log("VEDANT HERE text so far :"+textSoFar);

				if(textSoFar.length==0)
					return;

				if(startTimestamp<0){

					startTimer();

				}
				updateTimestamp();

			
				// document.getElementById("infixed").innerHTML =calcIncorrectFixed()+" if presses";
				calcIncorrectFixed();
				document.getElementById("cpm").innerHTML = getCPM() + " cpm";
                ////console.log("this is the calculated CPM --> " +getCPM());

				var currstring=cleanUpKeyboard(textSoFar);
				currstring=cleanUpUnicode(currstring);
				document.getElementById("error").innerHTML = getErrorRate(toType,currstring) + " %";
	            ////console.log("this is the calculated error rate  --> " +getErrorRate(toType,textSoFar));
			
				
			    calcFixes();
			}
        
            
			function getCPM()
			{
				var textSoFar = document.getElementById("tt").value;
				var str=textSoFar;
				str = str.trim();
				str = cleanUpUnicode(str);
				var cpm=0;
	
				var timeTaken = (endTimestamp - startTimestamp)/60000.0;

				if(timeTaken>0)
					cpm=Math.round((str.length-1)/timeTaken);
				else
					cpm=0;

				return cpm;
			}

			function getErrorRate(toType, textSoFar)
			{
				if(toType.length>0)
					toType = toType.trim();
				////console.log("err:"+toType+", "+textSoFar);
				var str=textSoFar;
				str = str.trim();
				str = cleanUpUnicode(str);
				var maxStringLength = Math.max(str.length,toType.length);
				var minStringLength = Math.min(str.length,toType.length);
				var errorrate;

			var ed = damerauLevenshteinDistance(toType,str);
			////console.log("demerauLevenstein distance is  "+ed);
			////console.log(" length of " + textSoFar +" is "+ str.length);
			////console.log("error rate is "+Math.round((ed/maxStringLength)*100.0,2));
				if(ed>minStringLength)
					errorrate = 100;
				else
					errorrate = Math.round((ed/maxStringLength)*100.0,2);

				return errorrate;

			}

			function getStarRating(correctnessRatio){

				var starsVal=0;

				if( correctnessRatio == 0){

					starsVal = "*";
					return "five";

		
				}else if(correctnessRatio > 0 && correctnessRatio <= 20){

					starsVal = "";
					return "four";

				}else if(correctnessRatio > 20 && correctnessRatio <= 40){

					starsVal = "*";
					return "three";

				}else if(correctnessRatio > 40 && correctnessRatio <= 60){

					starsVal = "";
					return "two";

				}else{

					starsVal = "*";
					return "one";
				}

			}

			function getED(textSoFar,toType) 
			{

            toType = toType.trim();
            var str=textSoFar;
            str = str.trim();
            var ed = damerauLevenshteinDistance(toType,str);
            return ed;

            }

			function startTimer()
			{
				
				startTimestamp = (new Date()).getTime() ;
			
			}
			function updateTimestamp()
			{
				
				endTimestamp = (new Date()).getTime();
				
			}		


			function shuffle(arra1) 
			{
    			var ctr = arra1.length, temp, index;

				// While there are elements in the array
				    while (ctr > 0) {
				// Pick a random index
				        index = Math.floor(Math.random() * ctr);
				// Decrease ctr by 1
				        ctr--;
				// And swap the last element with it
				        temp = arra1[ctr];
				        arra1[ctr] = arra1[index];
				        arra1[index] = temp;
				    }
				    return arra1;
				}

	function disableEnterKey(e){ 
		var key; 
	    if(window.event){ 
		  
		    key = window.event.keyCode; 
	
		    } else { 
		  
		    key = e.which;      
		    } 
	
		    if(key == 13){ 
		      
		      showNextPhrase();
	
		    return false; 

		    } else { 
		      
		   
		    return true; 
		    } 
	
		} 				

</script>
</body>

</html>
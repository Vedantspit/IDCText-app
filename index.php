<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/_dbconnect1.php';
require 'vendor/autoload.php'; // Path to PhpSpreadsheet autoload.php

use PhpOffice\PhpSpreadsheet\Reader\Csv;

$nosess = 0;
$task = 'test';
$sid = 0;
$nmph = 0;
$rf = 1;
$randomization = '';
$studyType = '';
$nokeyboards = 0;
$keyboardArray = serialize([]);

// Default values if files are not uploaded
$dataLookup = NULL;
$keyLookup = NULL;
$difficultyMatrix = NULL;
$phrases = NULL;

if (isset($_POST["submit"])) {
    echo "YO";
    $sid = $_POST["studyid"];
    $nosess = $_POST["sessno"];
    $nmph = $_POST["phrasesPerSession"];
    if (isset($_POST["repetitionFactor"]) && $_POST["repetitionFactor"] !== '') {
        $rf = $_POST["repetitionFactor"];
    }
    echo "study id is".$sid;
    echo "no. of sessions are".$nosess;
    echo "nmph are".$nmph;
    echo "rf is ".$rf;
    
    $randomization = isset($_POST["randomization"]) ? $_POST["randomization"] : '';
    $studyType = isset($_POST["studyType"]) ? $_POST["studyType"] : '';
    echo "Study Type: " . $studyType;
    
    $nokeyboards = isset($_POST["nokeyboards"]) ? $_POST["nokeyboards"] : 0;
    echo "nokeyboards is ".$nokeyboards;
    
    $keyboards = isset($_POST["keyboards"]) ? $_POST["keyboards"] : '';
    echo "keyboards is ".$keyboards;
    
    if ($keyboards) {
        $keyboardArray = explode(",", $keyboards);
        $keyboardArray = serialize($keyboardArray);
    }

    $reader = new Csv();

    // Handle CSV file upload
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $file = $_FILES["file"]["tmp_name"];
        try {
            $spreadsheet = $reader->load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $phrases = [];
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (!empty($rowData)) {
                    $phrase = $rowData[0];
                    $difficulty = isset($rowData[1]) ? $rowData[1] : '';
                    $language = isset($rowData[2]) ? $rowData[2] : '';
                    $phrase = $difficulty . ' ' . $phrase . ' ' . $language;
                    $phrases[] = $phrase;
                    echo $phrase . " ";
                }
            }
            if ($randomization == "norandom") {
                $phrases = array_reverse($phrases);
            }
            $phrases = serialize($phrases);
        } catch (Exception $e) {
            echo "Error reading CSV file: " . $e->getMessage();
        }
    }

    // Handle Ramping file upload
    if (isset($_FILES["ramping_file"]) && $_FILES["ramping_file"]["error"] == UPLOAD_ERR_OK) {
        $fileRamping = $_FILES["ramping_file"]["tmp_name"];
        try {
            $spreadsheetRamping = $reader->load($fileRamping);
            $worksheetRamping = $spreadsheetRamping->getActiveSheet();

            $difficultyMatrix = [];
            foreach ($worksheetRamping->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (!empty($rowData)) {
                    $start = $rowData[0];
                    $end = $rowData[1];
                    $difficulty = $rowData[2];

                    $difficultyMatrix[] = $start . "-" . $end . " " . $difficulty;
                }
            }
            $difficultyMatrix = serialize($difficultyMatrix);
        } catch (Exception $e) {
            echo "Error reading Ramping file: " . $e->getMessage();
        }
    }

    // Handle Unicode Lookup file upload
    if (isset($_FILES["unicode_lookup"]) && $_FILES["unicode_lookup"]["error"] == UPLOAD_ERR_OK) {
        $fileLookup = $_FILES["unicode_lookup"]["tmp_name"];
        try {
            $spreadsheetLookup = $reader->load($fileLookup);
            $worksheetLookup = $spreadsheetLookup->getActiveSheet();

            $dataLookup = [];
            $keyLookup = [];
            foreach ($worksheetLookup->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (!empty($rowData)) {
                    $val = $rowData[0];
                    $dataLookup[] = $val;

                    if (isset($rowData[1])) {
                        $keyexcept = $rowData[1];
                        $keyLookup[] = $keyexcept;
                    }
                }
            }
            $dataLookup = serialize($dataLookup);
            $keyLookup = serialize($keyLookup);
        } catch (Exception $e) {
            echo "Error reading Unicode Lookup file: " . $e->getMessage();
        }
    }

    mysqli_set_charset($conn, "utf8");
    $check_sql = "SELECT COUNT(*) AS count FROM studies WHERE studyID = '$sid'";
    $result = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
      
    } else {
        $sql = "INSERT INTO studies (studyID, task, language, sessions, phrasesps, rf, randomization, difficulty, phrases, studyType, noKeyboard, keyboards, unicodeLookup, keyboardLookup) 
                 VALUES ('$sid', '$task', '$language', '$nosess', '$nmph', '$rf', '$randomization', '$difficultyMatrix', '$phrases', '$studyType', $nokeyboards, '$keyboardArray', '$dataLookup', '$keyLookup')";

        if (mysqli_query($conn, $sql)) {
            echo "File Successfully uploaded";
            $ahead = rand(10, 99);  // Random number for ahead
            $behind = rand(10, 99); // Random number for behind
            $sid=$ahead.$sid.$behind;
            header('location:index3.php?study_id=' .$sid);
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Session</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Micro+5+Charted&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Teko:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>IDCText : Application to Conduct Text Input Research Studies in Indian Languages </h1>
    <h2>Study Creation</h2>
    

    <div class="video-container">
        <h2>How to create a study</h2>
        <div class="rounded-iframe">
            <iframe width="100%" height="300" src="https://www.youtube.com/embed/zjOIJ0RGGFE" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    
    <form id="sessionForm" method="post" enctype="multipart/form-data">
        <label><h4>Enter STUDY ID:</h4></label>
        <input id="studyid" name="studyid" type="text" required autofocus ><br>
        
        <label for="sessno"><h4>Number of Sessions:</h4></label>
        <input type="number" id="sessno" name="sessno" required min="1" onchange="calculateMinPhraseCount()"><br>
        <label for="phrasesPerSession"><h4>Number of Phrases per Session:</h4></label>
        <input type="number" id="phrasesPerSession" name="phrasesPerSession" min="1" onchange="calculateMinPhraseCount()"><br>
        
        <h4>Randomization of Phrases</h4>
        <label><input id="random" name="randomization" value="random" type="radio" onclick="toggleRampingInputs()">Randomization of phrases Required</label>
        <label><input id="norandom" name="randomization" value="norandom" type="radio" onclick="toggleRampingInputs()">No Randomization</label>
        
        <div id="rampingContainer">
            <label for="repetitionFactor">Repetition Factor:</label>
            <input type="number" id="repetitionFactor" name="repetitionFactor" min="1" onchange="calculateMinPhraseCount()"><br>
            <p id="minPhraseCountMessage" style="color: red;"></p> 
 
            <label for="ramping_file">Upload Ramping File:</label>
            <input type="File" name="ramping_file"><br>
            <a href="sample_files/sample_ramp.csv" download>
                <img class="csv-icon" src="csv_icon.png" alt="CSV Icon"> 
                Download Sample Ramping CSV
            </a>
            <br><br>
        </div>

        <label for="file"><h4>Upload CSV Phrases File:</h4></label>
        <input type="File" name="file"><br>
        <a href="sample_files/sample_phrases.csv" download>
            <img class="csv-icon" src="csv_icon.png" alt="CSV Icon"> 
            Download Sample Phrases CSV
        </a>
        <br>

        <h4>Upload Unicode Exceptions File?</h4>
        <label><input id="uploadExceptionsYes" name="uploadExceptions" value="yes" type="radio" onclick="toggleExceptionsUpload()">Yes</label>
        <label><input id="uploadExceptionsNo" name="uploadExceptions" value="no" type="radio" onclick="toggleExceptionsUpload()">No</label>
        
        <div id="exceptionsContainer" style="display: none;">
            <label for="unicode_lookup">Upload Unicode Exception File:</label>
            <input type="File" name="unicode_lookup"><br>
            <a href="sample_files/sample_exceptions.csv" download>
                <img class="csv-icon" src="csv_icon.png" alt="CSV Icon"> 
                Download Sample Unicode Exceptions CSV
            </a>
            <br><br>
        </div>

        <h4>Type of Study</h4>
        <label><input name="studyType" value="within" type="radio">Within Subject Study</label>
        <label><input name="studyType" value="between" type="radio">Between Subject Study</label>
      
        
        <label><h4>Enter number of keyboards</h4></label>
        <input name="nokeyboards" type="text"><br>
        <label><h4>Enter names of the keyboards</h4></label>
        <textarea rows="5" cols="30" name="keyboards" placeholder="Enter name of keyboards separated by commas"></textarea><br><br>
        
        <button type="submit" name="submit">Submit</button>
        <p id="url"></p>
    </form>
</div>

<script>
function calculateMinPhraseCount() {
    var sessno = parseInt(document.getElementById('sessno').value);
    var phrasesPerSession = parseInt(document.getElementById('phrasesPerSession').value);
    var repetitionFactor = parseInt(document.getElementById('repetitionFactor').value);

    var minPhraseCount = Math.ceil(sessno * phrasesPerSession / repetitionFactor);
    document.getElementById('minPhraseCountMessage').textContent = "Minimum count of phrases: " + minPhraseCount;
}

function toggleRampingInputs() {
    var random = document.getElementById('random').checked;
    var rampingContainer = document.getElementById('rampingContainer');
    rampingContainer.style.display = random ? 'block' : 'none';
}

function toggleExceptionsUpload() {
    var uploadExceptionsYes = document.getElementById('uploadExceptionsYes').checked;
    var exceptionsContainer = document.getElementById('exceptionsContainer');
    exceptionsContainer.style.display = uploadExceptionsYes ? 'block' : 'none';
}

window.onload = function() {
    toggleRampingInputs(); // Initialize the ramping inputs visibility based on the default selected radio button
    toggleExceptionsUpload(); // Initialize the exceptions file upload visibility based on the default selected radio button
};
</script>
</body>
</html>

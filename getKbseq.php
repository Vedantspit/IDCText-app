<?php

require 'config/_dbconnect1.php';

$uid = '1';
$sid = '1';

if (isset($_POST['userid'])) {
    $uid = $_POST['userid'];
}

if (isset($_POST['studyid'])) {
    $sid = $_POST['studyid'];
}

mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Database connection failed: " . mysqli_error($conn));
}

// Fetch kbseq based on uid and sid where flag is 0
$kbseqQuery = "SELECT kbseq FROM nusers WHERE userid = '$uid' AND studyid = '$sid' AND flag = 0 ORDER BY kbseq ASC  LIMIT 1;";
$kbseqResult = mysqli_query($conn, $kbseqQuery);

if ($kbseqResult && mysqli_num_rows($kbseqResult) > 0) {
    $kbseqRow = mysqli_fetch_assoc($kbseqResult);
    $kbseq = $kbseqRow['kbseq'];

    $response = [
        'kbseq' => $kbseq
    ];

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Failed to fetch kbseq from the database']);
}

?>

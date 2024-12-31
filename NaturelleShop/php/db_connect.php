<?php 

$servername = "localhost";
//sciencec_sciencec_marrouki
$username = "sciencec_sciencec_marrouki";
//.MbwK*W7uJng8,i#
$password = ".MbwK*W7uJng8,i#";
//sciencec_stage24_marzouki
$dbname = "sciencec_stage24_marzouki";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}


<?php
// submitGOD.php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in-page.php");
    exit();
}

$username = $_SESSION['username'];



$host = 'localhost'; // or your host
$dbUsername = 'root'; // or your database username
$dbPassword = ''; // or your database password
$dbName = 'user_database'; // your database name

$conn = new mysqli($host, $dbUsername, '', $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the username is set in the session


date_default_timezone_set('America/Chicago');

$dateTime = date("Y-m-d H:i:s"); // Current date and time
$selection = $_POST['selection'];

$stmt = $conn->prepare("INSERT INTO god (username, date, selection) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $dateTime, $selection);
$stmt->execute();

echo "Selection submitted successfully.";

$stmt->close();
$conn->close();
?>

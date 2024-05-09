<?php
session_start();

// Set session cookie parameters for security
include 'config.php';



// Database credentials
$host = 'localhost';
$dbUsername = 'root';
$dbName = 'user_database';

// Create connection
$conn = new mysqli($host, $dbUsername, '', $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from sign-in page
    $username = $_POST['username']; // assuming the username field is named 'username'
    $password = $_POST['password']; // assuming the password field is named 'password'

    // Retrieve reCAPTCHA response from the form
    $recaptchaSecretKey = '6Lfd6mIpAAAAAP7rceHUvjxq8ZE8JD74kmlVFR0D'; // Replace with your actual secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify the reCAPTCHA response
    $verificationUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecretKey&response=$recaptchaResponse";
    $response = file_get_contents($verificationUrl);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        // CAPTCHA verification failed, likely a bot
        // Handle the error or display a message
        header('Location: sign-in-page.php?error=captchafailed');
        exit();
    }

    // Protect against SQL injection and use prepared statements
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            // Password is correct, set the session
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true; // Set a flag that the user is logged in
            $_SESSION['last_activity'] = time(); // Time of the user's login
            header("Location: props-page.php?page=props"); // Redirect to props-page.php
            exit();
        } else {
            // Password is not correct
            header("Location: sign-in-page.php?error=invalidcredentials");
            exit();
        }
    } else {
        // No user found with that username
        header("Location: sign-in-page.php?error=invalidcredentials");
        exit();
    }

    // Close the connection
    $stmt->close();
}

$conn->close();
?>


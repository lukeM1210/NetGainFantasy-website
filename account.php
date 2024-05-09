<?php



if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: sign-in-page.php');
    exit();
}


$username = $_SESSION['username'];

// Database credentials
$host = 'localhost';
$dbUsername = 'root';
$dbName = 'user_database';

$conn = new mysqli($host, $dbUsername, '', $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}








$sql = "SELECT balance, weekWinLoss FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $balance = $row['balance'];
    $weekWinLoss = $row['weekWinLoss']; // Retrieve the weekWinLoss value
} else {
    $balance = "0.00";
    $weekWinLoss = "0.00"; // Default value if no record is found
}


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="account-design.css">
    <link rel="icon" type="image/jpg" href="NetGainLogo-removedBackground.png">
    <title>NetGainFantasySports</title>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
            <a href="props-page.php?page=props">
                <img src="NetGainLogo-removedBackground.png" alt="NetGain Logo" width="50" height="50">
                <!--Green color code: #00BF63-->
              </a>
            </div>
            <div class="navbar-items">
                <a href="props-page.php?page=props">Home</a>
                <a href="props-page.php?page=specials">Specials</a>
                <a href="props-page.php?page=nfl">NFL</a>
                <a href="props-page.php?page=nba">NBA</a>
                <a href="props-page.php?page=nhl">NHL</a>
                <a href="props-page.php?page=mlb">MLB</a>
                <a href="props-page.php?page=cbb">CBB</a>
                <a href="props-page.php?page=cfb">CFB</a>
                <a href="props-page.php?page=soccer">Soccer</a>
            </div>
            <div class="user-info">
                <span class="balance">Balance: $<!--Balance--></span>
                <div class="user-menu"> 
                    <a href="props-page.php?page=account" >Account <i class="fa fa-user"></i></a> 
                </div>
            </div>
            
        </nav>
    </header>

    <main class="flex-container">
        <section class="welcome-section">
            <span class="titleName"><!--username-->'s Account Details</span>
            <div class="link-table">
              <div class="table-row">
                <a href="props-page.php?page=openBets" class="table-cell">Open Bets</a>
                <a href="props-page.php?page=closedBets" class="table-cell">Closed Bets</a>
              </div>
              <div class="table-row">
                <span class="table-cell">Balance: $<!--Balance--></span>
                <span class="table-cell">This Week's Win/Loss: $<!--weekWinLoss--></span>
              </div>
              
            </div>
            
            <div class="table-row-logout">
                <a href="logout.php" class="logout">Logout</a>
            </div>

        </section>

        <!-- Other content goes here -->
    </main>

    <footer>
        <p>&copy; 2023 NetGain Fantasy Sports. All rights reserved.</p>
    </footer>
</body>
</html>

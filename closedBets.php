<?php
if (!isset($_SESSION['username'])) {
    header("Location: sign-in-page.php"); // Redirect if not logged in
    exit();
}

$username = $_SESSION['username'];
$bets = []; // Array to hold user's bets

// Database connection
$host = 'localhost';
$dbUsername = 'root';
$dbName = 'user_database';
$conn = new mysqli($host, $dbUsername, '', $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query for open bets
$sql = "SELECT * FROM bets WHERE BetUsername = ? AND processed = 'True' ORDER BY CreatedAt DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bets[] = $row;
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
    <link rel="stylesheet" href="closed-bets-design.css">
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
            <span class="titleName"><!--username-->'s Closed Bets</span>
            <div class="bets-container">
              <?php foreach ($bets as $bet): ?>
              <div class="bet">
              <div class="bet-info">
                <p>Status: <span class="bet-detail"><?php echo htmlspecialchars($bet['bet_status']); ?></span></p>
                <p>Bet ID: <span class="bet-detail"><?php echo htmlspecialchars($bet['BetID']); ?></span></p>
                <p>Risk Amount: <span class="bet-detail">$<?php echo htmlspecialchars($bet['RiskAmount']); ?></span></p>
                <p>Potential Payout: <span class="bet-detail">$<?php echo htmlspecialchars($bet['PayoutAmount']); ?></span></p>
                <p>Players: <span class="bet-detail"><?php echo htmlspecialchars($bet['Players']); ?></span></p>
                <p>Teams: <span class="bet-detail"><?php echo htmlspecialchars($bet['SelectedTeam']); ?></span></p>
                <p>Over/Under: <span class="bet-detail"><?php echo htmlspecialchars($bet['OverUnder']); ?></span></p>
                <p>Stat: <span class="bet-detail"><?php echo htmlspecialchars($bet['Stat']); ?></span></p>
                <p>Created At: <span class="bet-detail"><?php echo htmlspecialchars($bet['CreatedAt']); ?></span></p>
            </div>
              </div>
              <?php endforeach; ?>
              <?php if (empty($bets)): ?>
              <p>No closed bets found.</p>
              <?php endif; ?>
          </div>
      </section>
            
              

        <!-- Other content goes here -->
    </main>

    <footer>
        <p>&copy; 2023 NetGain Fantasy Sports. All rights reserved.</p>
    </footer>
</body>
</html>
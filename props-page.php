<?php

session_start();

// Check if the user is not logged in or if the session has expired
if (!isset($_SESSION['username']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900))) {
    // If the user is not logged in, or the session is not marked as logged in,
    // or the last activity was more than 30 minutes ago, then logout the user.

    // Clear session data and destroy the session
    session_unset();
    session_destroy();

    // Redirect to the login page
    header("Location: sign-in-page.php");
    exit();
}

// Update the last activity time
$_SESSION['last_activity'] = time();


$username = $_SESSION['username'];

// Database credentials
$host = 'localhost';
$dbUsername = 'root';
$dbName = 'user_database';

$conn = new mysqli($host, $dbUsername, '', $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// testing for updating users balance

// update the users balance


// Query for open bets
$sql = "SELECT * FROM bets WHERE BetUsername = ? AND (bet_status = 'Win' OR bet_status = 'Loss') AND processed = 'False' ORDER BY CreatedAt DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$totalNetGain = 0; // Net gain from winning bets
$totalPayout = 0;   // Total payout from winning bets
$totalRiskLoss = 0; // Total risk amount from losing bets
$betsProcessed = [];

while ($row = $result->fetch_assoc()) {
  $bets[] = $row;
  $betsProcessed[] = $row['BetID'];

  if ($row['bet_status'] == 'Win') {
      // Calculate net gain for each winning bet
      $netGainForBet = $row['PayoutAmount'] - $row['RiskAmount'];
      $totalNetGain += $netGainForBet;
      $totalPayout += $row['PayoutAmount']; // Add the payout to the total
  } elseif ($row['bet_status'] == 'Loss') {
      $totalRiskLoss += $row['RiskAmount']; // Add the risk amount to the total
  }
}

$stmt->close();

// Now, update the user's balance and weekWinLoss in the users table
if (!empty($betsProcessed)) {
  // Calculate the adjustment to weekWinLoss (include losses)
  $weekWinLossAdjustment = $totalNetGain - $totalRiskLoss;

  // Update the user's balance for wins and weekWinLoss
  $updateUserQuery = "UPDATE users SET balance = balance + ?, weekWinLoss = weekWinLoss + ? WHERE username = ?";
  $updateUserStmt = $conn->prepare($updateUserQuery);
  $updateUserStmt->bind_param("dds", $totalPayout, $weekWinLossAdjustment, $username);
  $updateUserStmt->execute();
  $updateUserStmt->close();

  foreach ($betsProcessed as $betID) {
      // Update each bet as processed
      $updateBetQuery = "UPDATE bets SET processed = 'True' WHERE BetID = ?";
      $updateBetStmt = $conn->prepare($updateBetQuery);
      $updateBetStmt->bind_param("i", $betID);
      $updateBetStmt->execute();
      $updateBetStmt->close();
  }
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


$page = $_GET['page'] ?? 'default';

switch ($page) {

    case 'nba':
      ob_start();
      include('nba.html');
      $htmlContent = ob_get_clean();
      // Replace balance placeholder
      $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
      break;

    case 'props':
        ob_start();
        include('props-page.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);

        // update users balance/weekWinLoss



        break;

    case 'nfl':
        ob_start();
        include('nfl.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'nhl':
        ob_start();
        include('nhl.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'soccer':
        ob_start();
        include('soccer.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'cbb':
        ob_start();
        include('cbb.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'cfb':
        ob_start();
        include('cfb.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'mlb':
        ob_start();
        include('mlb.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'specials':
        ob_start();
        include('specials.html');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);
        break;

    case 'openBets':
        ob_start();
        include('openBets.php');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);

        // Replace username placeholder
        $htmlContent = str_replace('<!--username-->', htmlspecialchars($username), $htmlContent);
        break;

    case 'closedBets':
        ob_start();
        include('closedBets.php');
        $htmlContent = ob_get_clean();
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);

        // Replace username placeholder
        $htmlContent = str_replace('<!--username-->', htmlspecialchars($username), $htmlContent);
        break;

    case 'account':
        ob_start();
        include('account.php');
        $htmlContent = ob_get_clean();
        
        // Replace balance placeholder
        $htmlContent = str_replace('<!--Balance-->', htmlspecialchars($balance), $htmlContent);

        // Replace username placeholder
        $htmlContent = str_replace('<!--username-->', htmlspecialchars($username), $htmlContent);

        // Replace week win/loss placeholder
        $htmlContent = str_replace('<!--weekWinLoss-->', htmlspecialchars($weekWinLoss), $htmlContent);
        break;
        
        
        
        
    // Add more cases as needed
    default:
        ob_start();
        include('sign-in-page.html');
        $htmlContent = ob_get_clean();
        break;
}

echo $htmlContent;
?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign-in-page.php");
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

// Function to calculate payout
function calculatePayout($riskAmount, $numberOfPicks) {
  // Define the multiplier array
  $multiplier = [0, 0, 3, 5, 10, 15, 25, 40];

  // Ensure the number of picks is within the bounds of the multiplier array
  if ($numberOfPicks < 0 || $numberOfPicks >= count($multiplier)) {
      return 0; // Or handle this scenario as appropriate
  }

  // Calculate the payout
  $payout = $riskAmount * $multiplier[$numberOfPicks];
  return $payout;
}

$moneylineSpreadTeams = [];
$selectedTeams = [];



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Access the variables directly from the $_POST array
      $riskAmount = isset($_POST['riskAmount']) ? $_POST['riskAmount'] : null;
      $picks = isset($_POST['picks']) ? json_decode($_POST['picks'], true) : null;

    // Validate risk amount
    if (!is_numeric($riskAmount) || $riskAmount < 5 || $riskAmount > 200) {
        echo "Invalid risk amount.";
        exit;
    }

    // Validate picks
    //$decodedPicks = json_decode($picks, true);
    if (count($picks) < 2) {
        echo "At least two picks are required.";
        exit;
    }

     // Validate the number of picks
     if (count($picks) > 7) {
      echo "Error: A maximum of 7 props can be selected for a parlay.";
      exit;
  }

    // Ensure each pick has an over/under selection
    foreach ($picks as $pick) {
      if (isset($pick['type']) && (($pick['type'] === 'moneyline') || ($pick['type'] === 'straight'))){
          $teamString = implode(", ", $pick['teams']);
          $moneylineSpreadTeams[] = $teamString;
          $selectedTeams[] = $pick['teamChoice'];
      } 
      
      else {
          // Only check for over/under selection if it's not a moneyline bet
          if (!isset($pick['overUnder'])) {
              echo "Each pick must have an over/under selection.";
              exit;
          }
      }
  }
  

    // Update users balance
    // Subtract risk amount from current balance
    
    // Retrieve current balance
    $balanceQuery = "SELECT balance FROM users WHERE username = ?";
    $balanceStmt = $conn->prepare($balanceQuery);
    $balanceStmt->bind_param("s", $username);
    $balanceStmt->execute();
    $balanceResult = $balanceStmt->get_result();
    if ($balanceRow = $balanceResult->fetch_assoc()) {
      $currentBalance = $balanceRow['balance'];

    // Check if sufficient balance
    if ($currentBalance < $riskAmount) {
        echo "Insufficient balance.";
        exit;
    }

    // Subtract bet amount from current balance
    $newBalance = $currentBalance - $riskAmount;

    // Update balance in database
    $updateBalanceQuery = "UPDATE users SET balance = ? WHERE username = ?";
    $updateBalanceStmt = $conn->prepare($updateBalanceQuery);
    $updateBalanceStmt->bind_param("ds", $newBalance, $username);
    $updateBalanceStmt->execute();
    $updateBalanceStmt->close();
    } else {
    echo "User not found.";
    exit;
    }

    // Insert into database (assuming you have a PDO instance $pdo)
    date_default_timezone_set('America/Chicago');
    $createdAt = date('Y-m-d H:i:s'); // Current date and time
    $potentialPayout = calculatePayout($riskAmount, count($picks));
    

    // Set parameters and execute

    // for moneyline/spread bets
    $moneylineSpreadTeamsString = implode("; ", $moneylineSpreadTeams); // Combine all Moneyline teams
    $selectedTeamsString = implode("; ", $selectedTeams); // Combine all selected teams

    

    $processed = "False";
    $betStatus = "Open";
    $players = implode(", ", array_column($picks, 'name'));
    $overUnder = implode(", ", array_column($picks, 'overUnder'));
    $stat = array_map(function($pick){
      return $pick['prop'];
    }, $picks);// define how you want to store this based on $decodedPicks
    $stat = implode(", ", $stat); // combines all 'points' data into single string

    

    //$stmt = $conn->prepare("INSERT INTO bets (BetUsername, RiskAmount, PayoutAmount, Players, OverUnder, Stat, CreatedAt, bet_status, processed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    //$stmt->bind_param("sddssssss", $username, $riskAmount, $potentialPayout, $players, $overUnder, $stat, $createdAt, $betStatus, $processed);
    //$stmt->execute();


    $stmt = $conn->prepare("INSERT INTO bets (BetUsername, RiskAmount, PayoutAmount, Players, OverUnder, Stat, CreatedAt, bet_status, processed, MoneylineSpreadTeams, SelectedTeam) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    $stmt->bind_param("sddssssssss", $username, $riskAmount, $potentialPayout, $players, $overUnder, $stat, $createdAt, $betStatus, $processed, $moneylineSpreadTeamsString, $selectedTeamsString);

    $stmt->execute();



    
    echo "Bet Placed Successfully";

    $stmt->close();
    $conn->close();
    $balanceStmt->close();
  }else{
    echo "Something Went Wrong.";
  }

?>

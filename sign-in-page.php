<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="sign-in-page-design.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
  <link rel="icon" type="image/jpg" href="NetGainLogo-removedBackground.png">
  <title>NetGainFantasySports</title>
</head>
<body>
  <div class="container">
    <div class="logo-sign-in-page">
    <img src="NetGainFantasySportsLogo-removedBackground.png" alt="NetGain Logo" width="400" height="380">
    <!--Green color code: #00BF63-->
    </div>
<section class="login-section">

<!-- Check for error message -->
  <?php
  if (isset($_GET['error']) && $_GET['error'] == 'invalidcredentials') {
      echo '<p class="error-message">Incorrect username/password</p>';
  }
  ?>

  <form id="loginForm" action="connect.php" method="post" >
    <input type="text" id="username" name="username" placeholder="Username" required>
    <input type="password" id="password" name="password" placeholder="Password" required>
    <div class="g-recaptcha" data-sitekey="6Lfd6mIpAAAAAFZos3iRhdQ4wvbEuhmixzvxKVga"></div>
    <input type="submit" value="Log in" id="submit-input">
  </form>

</section>
</div>
  <!-- Place any script tags here -->
  
</body>
</html>
<?php
// login.php

// Kontrola, jestli session už neběží
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Připojení k DB
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Nepovedlo se připojit: " . $conn->connect_error);
}

// Zpracování formuláře
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  $sql = "SELECT * FROM users WHERE UserEmail='$email' AND Password='$pass' LIMIT 1";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['idUsers'];
    $_SESSION['user_name'] = $user['UserName'];
    header("Location: login.php");
    exit();
  } else {
    echo "Špatný údaje, bro.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login Page</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="style.css" />
  <link href="https://api.fontshare.com/v2/css?f[]=switzer@100,101,200,201,300,301,400,401,500,501,600,601,700,701,800,801,900,901,1,2&display=swap" rel="stylesheet"/>
</head>
<body>

  <?php include 'header.php'; ?>

  <div class="login-container">
    <div class="login-content">
      <div class="login-left">
        <h1 class="logo-login">Travel Blog<span>*</span></h1>
        <h2 class="subheader">Login</h2>
      </div>
      <div class="login-right">
        <?php if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
          <form class="login-form" action="login.php" method="POST">
            <div class="input-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="input-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" required />
            </div>
            <button type="submit" class="btn">Login</button>
          </form>
        <?php else: ?>
          <p>Už jsi přihlášen, bro!</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>

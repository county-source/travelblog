<?php
// register.php

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

// Odeslaný registrační formulář
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstName = $_POST['name'];
  $secondName = $_POST['second_name'];
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $confirmPass = $_POST['confirm_password'];

  if ($pass !== $confirmPass) {
    echo "Hesla se neshodují, bro.";
  } else {
    // fullName spojí jméno a příjmení (protože v DB je jen UserName)
    $fullName = $firstName . " " . $secondName;

    // Ověření, zda není email už v DB
    $checkSql = "SELECT * FROM users WHERE UserEmail='$email' LIMIT 1";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
      echo "Uživatel s tímto e-mailem už existuje, bro.";
    } else {
      $sql = "INSERT INTO users (UserName, UserEmail, Password, Role)
              VALUES ('$fullName', '$email', '$pass', 'delegate')";
      if ($conn->query($sql) === TRUE) {
        echo "Registrován! Můžeš se přihlásit, bro.";
      } else {
        echo "Chyba při registraci: " . $conn->error;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register Page</title>¨
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://api.fontshare.com/v2/css?f[]=switzer@100,101,200,201,300,301,400,401,500,501,600,601,700,701,800,801,900,901,1,2&display=swap"
    rel="stylesheet"
  />
</head>
<body>

  <?php include 'header.php'; ?>

  <div class="signup-container">
    <div class="signup-content">
      <div class="signup-left">
        <h1 class="logo-register">Travel Blog<span>*</span></h1>
        <h2 class="subheader">Sign up</h2>
      </div>
      <div class="signup-right">
        <form class="signup-form" action="register.php" method="POST">
          <div class="input-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required />
          </div>
          <div class="input-group">
            <label for="second-name">Second Name</label>
            <input type="text" id="second-name" name="second_name" required />
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div class="input-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" required />
          </div>
          <button type="submit" class="btn">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

<?php
// adminpanel.php

// Spustíme session, pokud ještě neběží
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Když není user přihlášen, zablokujem ho
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("Sem nemáš přístup, bro!");
}

// Načteme sdílený header s navbarem
include 'header.php';

// Připojení k DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Nepovedlo se připojit: " . $conn->connect_error);
}

// Pro dropdowny si vytáhneme uživatele (author) a destinace
$usersSql = "SELECT idUsers, UserName FROM users";
$usersRes = $conn->query($usersSql);

$destSql = "SELECT idDestination, Name FROM destination";
$destRes = $conn->query($destSql);

// Zpracování formuláře
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = $_POST['title']       ?? '';
    $content     = $_POST['content']     ?? '';
    $profileImg  = $_POST['profileImg']  ?? ''; // Případně handling uploadu
    $image       = $_POST['image']       ?? ''; // Případně handling uploadu
    $author      = $_POST['author']      ?? '';
    $destination = $_POST['destination'] ?? '';
    $datePublic  = $_POST['datePublic']  ?? '';

    // Jednoduchý INSERT do articles
    // Bez ošetření, v praxi byste použili prepared statements
    $sqlInsert = "INSERT INTO articles 
                    (Title, Content, ProfileImg, Image, Author, Destination, DatePublic) 
                  VALUES 
                    ('$title', '$content', '$profileImg', '$image', '$author', '$destination', '$datePublic')";

    if ($conn->query($sqlInsert) === TRUE) {
        echo "<p>Article byl přidán, bro!</p>";
    } else {
        echo "<p>Chyba při přidání: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="admin-panel-container">
    <h1>Admin Panel</h1>

    <h2>Přidat nový článek</h2>
    <form action="adminpanel.php" method="POST">
      <div>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required>
      </div>

      <div>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="5" cols="50" required></textarea>
      </div>

      <div>
        <label for="profileImg">ProfileImg:</label><br>
        <input type="text" id="profileImg" name="profileImg" placeholder="např. paris.jpg">
      </div>

      <div>
        <label for="image">Image:</label><br>
        <input type="text" id="image" name="image" placeholder="např. bigphoto.jpg">
      </div>

      <div>
        <label for="author">Author:</label><br>
        <select id="author" name="author" required>
          <option value="">-- Vyber autora --</option>
          <?php if($usersRes && $usersRes->num_rows > 0): ?>
            <?php while($u = $usersRes->fetch_assoc()): ?>
              <option value="<?php echo $u['idUsers']; ?>">
                <?php echo $u['UserName']; ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
      </div>

      <div>
        <label for="destination">Destination:</label><br>
        <select id="destination" name="destination" required>
          <option value="">-- Vyber destinaci --</option>
          <?php if($destRes && $destRes->num_rows > 0): ?>
            <?php while($d = $destRes->fetch_assoc()): ?>
              <option value="<?php echo $d['idDestination']; ?>">
                <?php echo $d['Name']; ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
      </div>

      <div>
        <label for="datePublic">DatePublic (YYYY-MM-DD):</label><br>
        <input type="date" id="datePublic" name="datePublic" required>
      </div>

      <br>
      <button type="submit">Přidat článek</button>
    </form>
  </div>

</body>
</html>
<?php
$conn->close();

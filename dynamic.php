<?php
// dynamic.php

// Spustíme session, pokud ještě neběží
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Získáme id z GET parametrů
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No blog ID provided, bro.");
}
$blogId = intval($_GET['id']);

// Připojení k DB
$conn = new mysqli("localhost", "root", "", "travelblog");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dotaz pro načtení jednoho blogu s JOINem na destination a uživatele (autor)
$sql = "SELECT a.*, d.Name AS DestinationName, u.UserName AS AuthorName 
        FROM articles a 
        JOIN destination d ON a.Destination = d.idDestination 
        JOIN users u ON a.Author = u.idUsers 
        WHERE a.idArticles = $blogId 
        LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $blog = $result->fetch_assoc();
} else {
    die("Blog not found, bro.");
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($blog['Title']); ?> - Travel Blog</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://api.fontshare.com/v2/css?f[]=switzer@400,600&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body>
  <!-- Sdílený header s navbarem -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <div class="hero-image">
    <?php if (!empty($blog['Image'])): ?>
      <img src="<?php echo htmlspecialchars($blog['Image']); ?>" alt="Blog Image">
    <?php else: ?>
      <img src="./images/header.JPG" alt="Default Hero Image">
    <?php endif; ?>
    <div class="hero-content">
      <h1><?php echo htmlspecialchars($blog['DestinationName']); ?></h1>
    </div>
  </div>

  <!-- Blog Detail Section -->
  <section class="section">
    <article class="blog-detail">
      <h2><?php echo htmlspecialchars($blog['Title']); ?></h2>
      <p><strong>Author:</strong> <?php echo htmlspecialchars($blog['AuthorName']); ?></p>
      <p><strong>Date:</strong> <?php echo htmlspecialchars($blog['DatePublic']); ?></p>
      <div class="blog-content-full">
        <?php echo $blog['Content']; // Předpokládáme, že obsah může obsahovat HTML ?>
      </div>
    </article>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-logo">
        <h2>Travel Blog<span>*</span></h2>
        <p>A place where nature and adventure unite</p>
      </div>
      <div class="footer-links">
        <div class="footer-column">
          <h3>About us</h3>
          <ul>
            <li><a href="#">Our guides</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Contact us</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>FAQ</h3>
          <ul>
            <li><a href="#">Personal trip</a></li>
            <li><a href="#">Group trip</a></li>
            <li><a href="#">Tour payment</a></li>
          </ul>
        </div>
      </div>
    </div>
    <hr>
    <div class="footer-bottom">
      <p>Copyright 2025 Travel Blog - Plzeň, Czech Republic</p>
      <div class="footer-icons">
        <a href="#"><img src="./icons/facebook-f-brands-solid.svg" alt=""></a>
        <a href="#"><img src="./icons/instagram-brands-solid.svg" alt=""></a>
        <a href="#"><img src="./icons/youtube-brands-solid.svg" alt=""></a>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const mobileMenu = document.getElementById("mobile-menu");
    const navLinks = document.querySelector(".nav-links");
    mobileMenu.addEventListener("click", () => {
      navLinks.classList.toggle("active");
    });
  </script>
</body>
</html>

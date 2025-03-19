<?php
// bloglist.php

// Spustíme session, pokud ještě neběží
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Načteme sdílený header s navbarem (a tamtéž se kontroluje session)
include 'header.php';

// Připojení k DB
$servername = "localhost";
$username   = "root"; 
$password   = "";
$dbname     = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Nepovedlo se připojit: " . $conn->connect_error);
}

// Převezmeme filtry z GET
$search    = isset($_GET['search']) ? $_GET['search'] : '';
$continent = isset($_GET['continent']) ? $_GET['continent'] : 'none';
$dateOrder = isset($_GET['dateorder']) ? $_GET['dateorder'] : 'none';

// Mapujeme destinace na kontinenty (příklad)
$continentMap = [
    'africa'        => ['???'],
    'asia'          => ['Tokyo'],
    'europe'        => ['Paris','Plzen'],
    'north-america' => ['New York'],
    'south-america' => ['???'],
    'australia'     => ['???'],
    'antarctica'    => ['???']
];

// Vytvoříme pole pro WHERE podmínky
$whereClauses = [];

// 1) Vyhledávání
if (!empty($search) && $search !== 'none') {
    $whereClauses[] = "(a.Title LIKE '%$search%' OR a.Content LIKE '%$search%')";
}

// 2) Filtr kontinentů
if ($continent !== 'none' && isset($continentMap[$continent])) {
    $allowedDestinations = $continentMap[$continent];
    if (!empty($allowedDestinations)) {
        $destList = "'" . implode("','", $allowedDestinations) . "'";
        $whereClauses[] = "d.Name IN ($destList)";
    } else {
        $whereClauses[] = "1=0"; // nic se nenajde
    }
}

// 3) Seřazení podle data
$orderBy = "ORDER BY a.idArticles DESC"; 
if ($dateOrder === 'newest') {
    $orderBy = "ORDER BY a.DatePublic DESC";
} elseif ($dateOrder === 'oldest') {
    $orderBy = "ORDER BY a.DatePublic ASC";
}

// Složíme SELECT (přidáme a.Image jako Image, abychom ho měli v $row['Image'])
$sql = "SELECT a.*, a.Image AS Image, u.UserName AS AuthorName, d.Name AS DestinationName
        FROM articles a
        JOIN users u ON a.Author = u.idUsers
        JOIN destination d ON a.Destination = d.idDestination";

// Když máme WHERE, přidáme ho
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Nakonec ORDER
$sql .= " $orderBy";

// Dotaz
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="favicon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Travel Blog</title>
  <link
    href="https://api.fontshare.com/v2/css?f[]=switzer@400,500,600,700&display=swap"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <!-- Filtr + vyhledávací formulář -->
  <div class="search-section">
    <form class="search-form" action="bloglist.php" method="GET">
      <input
        type="text"
        name="search"
        class="search-input"
        placeholder="Search for blogs..."
        value="<?php echo htmlspecialchars($search); ?>"
      />
      <select name="continent" class="filter-select">
        <option value="none" <?php if($continent==='none') echo 'selected'; ?>>No Continent Filter</option>
        <option value="africa" <?php if($continent==='africa') echo 'selected'; ?>>Africa</option>
        <option value="asia" <?php if($continent==='asia') echo 'selected'; ?>>Asia</option>
        <option value="europe" <?php if($continent==='europe') echo 'selected'; ?>>Europe</option>
        <option value="north-america" <?php if($continent==='north-america') echo 'selected'; ?>>North America</option>
        <option value="south-america" <?php if($continent==='south-america') echo 'selected'; ?>>South America</option>
        <option value="australia" <?php if($continent==='australia') echo 'selected'; ?>>Australia</option>
        <option value="antarctica" <?php if($continent==='antarctica') echo 'selected'; ?>>Antarctica</option>
      </select>
      <select name="dateorder" class="filter-select">
        <option value="none" <?php if($dateOrder==='none') echo 'selected'; ?>>No Date Filter</option>
        <option value="newest" <?php if($dateOrder==='newest') echo 'selected'; ?>>Newest</option>
        <option value="oldest" <?php if($dateOrder==='oldest') echo 'selected'; ?>>Oldest</option>
      </select>
      <button type="submit" class="search-button">
        <img src="./icons/magnifying-glass-solid.svg" alt="Search Icon" />
      </button>
    </form>
  </div>

  <!-- Blog Section -->
  <div class="blogs-container">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="blog-item">
          <div class="blog-image">
            <!-- Tady zobrazíme obrázek -->
            <img src="./images/<?php echo htmlspecialchars($row['Image']); ?>" alt="Blog Image" />
          </div>
          <div class="blog-content">
            <h2 class="blog-header"><?php echo htmlspecialchars($row['Title']); ?></h2>
            <p class="blog-subheader">
              Autor: <?php echo htmlspecialchars($row['AuthorName']); ?> |
              Destinace: <?php echo htmlspecialchars($row['DestinationName']); ?> |
              Datum: <?php echo htmlspecialchars($row['DatePublic']); ?>
            </p>
          </div>
          <div class="blog-arrow">
            <img src="./icons/arrow-right-solid.svg" alt="Arrow Icon" />
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">Žádné blogy k zobrazení, bro!</p>
    <?php endif; ?>
  </div>

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
    <hr />
    <div class="footer-bottom">
      <p>Copyright 2025 Travel Blog - Plzeň, Czech Republic</p>
      <div class="footer-icons">
        <a href="#"><img src="./icons/facebook-f-brands-solid.svg" alt=""/></a>
        <a href="#"><img src="./icons/instagram-brands-solid.svg" alt=""/></a>
        <a href="#"><img src="./icons/youtube-brands-solid.svg" alt=""/></a>
      </div>
    </div>
  </footer>

  <script>
    const mobileMenu = document.getElementById("mobile-menu");
    const navLinks = document.querySelector(".nav-links");
    mobileMenu.addEventListener("click", () => {
      navLinks.classList.toggle("active");
    });
  </script>
</body>
</html>
<?php
// Uzavřeme spojení s DB
$conn->close();

<?php
// bloglist.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the shared header
include 'header.php';

// Connect to the database
$servername = "localhost";
$username   = "root"; 
$password   = "";
$dbname     = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve filter values from GET parameters
$search    = isset($_GET['search']) ? $_GET['search'] : '';
$continent = isset($_GET['continent']) ? $_GET['continent'] : 'none';
$dateOrder = isset($_GET['dateorder']) ? $_GET['dateorder'] : 'none';

// Map continents to destination names (adjust values to match your database)
$continentMap = [
    'africa'        => ['South Africa'],
    'asia'          => ['Japan','China'],
    'europe'        => ['Italy'],
    'north-america' => ['New York'],
    'south-america' => ['Peru'],
    'australia'     => ['Australia'],
    'antarctica'    => ['???']
];

// Build an array for WHERE clauses
$whereClauses = [];

// 1) Search Filter
if (!empty($search) && $search !== 'none') {
    $searchEsc = $conn->real_escape_string($search);
    $whereClauses[] = "(a.Title LIKE '%$searchEsc%' OR a.Content LIKE '%$searchEsc%')";
}

// 2) Continent Filter
if ($continent !== 'none' && isset($continentMap[$continent])) {
    $allowedDestinations = $continentMap[$continent];
    if (!empty($allowedDestinations)) {
        // Escape each destination value
        $escapedDestinations = array_map(function($dest) use ($conn) {
            return $conn->real_escape_string($dest);
        }, $allowedDestinations);
        $destList = "'" . implode("','", $escapedDestinations) . "'";
        $whereClauses[] = "d.Name IN ($destList)";
    } else {
        $whereClauses[] = "1=0"; // no results if no allowed destinations
    }
}

// 3) Date Ordering
$orderBy = "ORDER BY a.idArticles DESC"; // default order
if ($dateOrder === 'newest') {
    $orderBy = "ORDER BY a.DatePublic DESC";
} elseif ($dateOrder === 'oldest') {
    $orderBy = "ORDER BY a.DatePublic ASC";
}

// Build the final SQL query with JOINs
$sql = "SELECT a.*, u.UserName AS AuthorName, d.Name AS DestinationName
        FROM articles a
        JOIN users u ON a.Author = u.idUsers
        JOIN destination d ON a.Destination = d.idDestination";

// If any WHERE conditions exist, add them
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Append the ordering clause
$sql .= " $orderBy";

// Debug: Uncomment the lines below to check the query and filter values
// echo "<pre>$sql</pre>";
// echo "Search: $search, Continent: $continent, DateOrder: $dateOrder";
// exit();

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Travel Blog</title>
  <link href="https://api.fontshare.com/v2/css?f[]=switzer@400,500,600,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body>
  <!-- Filter and search form -->
  <div class="search-section">
    <form class="search-form" action="bloglist.php" method="GET">
      <input type="text" name="search" class="search-input" placeholder="Search for blogs..." value="<?php echo htmlspecialchars($search); ?>" />
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
        <img src="icons/magnifying-glass-solid.svg" alt="Search Icon" />
      </button>
    </form>
  </div>

  <!-- Blog Section -->
  <div class="blogs-container">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="blog-item">
          <div class="blog-image">
            <?php if (!empty($row['Image'])): ?>
                <img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="Blog Image" />
            <?php else: ?>
                <img src="images/default.jpg" alt="Default Image" />
            <?php endif; ?>
          </div>
          <div class="blog-content">
            <!-- Display only the destination (as header) and title (as subheader) -->
            <h2 class="blog-header"><?php echo htmlspecialchars($row['DestinationName']); ?></h2>
            <h3 class="blog-subheader"><?php echo htmlspecialchars($row['Title']); ?></h3>
          </div>
          <div class="blog-arrow">
            <a href="dynamic.php?id=<?php echo $row['idArticles']; ?>">
              <img src="icons/arrow-right-solid.svg" alt="Arrow Icon" />
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">No blogs found, bro!</p>
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
        <a href="#"><img src="icons/facebook-f-brands-solid.svg" alt=""/></a>
        <a href="#"><img src="icons/instagram-brands-solid.svg" alt=""/></a>
        <a href="#"><img src="icons/youtube-brands-solid.svg" alt=""/></a>
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
$conn->close();

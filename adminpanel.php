<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("You don't have access here, bro!");
}

// Check if user_id exists
if (!isset($_SESSION['user_id'])) {
    die("User ID is not set, bro.");
}

$currentUserId = $_SESSION['user_id'];
// Determine if the user is admin (role stored in lowercase)
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Debug: Uncomment the following lines to see session details
// echo "<pre>"; var_dump($_SESSION); echo "</pre>";
// echo "isAdmin: " . ($isAdmin ? "true" : "false") . "<br>";
// echo "CurrentUserId: " . $currentUserId . "<br>";

// Connect to DB
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
}

// Variables for the form
$title       = "";
$content     = "";
$destination = "";
$articleId   = 0; // 0 means new article

// If editing (GET parameter), load the article only if the article belongs to the user (unless admin)
if (isset($_GET['edit'])) {
    $articleId = (int)$_GET['edit'];
    $sqlEdit = "SELECT a.*, d.Name AS DestName
                FROM articles a
                JOIN destination d ON a.Destination = d.idDestination
                WHERE a.idArticles = $articleId";
    if (!$isAdmin) {
        $sqlEdit .= " AND a.Author = $currentUserId";
    }
    $sqlEdit .= " LIMIT 1";
    $resEdit = $conn->query($sqlEdit);
    if ($resEdit && $resEdit->num_rows > 0) {
        $rowEdit = $resEdit->fetch_assoc();
        $title       = $rowEdit['Title'];
        $content     = $rowEdit['Content'];
        $destination = $rowEdit['DestName'];
    } else {
        die("You cannot edit this article, bro!");
    }
}

// Handle form submission (add/update)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isUpdate = isset($_POST['articleId']) && $_POST['articleId'] > 0;
    $articleIdPost = (int)($_POST['articleId'] ?? 0);
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    // Handle image upload if provided
    $image = "";
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === 0) {
        $allowed = [
            "jpg"  => "image/jpg",
            "jpeg" => "image/jpeg",
            "png"  => "image/png",
            "gif"  => "image/gif"
        ];
        $filename = $_FILES['image_upload']['name'];
        $filetype = $_FILES['image_upload']['type'];
        $filesize = $_FILES['image_upload']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            die("Error: Please select a valid file format.");
        }
        $maxsize = PHP_INT_MAX;
        if ($filesize > $maxsize) {
            die("Error: File is too large.");
        }
        if (in_array($filetype, $allowed)) {
            $new_filename = uniqid() . "." . $ext;
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $upload_dir . $new_filename)) {
                $image = $upload_dir . $new_filename;
            } else {
                die("Error: Problem uploading the file, please try again.");
            }
        } else {
            die("Error: Problem with your file, please try again.");
        }
    }
    
    // For new articles, set author from session
    $author = $currentUserId;
    
    // Get destination from form and check/insert
    $destinationName = $_POST['destination'] ?? '';
    $datePublic = date('Y-m-d'); // auto date
    $destinationId = 0;
    $destQuery = "SELECT idDestination FROM destination
                  WHERE Name='" . $conn->real_escape_string($destinationName) . "'
                  LIMIT 1";
    $destResult = $conn->query($destQuery);
    if ($destResult && $destResult->num_rows > 0) {
        $destRow = $destResult->fetch_assoc();
        $destinationId = $destRow['idDestination'];
    } else {
        $insertDest = "INSERT INTO destination (Name) VALUES ('" . $conn->real_escape_string($destinationName) . "')";
        if ($conn->query($insertDest) === TRUE) {
            $destinationId = $conn->insert_id;
        } else {
            die("Error inserting destination: " . $conn->error);
        }
    }
    
    if ($isUpdate) {
        // If not admin, ensure the article belongs to the user
        if (!$isAdmin) {
            $checkSql = "SELECT idArticles FROM articles
                         WHERE idArticles = $articleIdPost AND Author = $currentUserId
                         LIMIT 1";
            $checkRes = $conn->query($checkSql);
            if (!$checkRes || $checkRes->num_rows === 0) {
                die("You cannot edit this article, bro!");
            }
        }
        // If no new image uploaded, keep old image
        $sqlOld = "SELECT Image FROM articles WHERE idArticles = $articleIdPost LIMIT 1";
        $resOld = $conn->query($sqlOld);
        $oldImage = "";
        if ($resOld && $resOld->num_rows > 0) {
            $oldRow = $resOld->fetch_assoc();
            $oldImage = $oldRow['Image'];
        }
        if ($image === "") {
            $image = $oldImage;
        }
        $sqlUpdate = "UPDATE articles
                      SET Title='" . $conn->real_escape_string($title) . "',
                          Content='" . $conn->real_escape_string($content) . "',
                          Image='" . $conn->real_escape_string($image) . "',
                          Destination='" . $conn->real_escape_string($destinationId) . "'
                      WHERE idArticles = $articleIdPost LIMIT 1";
        if ($conn->query($sqlUpdate) === TRUE) {
            echo "<p style='text-align:center;'>Article has been updated, bro!</p>";
        } else {
            echo "<p style='text-align:center;'>Error while updating: " . $conn->error . "</p>";
        }
    } else {
        $sqlInsert = "INSERT INTO articles (Title, Content, Image, Author, Destination, DatePublic)
                      VALUES ('" . $conn->real_escape_string($title) . "',
                              '" . $conn->real_escape_string($content) . "',
                              '" . $conn->real_escape_string($image) . "',
                              '" . $conn->real_escape_string($author) . "',
                              '" . $conn->real_escape_string($destinationId) . "',
                              '$datePublic')";
        if ($conn->query($sqlInsert) === TRUE) {
            echo "<p style='text-align:center;'>Article has been added, bro!</p>";
        } else {
            echo "<p style='text-align:center;'>Error while adding: " . $conn->error . "</p>";
        }
    }
    // Reset form variables
    $articleId   = 0;
    $title       = "";
    $content     = "";
    $destination = "";
}

// Retrieve articles for listing
// If admin, show all articles; otherwise, only show articles belonging to the current user
$sqlAll = "SELECT a.idArticles, a.Title, a.DatePublic, d.Name as DestName, a.Image
           FROM articles a
           JOIN destination d ON a.Destination = d.idDestination";
if (!$isAdmin) {
    $sqlAll .= " WHERE a.Author = $currentUserId";
}
$sqlAll .= " ORDER BY a.idArticles DESC";
$resAll = $conn->query($sqlAll);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Manage Articles</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="images/favicon.png">
  <link href="https://api.fontshare.com/v2/css?f[]=switzer@400,600&display=swap" rel="stylesheet">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="admin-container">
    <h1 class="admin-title">Article</h1>

    <!-- One form split into two columns -->
    <form class="admin-content" action="adminpanel.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="articleId" value="<?php echo $articleId; ?>">

      <!-- Left Column -->
      <div class="admin-left">
        <div class="input-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>" />
        </div>
        <div class="input-group">
          <label for="destination">Destination</label>
          <input type="text" id="destination" name="destination" placeholder="Enter destination" required value="<?php echo htmlspecialchars($destination); ?>" />
        </div>
        <div class="input-group">
          <label for="image_upload">Upload Image</label>
          <input type="file" id="image_upload" name="image_upload" accept="image/*" />
          <img id="imagePreview" src="#" alt="Image Preview" style="display: none;" />
        </div>
      </div>

      <!-- Right Column -->
      <div class="admin-right">
        <div class="input-group">
          <label for="content">Content</label>
          <textarea id="content" name="content" rows="10" cols="50" required><?php echo htmlspecialchars($content); ?></textarea>
        </div>
        <button type="submit" class="btn" style="margin-top: 15px;">
          <?php echo $articleId > 0 ? 'Update Article' : 'Add Article'; ?>
        </button>
      </div>
    </form>

    <!-- Article list (cards) -->
    <h2 style="text-align:center; margin-top:40px;">Article List</h2>
    <div class="cards-container">
      <?php if ($resAll && $resAll->num_rows > 0): ?>
        <?php while($row = $resAll->fetch_assoc()): ?>
          <div class="card">
            <img class="card-image" src="<?php echo !empty($row['Image']) ? htmlspecialchars($row['Image']) : './images/default.jpg'; ?>" alt="Blog Image">
            <div class="card-content">
              <h2 class="card-title"><?php echo htmlspecialchars($row['Title']); ?></h2>
              <p class="card-destination"><?php echo htmlspecialchars($row['DestName']); ?></p>
              <div class="card-actions">
                <a class="btn-edit" href="adminpanel.php?edit=<?php echo $row['idArticles']; ?>">Edit</a>
                <?php if ($isAdmin): ?>
                  <a class="btn-delete" href="adminpanel.php?delete=<?php echo $row['idArticles']; ?>" onclick="return confirm('Are you sure you want to delete this article?');">Delete</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center;">No articles found, bro.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Image preview script
    document.getElementById("image_upload").onchange = function(event) {
      var reader = new FileReader();
      reader.onload = function(){
        var output = document.getElementById("imagePreview");
        output.src = reader.result;
        output.style.display = "block";
      };
      if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
      }
    };
  </script>
</body>
</html>
<?php
$conn->close();

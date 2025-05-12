<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

// Kontrola přihlášení
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("You don't have access here, bro!");
}
if (!isset($_SESSION['user_id'])) {
    die("User ID is not set, bro.");
}

$currentUserId = $_SESSION['user_id'];
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Připojení k databázi
$conn = new mysqli("localhost", "root", "", "travelblog");
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
}

// 🔥 Mazání článku
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($isAdmin) {
        $sqlDelete = "DELETE FROM articles WHERE idArticles = $deleteId";
    } else {
        $sqlDelete = "DELETE FROM articles WHERE idArticles = $deleteId AND Author = $currentUserId";
    }
    $conn->query($sqlDelete);
    header("Location: adminpanel.php");
    exit;
}

// Výchozí hodnoty pro formulář
$title = "";
$content = "";
$destination = "";
$articleId = 0;

// Editace článku
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
        $title = $rowEdit['Title'];
        $content = $rowEdit['Content'];
        $destination = $rowEdit['DestName'];
    } else {
        die("You cannot edit this article, bro!");
    }
}

// Odeslání formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isUpdate = isset($_POST['articleId']) && $_POST['articleId'] > 0;
    $articleIdPost = (int)($_POST['articleId'] ?? 0);
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $destinationName = $_POST['destination'] ?? '';
    $image = "";
    $datePublic = date('Y-m-d');
    $author = $currentUserId;

    // Nahrání obrázku
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $_FILES['image_upload']['name'];
        $filetype = $_FILES['image_upload']['type'];
        $filesize = $_FILES['image_upload']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
        if ($filesize > PHP_INT_MAX) die("Error: File is too large.");
        if (in_array($filetype, $allowed)) {
            $new_filename = uniqid() . "." . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            $relative_path = 'uploads/' . $new_filename;

            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $upload_dir . $new_filename)) {
                $image = $relative_path;
            } else {
                die("Error: Problem uploading the file.");
            }
        } else {
            die("Error: Problem with file.");
        }
    }

    // Najdi nebo přidej destinaci
    $destinationId = 0;
    $destQuery = "SELECT idDestination FROM destination WHERE Name='" . $conn->real_escape_string($destinationName) . "' LIMIT 1";
    $destResult = $conn->query($destQuery);
    if ($destResult && $destResult->num_rows > 0) {
        $destinationId = $destResult->fetch_assoc()['idDestination'];
    } else {
        $insertDest = "INSERT INTO destination (Name) VALUES ('" . $conn->real_escape_string($destinationName) . "')";
        if ($conn->query($insertDest) === TRUE) {
            $destinationId = $conn->insert_id;
        } else {
            die("Error inserting destination: " . $conn->error);
        }
    }

    // Uložení článku
    if ($isUpdate) {
        if (!$isAdmin) {
            $checkSql = "SELECT idArticles FROM articles WHERE idArticles = $articleIdPost AND Author = $currentUserId LIMIT 1";
            $checkRes = $conn->query($checkSql);
            if (!$checkRes || $checkRes->num_rows === 0) {
                die("You cannot edit this article, bro!");
            }
        }

        $sqlOld = "SELECT Image FROM articles WHERE idArticles = $articleIdPost LIMIT 1";
        $resOld = $conn->query($sqlOld);
        $oldImage = ($resOld && $resOld->num_rows > 0) ? $resOld->fetch_assoc()['Image'] : "";
        if ($image === "") $image = $oldImage;

        $sqlUpdate = "UPDATE articles 
                      SET Title='" . $conn->real_escape_string($title) . "',
                          Content='" . $conn->real_escape_string($content) . "',
                          Image='" . $conn->real_escape_string($image) . "',
                          Destination='" . $conn->real_escape_string($destinationId) . "' 
                      WHERE idArticles = $articleIdPost LIMIT 1";
        $resultMsg = $conn->query($sqlUpdate) === TRUE ? "Article has been updated, bro!" : "Error while updating: " . $conn->error;
    } else {
        $sqlInsert = "INSERT INTO articles (Title, Content, Image, Author, Destination, DatePublic) 
                      VALUES ('" . $conn->real_escape_string($title) . "',
                              '" . $conn->real_escape_string($content) . "',
                              '" . $conn->real_escape_string($image) . "',
                              '" . $conn->real_escape_string($author) . "',
                              '" . $conn->real_escape_string($destinationId) . "',
                              '$datePublic')";
        $resultMsg = $conn->query($sqlInsert) === TRUE ? "Article has been added, bro!" : "Error while adding: " . $conn->error;
    }

    $articleId = 0;
    $title = "";
    $content = "";
    $destination = "";
}

// Výpis článků
$sqlAll = "SELECT a.idArticles, a.Title, a.DatePublic, d.Name as DestName, a.Image 
           FROM articles a 
           JOIN destination d ON a.Destination = d.idDestination";
if (!$isAdmin) {
    $sqlAll .= " WHERE a.Author = $currentUserId";
}
$sqlAll .= " ORDER BY a.idArticles DESC";
$resAll = $conn->query($sqlAll);

// Latte
$latte = new Engine;
$latte->render(__DIR__ . '/../templates/adminpanel.latte', [
    'isAdmin' => $isAdmin,
    'currentUserId' => $currentUserId,
    'articleId' => $articleId,
    'title' => $title,
    'content' => $content,
    'destination' => $destination,
    'resAll' => $resAll,
    'resultMsg' => $resultMsg ?? null
]);

$conn->close();

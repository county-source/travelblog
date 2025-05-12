<?php
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Získání ID článku
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No blog ID provided, bro.");
}
$blogId = intval($_GET['id']);

// Připojení k databázi
$conn = new mysqli("localhost", "root", "", "travelblog");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL dotaz
$sql = "SELECT a.*, d.Name AS DestinationName, u.UserName AS AuthorName
        FROM articles a
        JOIN destination d ON a.Destination = d.idDestination
        JOIN users u ON a.Author = u.idUsers
        WHERE a.idArticles = {$blogId}
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $blog = $result->fetch_assoc();
} else {
    die("Blog not found, bro.");
}

$conn->close();

// Latte render
$latte = new Engine;
$latte->render(__DIR__ . '/../templates/dynamic.latte', [
    'blog' => $blog
]);

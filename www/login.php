<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelblog";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Nepovedlo se připojit: " . $conn->connect_error);
}

$message = null;
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE UserEmail = ? AND Password = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['idUsers'];
        $_SESSION['user_name'] = $user['UserName'];
        $_SESSION['role'] = $user['Role'];
        header("Location: index.php");
        exit;
    } else {
        $message = "Špatné údaje, bro.";
    }

    $stmt->close();
}

$latte = new Engine;
$latte->render(__DIR__ . '/../templates/login.latte', [
    'message' => $message,
    'loggedIn' => $loggedIn
]);

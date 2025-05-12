<?php
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Připojení k databázi
$conn = new mysqli("localhost", "root", "", "travelblog");
if ($conn->connect_error) {
    die("Nepovedlo se připojit: " . $conn->connect_error);
}

$message = null;

// Zpracování formuláře
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST["name"] ?? '';
    $secondName = $_POST["second_name"] ?? '';
    $email = $_POST["email"] ?? '';
    $pass = $_POST["password"] ?? '';
    $confirmPass = $_POST["confirm_password"] ?? '';

    if ($pass !== $confirmPass) {
        $message = "Hesla se neshodují, bro.";
    } else {
        $fullName = $firstName . " " . $secondName;

        // Ověření, zda email už existuje
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE UserEmail=? LIMIT 1");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            $message = "Uživatel s tímto e-mailem už existuje, bro.";
        } else {
            // Vložení uživatele
            $insertStmt = $conn->prepare("INSERT INTO users (UserName, UserEmail, Password, Role) VALUES (?, ?, ?, 'delegate')");
            $insertStmt->bind_param("sss", $fullName, $email, $pass);

            if ($insertStmt->execute()) {
                $message = "Registrován! Můžeš se přihlásit, bro.";
            } else {
                $message = "Chyba při registraci: " . $conn->error;
            }
        }
    }
}

$conn->close();

// Latte render
$latte = new Engine;
$latte->render(__DIR__ . '/../templates/register.latte', [
    'message' => $message
]);

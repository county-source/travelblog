<?php
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$latte = new Engine;
$latte->render(__DIR__ . '/../templates/header.latte');

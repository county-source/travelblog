<?php
require __DIR__ . '/../vendor/autoload.php'; // správná relativní cesta

use Latte\Engine;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$latte = new Engine;

// Pokud chceš použít cache pro rychlost:
// $latte->setTempDirectory(__DIR__ . '/../temp');

$latte->render(__DIR__ . '/../templates/index.latte');

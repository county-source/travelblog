<?php
session_start();
session_destroy();

// Přesměrování na hlavní stránku
header("Location: index.php");
exit;

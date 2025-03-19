<?php
session_start();
session_destroy();

// Po odhlášení přesměrujeme na hlavní stránku (nebo kamkoliv)
header("Location: index.php");
exit();

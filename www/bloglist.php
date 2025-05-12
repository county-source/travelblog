<?php
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Připojení k DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelblog";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Získání parametrů z URL
$search = $_GET['search'] ?? '';
$continent = $_GET['continent'] ?? 'none';
$dateOrder = $_GET['dateorder'] ?? 'none';

// Kontinent → destinace
$continentMap = [
    'africa' => ['South Africa'],
    'asia' => ['Japan', 'China'],
    'europe' => ['Italy'],
    'north-america' => ['New York'],
    'south-america' => ['Peru'],
    'australia' => ['Australia'],
    'antarctica' => ['???']
];

$whereClauses = [];

// Fulltext filtr
if (!empty($search) && $search !== 'none') {
    $searchEsc = $conn->real_escape_string($search);
    $whereClauses[] = "(a.Title LIKE '%{$searchEsc}%' OR a.Content LIKE '%{$searchEsc}%')";
}

// Kontinent filtr
if ($continent !== 'none' && isset($continentMap[$continent])) {
    $escapedDestinations = array_map(fn($d) => $conn->real_escape_string($d), $continentMap[$continent]);
    if (!empty($escapedDestinations)) {
        $destList = "'" . implode("','", $escapedDestinations) . "'";
        $whereClauses[] = "d.Name IN ({$destList})";
    } else {
        $whereClauses[] = "1=0";
    }
}

// Řazení
$orderBy = "ORDER BY a.idArticles DESC";
if ($dateOrder === 'newest') {
    $orderBy = "ORDER BY a.DatePublic DESC";
} elseif ($dateOrder === 'oldest') {
    $orderBy = "ORDER BY a.DatePublic ASC";
}

// SQL
$sql = "SELECT a.*, u.UserName AS AuthorName, d.Name AS DestinationName
        FROM articles a
        JOIN users u ON a.Author = u.idUsers
        JOIN destination d ON a.Destination = d.idDestination";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " $orderBy";

// Výsledky
$result = $conn->query($sql);

// Latte render
$latte = new Engine;
$latte->render(__DIR__ . '/../templates/bloglist.latte', [
    'search' => $search,
    'continent' => $continent,
    'dateOrder' => $dateOrder,
    'result' => $result
]);

$conn->close();

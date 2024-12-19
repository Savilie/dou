<?php
require_once(__DIR__ . "/../../config.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

if (isset($_GET['discordid'])) {
    $discordid = $_GET['discordid'];

    $stmt = $pdo->prepare("SELECT * FROM characters WHERE discordid=?");
    $stmt->execute([$discordid]);
    $count = $stmt->fetchAll();

    if (sizeof($count) > 0) {
        $data = [
            'status' => true,
        ];
    } else {
        $data = [
            'status' => false,
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
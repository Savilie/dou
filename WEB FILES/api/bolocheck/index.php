<?php
require_once(__DIR__ . "/../../config.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

$result = $pdo->query("SELECT * FROM bolos");
$count = $pdo->query("SELECT * FROM bolos");
$increment = 1;
if (sizeof($count->fetchAll()) > 0) {

        $data = [
            'status' => 'Found',
            'result' => array()
        ];

        foreach ($result as $row)
        {
            $data['result'][] = array (  
                'ID' => $row['ID'],
                'type' => $row['type'],
                'details' => $row['details'],
                'plate' => $row['plate'],
                'date' => $row['date'],
                'time' => $row['time'],
            );
        }

} else {
    $data = [
        'status' => 'No Bolos',
    ];
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
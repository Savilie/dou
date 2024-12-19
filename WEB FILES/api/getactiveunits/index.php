<?php
require_once(__DIR__ . "/../../config.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

$result = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7'");
$count = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7'");
$increment = 1;
if (sizeof($count->fetchAll()) > 0) {

        $data = [
            'result' => array()
        ];

        foreach ($result as $row)
        {
            $data['result'][] = array (  
                'identifier' => $row['identifier'],
                'department' => $row['currdept'],
                'status' => $row['currstatus'],
                'discordid' => $row['discordid'],
            );
        }

} else {
    $data = [
        'status' => 'No Active Units',
    ];
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
<?php
require_once(__DIR__ . "/../../config.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

if (isset($_GET['q'])) {
    $nc = $_GET['q'];

    $stmt = $pdo->prepare("SELECT * FROM characters WHERE name=?");
    $stmt->execute([$nc]);
    $result = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM characters WHERE name=?");
    $stmt->execute([$nc]);
    $count = $stmt->fetchAll();

    if (sizeof($count) > 0) {

        foreach ($result as $row)
        {
            $civid = $row['ID'];
            $result2 = $pdo->query("SELECT * FROM warrants WHERE civid='$civid'");
            $warrant = 'NO WARRANTS';
            foreach ($result2 as $row2)
            {
                $warrant = 'HAS WARRANTS';
            }
            $dead = $row['dead'];
            if ($dead == 0) {
                $deadStatus = 'Alive';
            } else {
                $deadStatus = 'Dead';
            }
            $data = [
                'status' => 'Found',
                'image' => $row['image'],
                'info' => [
                    'name' => $row['name'],
                    'dob' => $row['dob'],
                    'haircolor' => $row['haircolor'],
                    'address' => $row['address'],
                    'gender' => $row['gender'],
                    'race' => $row['race'],
                    'build' => $row['build'],
                    'occupation' => $row['occupation'],
                    'dead' => $deadStatus,
                    'warrant' => $warrant,
                ],
                'permits' => [
                    'drivers' => $row['drivers'],
                    'weapons' => $row['weapons'],
                    'hunting' => $row['hunting'],
                    'fishing' => $row['fishing'],
                    'commercial' => $row['commercial'],
                    'boating' => $row['boating'],
                    'aviation' => $row['aviation'],
                ],
                'medical' => [
                    'bloodtype' => $row['bloodtype'],
                    'emergcontact' => $row['emergcontact'],
                    'allergies' => $row['allergies'],
                    'medication' => $row['medication'],
                ],
            ];
        }
    } else {
        $data = [
            'status' => 'Error 2',
        ];
    }
} else {
    $data = [
        'status' => 'Error 1',
    ];
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
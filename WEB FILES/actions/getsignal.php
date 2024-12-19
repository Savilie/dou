<?php
require_once(__DIR__ . "/../config.php");

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE currsignal='1'");
foreach ($result as $row)
{
    $currsignal = $row['currsignal'];
    $currsound = $row['currsound'];

    if ($currsignal == 1) {
?>
        <br><h4 class="text-center text-danger">SIGNAL 100 IN EFFECT</span></h4>
<?php
    }

    if ($currsound == 1) {
?>
        <script>
            var audio = new Audio('../assets/audio/signal100.mp3');
            audio.volume = 0.2;
            audio.play();
        </script>
<?php
        sleep(3);
        $result2 = $pdo->query("UPDATE users SET currsound='0'");
    }
}
?>
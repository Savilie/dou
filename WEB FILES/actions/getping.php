<?php
require_once(__DIR__ . "/../config.php");
session_start();
$user_discordid = $_SESSION['user_discordid'];

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE currping='1' AND discordid='$user_discordid'");
foreach ($result as $row)
{
    $currping = $row['currping'];

    if ($currping == 1) {
?>
        <br><h4 class="text-center text-success">YOU HAVE BEEN PINGED BY DISPATCH</span></h4>
        <script>
            var audio = new Audio('../assets/audio/unitPing.mp3');
            audio.volume = 0.2;
            audio.play();
        </script>
<?php
        $result2 = $pdo->query("UPDATE users SET currping='0' WHERE discordid='$user_discordid'");
    }
}
?>
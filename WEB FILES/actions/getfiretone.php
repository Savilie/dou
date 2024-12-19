<?php
session_start();
require_once(__DIR__ . "/../config.php");
$user_discordid = $_SESSION['user_discordid'];

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE currfiretone!='0'");
foreach ($result as $row)
{
    $currfiretone = $row['currfiretone'];

    if ($currfiretone == 1) {
        $file = "allcall.mp3";
        $type = "All Call";
    } else if ($currfiretone == 2) {
        $file = "ls.mp3";
        $type = "LS Station";
    } else if ($currfiretone == 3) {
        $file = "bc.mp3";
        $type = "BC Station";
    } else if ($currfiretone == 4) {
        $file = "ems.mp3";
        $type = "EMS";
    } else if ($currfiretone == 5) {
        $file = "marshal.mp3";
        $type = "Fire Marshall";
    }

?>
        <br><h4 class="text-center text-danger">!! ALERT TONE - <?php echo $type; ?> !!</span></h4>
        <script>
            var audio = new Audio('../assets/audio/alerts/<?php echo $file; ?>');
            audio.volume = 0.1;
            audio.play();
        </script>
<?php
    sleep(5);
    $result2 = $pdo->query("UPDATE users SET currfiretone='0'");
}
?>
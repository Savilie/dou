<?php
require_once(__DIR__ . "/../config.php");
session_start();

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE currpanic='1'");
foreach ($result as $row)
{
    $panic_discordid = $row['discordid'];
    $panic_identifier = $row['identifier'];
    $panic_currpanic = $row['currpanic'];
    $panic_currpaniclocation = $row['currpaniclocation'];

    if ($panic_currpanic == 1) {
?>
        <script>
            var audio = new Audio('../assets/audio/panicButton.mp3');
            audio.volume = 0.2;
            audio.play();
        </script>
        <br><h4 class="text-center text-danger">
            <?php
            if ($_SESSION['supervisor'] == 1 || $_SESSION['dispatchperms'] == 1)
            {
            ?>
            <!-- <button class="btn btn-outline-danger btn-sm">Disable</button> -->
            <a style="text-decoration: none; color: white; right: 0; padding-right: 5px;" title="Disable Units Panic" href="../actions/department_functions.php?panic2=0&unitdiscordid=<?php echo $panic_discordid; ?>&paniclocation="><span>&times;</span></a>
            <?php
            }
            ?>
            PANIC BUTTON BY <span style="color: white;"><?php echo $panic_identifier; ?></span> AT <span style="color: white;"><?php echo $panic_currpaniclocation; ?></span></h4>

<?php
    }
}
?>
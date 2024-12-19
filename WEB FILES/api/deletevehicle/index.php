<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../actions/discord_functions.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// Information Sent
$discordid = htmlspecialchars($_GET['discordid']);
$plate = htmlspecialchars($_GET['plate']);
$secret = htmlspecialchars($_GET['secret']);

if ($secret == SECRET_KEY)
{

    // DELETE FROM DATABASE
    $result = $pdo->query("DELETE FROM vehicles WHERE plate='$plate' AND discordid='$discordid'");

    // LOG IT
    $log = new richEmbed("VEHICLE DELETED", "By <@{$discordid}>");
    $log->addField("Plate:", $plate, false);
    $log = $log->build();
    sendLog($log, CHARACTER_LOGS);

}

?>
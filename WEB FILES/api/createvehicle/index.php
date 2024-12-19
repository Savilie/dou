<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../actions/discord_functions.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// Information Sent
$discordid = htmlspecialchars($_GET['discordid']);
$name = htmlspecialchars($_GET['name']);
$plate = htmlspecialchars($_GET['plate']);
$model = htmlspecialchars($_GET['model']);
$color = htmlspecialchars($_GET['color']);
$secret = htmlspecialchars($_GET['secret']);

if ($secret == SECRET_KEY)
{

    // Set Variables
    $insurance = "Valid";
    $regstate = "Los Santos";
    $flags = "None";

    // Get Character ID
    $getcharid = $pdo->query("SELECT * FROM characters WHERE discordid='$discordid' AND name='$name'");

    foreach ($getcharid as $row)
    {
        $charid = $row['ID'];
    }

    // Insert
    $stmt = $pdo->prepare("INSERT INTO vehicles (discordid, charid, plate, makemodel, color, insurance, regstate, flags) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute(array($discordid, $charid, $plate, $model, $color, $insurance, $regstate, $flags));

    // LOG IT
    $log = new richEmbed("NEW VEHICLE", "By <@{$discordid}>");
    $log->addField("Name:", $name, false);
    $log->addField("Plate:", $plate, false);
    $log->addField("Model:", $model, false);
    $log->addField("Color:", $color, false);
    $log = $log->build();
    sendLog($log, CHARACTER_LOGS);

}

?>
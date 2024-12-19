<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../actions/discord_functions.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// Information Sent
$discordid = htmlspecialchars($_GET['discordid']);
$name = htmlspecialchars($_GET['name']);
$secret = htmlspecialchars($_GET['secret']);

if ($secret == SECRET_KEY)
{

    // DELETE FROM DATABASE
    $result = $pdo->query("DELETE FROM characters WHERE name='$name' AND discordid='$discordid'");

    // LOG IT
    $log = new richEmbed("CHARACTER DELETED", "By <@{$discordid}>");
    $log->addField("Name:", $oldname, false);
    $log = $log->build();
    sendLog($log, CHARACTER_LOGS);

}

?>
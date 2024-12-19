<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../actions/discord_functions.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// Information Sent
$discordid = htmlspecialchars($_GET['discordid']);
$oldname = htmlspecialchars($_GET['oldname']);
$newname = htmlspecialchars($_GET['newname']);
$dob = htmlspecialchars($_GET['dob']);
$gender = htmlspecialchars($_GET['gender']);
$secret = htmlspecialchars($_GET['secret']);

if ($secret == SECRET_KEY)
{

    // UPDATE DATABASE
    $stmt = $pdo->prepare("UPDATE characters SET name=?, dob=?, gender=? WHERE discordid='$discordid' AND name='$oldname'");
    $stmt->execute([$newname, $dob, $gender]);

    // LOG IT
    $log = new richEmbed("CHARACTER UPDATED", "By <@{$discordid}>");
    $log->addField("Old Name:", $oldname, false);
    $log->addField("New Name:", $newname, false);
    $log->addField("Gender:", $gender, false);
    $log->addField("DOB:", $dob, false);
    $log = $log->build();
    sendLog($log, CHARACTER_LOGS);

}

?>
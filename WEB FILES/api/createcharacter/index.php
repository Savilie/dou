<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../actions/discord_functions.php");

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// Information Sent
$discordid = htmlspecialchars($_GET['discordid']);
$name = htmlspecialchars($_GET['name']);
$dob = htmlspecialchars($_GET['dob']);
$gender = htmlspecialchars($_GET['gender']);
$secret = htmlspecialchars($_GET['secret']);

if ($secret == SECRET_KEY)
{

    // Create SSM
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $character_ssn = '';
    for ($i = 0; $i < 9; $i++) {
        $character_ssn .= $characters[rand(0, $charactersLength - 1)];
    }

    // Set Variables
    $character_image = "https://imgur.com/PBXTxT5.png";
    $character_haircolor = "Black";
    $character_address = "None";
    $character_build = "Average";
    $race = "Caucasian";

    // Insert
    $stmt = $pdo->prepare("INSERT INTO characters (discordid, name, dob, haircolor, address, gender, race, build, ssn, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute(array($discordid, $name, $dob, $character_haircolor, $character_address, $gender, $race, $character_build, $character_ssn, $character_image));

    // LOG IT
    $log = new richEmbed("NEW CHARACTER", "By <@{$discordid}>");
    $log->addField("Name:", $name, false);
    $log->addField("DOB:", $dob, false);
    $log->addField("Hair Color:", $character_haircolor, false);
    $log->addField("Address:", $character_address, false);
    $log->addField("Gender:", $gender, false);
    $log->addField("Race:", $race, false);
    $log->addField("Build:", $character_build, false);
    $log->addField("SSN:", $character_ssn, false);
    $log->addField("Image:", $character_image, false);
    $log = $log->build();
    sendLog($log, CHARACTER_LOGS);

}

?>
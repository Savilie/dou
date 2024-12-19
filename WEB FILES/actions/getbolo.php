<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/discord_functions.php");
session_start();

$user_discordid = $_SESSION['user_discordid'];

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

// CHECK DEPT
$checkuser = $pdo->query("SELECT * FROM users WHERE discordid='$user_discordid'");
foreach ($checkuser as $row)
{
  $currdept = $row['currdept'];
}


$result = $pdo->query("SELECT * FROM bolos ORDER BY ID DESC");
$bolos = $pdo->query("SELECT * FROM bolos");

if ($_SESSION['dispatchperms'] == 1 || $_SESSION['leoperms'] == 1 || $_SESSION['adminperms'] == 1) {
if (sizeof($bolos->fetchAll()) > 0) {
?>

                      <thead>
                        <tr>
                          <th>Type</th>
                          <th>Details</th>
                          <th>Date & Time</th>
                          <th>Issued By</th>
                          <?php
                            if ($_SESSION['dispatchperms'] == 1 & $currdept == "DISPATCH")
                            {
                          ?>
                          <th></th>
                          <?php
                            }
                          ?>
                        </tr>
                      </thead>
                      <tbody style="color: white;">
<?php

                    foreach ($result as $row) {
                      $bolo_type = $row['type'];
                      $bolo_plate = $row['plate'];
                      $bolo_details = $row['details'];
                      $bolo_unitidentifier = $row['unitidentifier'];
?>
                        <tr>
                            <td><?php echo $bolo_type; ?></td>
                            <td><?php if ($bolo_type == "Vehicle") {echo $bolo_plate . " | " . $bolo_details;} else {echo $bolo_details;} ?></td>
                            <td><?php echo $row['date'] . " | " . $row['time']; ?></td>
                            <td><?php echo $bolo_unitidentifier; ?></td>
                          <?php
                            if (($_SESSION['dispatchperms'] == 1 & $currdept == "DISPATCH") || $_SESSION['supervisor'] == 1 & $currdept == "LEO")
                            {
                          ?>
                            <td><button type="button" onclick="deleteBolo('<?php echo $row['ID']; ?>')" class="btn btn-outline-danger">Delete</button></td>
                          <?php
                            }
                          ?>
                        </tr>	
<?php
                    }
?>
                      </tbody>
<?php
} else {
?>
    <p class="text-success">No Current Bolo's</p>
<?php
}
}
?>
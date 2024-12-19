<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../actions/discord_functions.php");
session_start();

$user_discordid = $_SESSION['user_discordid'];
$characterID = $_SESSION["characterID"];

function getColor($status)
{
  if ($status == "Valid")
  {
    return "green";
  } 
  else if ($status == "Unobtained")
  {
    return "#F0BF48";
  }
  else if ($status == "Invalid")
  {
    return "#F0BF48";
  }
  else if ($status == "Unknown")
  {
    return "#F0BF48";
  }
  else
  {
    return "red";
  }
}

if ($characterID != "") {

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

    $license = $pdo->query("SELECT * FROM characters WHERE ID='$characterID'");

    foreach ($license as $row) {
      $drivers = $row['drivers'];
      $weapons = $row['weapons'];
      $hunting = $row['hunting'];
      $fishing = $row['fishing'];
      $commercial = $row['commercial'];
      $boating = $row['boating'];
      $aviation = $row['aviation'];
?>
      <tr>
        <td style="color: <?php echo getColor($drivers); ?>"><?php echo $drivers; ?></td>
        <td style="color: <?php echo getColor($weapons); ?>;"><?php echo $weapons; ?></td>
        <td style="color: <?php echo getColor($hunting); ?>;"><?php echo $hunting; ?></td>
        <td style="color: <?php echo getColor($fishing); ?>;"><?php echo $fishing; ?></td>
        <td style="color: <?php echo getColor($commercial); ?>;"><?php echo $commercial; ?></td>
        <td style="color: <?php echo getColor($boating); ?>;"><?php echo $boating; ?></td>
        <td style="color: <?php echo getColor($aviation); ?>;"><?php echo $aviation; ?></td>
      </tr>
<?php
    }
}
?>
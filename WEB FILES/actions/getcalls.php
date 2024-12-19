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

$getstatus = $pdo->query("SELECT * FROM users WHERE discordid='$user_discordid' AND currstatus != '10-7'");

$activecalls = $pdo->query("SELECT * FROM activecalls WHERE attachedunits LIKE '%$user_discordid%' AND status='0'");
$result = $pdo->query("SELECT * FROM activecalls WHERE attachedunits LIKE '%$user_discordid%' AND status='0'");

if ($_SESSION['dispatchperms'] == 1 || $_SESSION['leoperms'] == 1 || $_SESSION['fireemsperms'] == 1 || $_SESSION['adminperms'] == 1) {
if (sizeof($getstatus->fetchAll()) > 0) {

if (sizeof($activecalls->fetchAll()) > 0) {
?>
                    <table class="table text-center">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Type</th>
                          <th>Location</th>
                          <th>Postal</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody style="color: white;">
<?php

                    foreach ($result as $row) {
                      $calltype = $row['calltype'];
                      $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                      $charactersLength = strlen($characters);
                      $modalstr = '';
                      for ($i = 0; $i < 10; $i++) {
                          $modalstr .= $characters[rand(0, $charactersLength - 1)];
                      }
?>       
                        <tr>
                            <td><?php echo $row['ID']; ?></td>
                            <td><?php echo $row['calltype']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['postal']; ?></td>
                            <td><p class="btn btn-outline-info m-2" data-toggle="modal" data-target="#<?php echo $modalstr; ?>">View Details</p></td>
                        </tr> 
<?php
                    }
?>
                      </tbody>
                    </table>
<!-- Narrative Call Modal -->
<div class="modal fade" id="<?php echo $modalstr; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Call Narrative</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <?php echo $row['narrative']; ?>
      </div>
    </div>
  </div>
</div>
<?php

} else {
  echo "<p class='text-success'>No Current Call's</p>";
}

} else {
?>
    <p class="text-success">No Current Call's</p>
<?php
}
}
?>
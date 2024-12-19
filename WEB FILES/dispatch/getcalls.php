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

$result = $pdo->query("SELECT * FROM activecalls WHERE status='0' ORDER BY ID DESC");
$activecalls = $pdo->query("SELECT * FROM activecalls WHERE status='0'");

if (sizeof($activecalls->fetchAll()) > 0) {
?>
                    <table class="table text-center">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Type</th>
                          <th>Location & Postal</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody style="color: white;">
<?php

                    foreach ($result as $row) {
                      $callID = $row['ID'];
                      $attachedunits = $row['attachedunits'];
                      $postal = $row['postal'];
                      $location = $row['location'];
                      $calltype = $row['calltype'];
                      $seperatedunits = explode(",",$attachedunits);

                      $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                      $charactersLength = strlen($characters);
                      $modalstr = '';
                      $idstr = '';
                      for ($i = 0; $i < 10; $i++) {
                          $modalstr .= $characters[rand(0, $charactersLength - 1)];
                          $idstr .= $characters[rand(0, $charactersLength - 1)];
                          $idstr2 .= $characters[rand(0, $charactersLength - 1)];
                          $idstr3 .= $characters[rand(0, $charactersLength - 1)];
                          $idstr4 .= $characters[rand(0, $charactersLength - 1)];
                          $idstr5 .= $characters[rand(0, $charactersLength - 1)];
                      }
?>       
                        <tr>
                            <td><?php echo $callID; ?></td>
                            <td><?php echo $row['calltype']; ?></td>
                            <td><?php echo $row['location'].' | '.$row['postal'];  ?></td>
                            <td><p class="btn btn-outline-info m-2" data-toggle="modal" data-target="#<?php echo $modalstr; ?>">View Details</p></td>
                        </tr>
<!-- Narrative Call Modal -->
<div class="modal fade" id="<?php echo $modalstr; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Call Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <div id="narrativesuccess"></div>
        <?php 
        echo '<b>Call Type:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<select onchange="checkCall(this);" class="form-control p-input" id="'.$idstr2.'" style="color: white;" required>
          <option value="'.$calltype.'">'.$calltype.'</option>
          <option value="10-10 | Fight in Progress">10-10 | Fight in Progress</option>
          <option value="10-11 | Traffic Stop">10-11 | Traffic Stop</option>
          <option value="10-13 | Shots Fired">10-13 | Shots Fired</option>
          <option value="10-16 | Stolen Vehicle">10-16 | Stolen Vehicle</option>
          <option value="10-17 | Suspicious Person">10-17 Suspicious Person</option>
          <option value="10-50 | Motor Vehicle Accident">10-50 | Motor Vehicle Accident</option>
          <option value="10-55 | Intoxicated Driver">10-55 | Intoxicated Driver</option>
          <option value="10-56 | Intoxicated Pedestrian">10-56 | Intoxicated Pedestrian</option>
          <option value="10-60 | Individual Armed with a Gun">10-60 | Individual Armed with a Gun</option>
          <option value="10-61 | Individual Armed with a Knife">10-61 | Individual Armed with a Knife</option>
          <option value="10-62 | Kidnapping">10-62 | Kidnapping</option>
          <option value="10-64 | Sexual Assault">10-64 | Sexual Assault</option>
          <option value="10-65 | Escorting Prisoner">10-65 | Escorting Prisoner</option>
          <option value="10-66 | Reckless Driver">10-66 | Reckless Driver</option>
          <option value="10-67 | Fire">10-67 | Fire</option>
          <option value="10-68 | Armed Robbery">10-68 | Armed Robbery</option>
          <option value="10-70 | Foot Pursuit">10-70 | Foot Pursuit</option>
          <option value="10-80 | Vehicle Pursuit">10-80 | Vehicle Pursuit</option>
          <option value="10-99 | Officer in Distress">10-99 | Officer in Distress</option>
          <option value="Other">Other</option>
              </select>';
        echo '<br><div class="form-group" id="ifOther2" style="display: none;">
          <input type="text" placeholder="Enter other call type..." class="form-control p-input" id="'.$idstr5.'">
        </div>';
        echo '<br><b>Call Location & Postal:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<div class="row"><div class="col-md-6"><input type="text" style="color: white;" class="form-control p-input" id="'.$idstr3.'" value="'.$location.'"></input></div>
          <div class="col-md-6"><input type="text" style="color: white;" class="form-control p-input" value="'.$postal.'" id="'.$idstr4.'"></input></div></div>';

        echo '<br><br><b>Narrative:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<textarea type="text" style="white-space: pre-wrap; height: 150px; color: white;" class="form-control p-input" id="'.$idstr.'">'.$row['narrative'].'</textarea>';

        echo "<br><br><b>Current Units On Call:</b><br>";

        foreach ($seperatedunits as $value)
        {
          $value = str_replace(' ', '', $value);
          $getidentifier = $pdo->query("SELECT * FROM users WHERE discordid='$value'");
          foreach ($getidentifier as $row2)
          {
            echo "<span class='text-muted'>".$row2['identifier']."</span><br>";
          }
        }

        echo "<br><br><b>Date:</b> <span class='text-muted'>".$row['date']."</span>";; 
        echo "<br><b>Time:</b> <span class='text-muted'>".$row['time']."</span>";; 
        ?>
        <br><br>
          <button type="button" onclick="updateCall(<?php echo $callID; ?>, '<?php echo $idstr; ?>', '<?php echo $idstr2; ?>', '<?php echo $idstr3; ?>', '<?php echo $idstr4; ?>', '<?php echo $idstr5; ?>')" class="btn btn-outline-info">Update</button>
          <span class="p-2"></span>
          <button type="button" onclick="deleteCall(<?php echo $callID; ?>)" class="btn btn-outline-danger">End Call</button>
      </div>
    </div>
  </div>
</div>
<?php
                    }
?>
                      </tbody>
                    </table>
<?php

} else {
  echo "<p class='text-success'>No Current Call's</p>";
}


?>
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

$result = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7' AND currdept='FIREEMS' ORDER BY identifier ASC");
$activeleounits = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7' AND currdept='FIREEMS'");

if ($_SESSION['dispatchperms'] == 1 || $_SESSION['adminperms'] == 1) {
if (sizeof($activeleounits->fetchAll()) > 0) {
?>
                    <table class="table text-center">
                      <thead>
                        <tr>
                          <th>Unit Identifier</th>
                          <th>Current Status</th>
                          <th>Division</th>
                          <th>Apparatus</th>
                          <th>Assigned Call #</th>
                          <th>Ping</th>
                        </tr>
                      </thead>
                      <tbody style="color: white;">
<?php

                    foreach ($result as $row) {
                      $unit_ID = $row['ID'];
                      $unit_discordid = $row['discordid'];
                      $callNumber = "None";
                      $result2 = $pdo->query("SELECT * FROM activecalls WHERE attachedunits LIKE '%$unit_discordid%' AND status='0'");
                      $sizeresult2 = $pdo->query("SELECT * FROM activecalls WHERE attachedunits LIKE '%$unit_discordid%' AND status='0'");
                      $result4 = $pdo->query("SELECT * FROM divisions WHERE type='FIREEMS'");

                      if (sizeof($sizeresult2->fetchAll()) > 0)
                      {

                        foreach ($result2 as $row2)
                        {
                          $callNumber = $row2['ID'];
                          $result3 = $pdo->query("SELECT * FROM activecalls WHERE status='0'");
?>       
                        <tr>
                            <td><?php echo $row['identifier']; ?> <i><?php if ($row['showsupervisor'] == 1) {?>(<?php } if ($row['showsupervisor'] == 1) { echo 'Supervisor'; } if (($row['department'] != "") && ($row['department'] != "None")) { echo ' | '; } if ($row['department'] != "None") { echo $row['department']; } if ($row['showsupervisor'] == 1) {?>)<?php } ?></i></td>
                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitStatus(this, <?php echo $unit_ID; ?>);" style="color: white;" required>
                                <option value="<?php echo $row['currstatus']; ?>"><?php echo $row['currstatus']; ?></option>
                                <option value="10-8">10-8</option>
                                <option value="10-6">10-6</option>
                                <option value="10-11">10-11</option>
                                <option value="10-15">10-15</option>
                                <option value="10-23">10-23</option>
                                <option value="10-97">10-97</option>
                                <option value="10-99">10-99</option>
                                <option value="10-7">10-7</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitDivision(this, '<?php echo $unit_ID; ?>');" style="color: white;" required>
                                <option value="<?php echo $row['currdivision']; ?>"><?php echo $row['currdivision']; ?></option>
                                <?php
                                foreach ($result4 as $row4)
                                {
                                ?>
                                <option value="<?php echo $row4['name']; ?>"><?php echo $row4['name']; ?></option>
                                <?php
                                }
                                ?>
                                <option value="None">None</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitApparatus(this, '<?php echo $unit_ID; ?>');" style="color: white;" required>
                                <option value="<?php echo $row['currapparatus']; ?>"><?php echo $row['currapparatus']; ?></option>
                                <?php
                                foreach ($FIREEMS_APPARATUS as $name)
                                {
                                ?>
                                <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
                                <?php
                                }
                                ?>
                                <option value="None">None</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitCall(this, '<?php echo $unit_discordid; ?>');" style="color: white;" required>
                                <option value="<?php echo $callNumber; ?>"><?php echo $callNumber; ?></option>
                                <option value="None">None</option>
                                <?php
                                foreach ($result3 as $row3)
                                {
                                ?>
                                <option value="<?php echo $row3['ID']; ?>"><?php echo $row3['ID']; ?></option>
                                <?php
                                }
                                ?>
                              </select>
                            </td>
                            <td><a type="button" style="color: white;" onclick="unitPing(<?php echo $row['ID']; ?>)"><i class="mdi mdi-bell"></i></a></td>
                        </tr> 
<?php
                        }
                      } else 
                        {
                          $result3 = $pdo->query("SELECT * FROM activecalls WHERE status='0'");
?>
                        <tr>
                            <td><?php echo $row['identifier']; ?> <i><?php if ($row['showsupervisor'] == 1) {?>(<?php } if ($row['showsupervisor'] == 1) { echo 'Supervisor'; } if (($row['department'] != "") && ($row['department'] != "None")) { echo ' | '; } if ($row['department'] != "None") { echo $row['department']; } if ($row['showsupervisor'] == 1) {?>)<?php } ?></i></td>
                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitStatus(this, <?php echo $unit_ID; ?>);" style="color: white;" required>
                                <option value="<?php echo $row['currstatus']; ?>"><?php echo $row['currstatus']; ?></option>
                                <option value="10-8">10-8</option>
                                <option value="10-6">10-6</option>
                                <option value="10-11">10-11</option>
                                <option value="10-15">10-15</option>
                                <option value="10-23">10-23</option>
                                <option value="10-97">10-97</option>
                                <option value="10-99">10-99</option>
                                <option value="10-7">10-7</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitDivision(this, '<?php echo $unit_ID; ?>');" style="color: white;" required>
                                <option value="<?php echo $row['currdivision']; ?>"><?php echo $row['currdivision']; ?></option>
                                <?php
                                foreach ($result4 as $row4)
                                {
                                ?>
                                <option value="<?php echo $row4['name']; ?>"><?php echo $row4['name']; ?></option>
                                <?php
                                }
                                ?>
                                <option value="None">None</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitApparatus(this, '<?php echo $unit_ID; ?>');" style="color: white;" required>
                                <option value="<?php echo $row['currapparatus']; ?>"><?php echo $row['currapparatus']; ?></option>
                                <?php
                                foreach ($FIREEMS_APPARATUS as $name)
                                {
                                ?>
                                <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
                                <?php
                                }
                                ?>
                                <option value="None">None</option>
                              </select>
                            </td>

                            <td>
                              <select class="form-control p-input" style="height: 100%;" onchange="changeUnitCall(this, '<?php echo $unit_discordid; ?>');" style="color: white;" required>
                                <option value="<?php echo $callNumber; ?>"><?php echo $callNumber; ?></option>
                                <?php
                                foreach ($result3 as $row3)
                                {
                                ?>
                                <option value="<?php echo $row3['ID']; ?>"><?php echo $row3['ID']; ?></option>
                                <?php
                                }
                                ?>
                              </select>
                            </td>
                            <td><a type="button" style="color: white;" onclick="unitPing(<?php echo $row['ID']; ?>)"><i class="mdi mdi-bell"></i></a></td>
                        </tr> 
<?php
                        }
                    }
?>
                      </tbody>
                    </table>
<?php

} else {
  echo "<p class='text-success'>No Current Unit's</p>";
}
}

?>
<script>
function changeUnitStatus(that, id) {
  $.get('../actions/department_functions.php?changeunitstatus=TRUE&su='+id+'&ss='+that.value);
}

function changeUnitCall(that, id) {
  $.get('../actions/department_functions.php?assignunitcall=TRUE&su='+id+'&cid='+that.value);
}

function changeUnitDivision(that, id) {
  $.get('../actions/department_functions.php?changeunitdivision=TRUE&su='+id+'&sdiv='+that.value);
}

function changeUnitApparatus(that, id) {
  $.get('../actions/department_functions.php?changeunitapparatus=TRUE&su='+id+'&sdiv='+that.value);
}
</script>
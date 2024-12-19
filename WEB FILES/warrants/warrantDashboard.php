<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
checkBan();
$user_discordid = $_SESSION['user_discordid'];

if ($_SESSION['redirect'] != "/warrants/warrantDashboard.php") {
  $_SESSION['refresh'] = $_SESSION['redirect'];
}

if ($_SESSION['leoperms'] == 1 || $_SESSION['courtperms'] == 1 || $_SESSION['dispatchperms'] == 1)
{
  // Allowed In
} else {
  header('Location: ../index.php?notAuthorisedDepartment');
}

try{
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
  echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo SERVER_SHORT_NAME; ?> CAD | Warrants</title>
<!-- CSS -->
<link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
<link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
<link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<!-- FAVICON -->
<link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
</head>
<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<body id="modalCheck">
<div class="container-scroller">
<div class="horizontal-menu">
<!-- HEADER -->
<?php include "../includes/header.inc.php"; ?>
<!-- NAVBAR -->
<?php include "../includes/navbar.inc.php"; ?>
</div>

<div class="container-fluid page-body-wrapper">
<div class="main-panel">
<div class="content-wrapper">
<!-- ACTION DISPLAY -->
<?php if($actionMessage){echo $actionMessage;} ?>
<div style="text-align: center;">
<a href="<?php echo "..".$_SESSION['refresh']; ?>" onmouseover="this.style.color='<?php echo ACCENT_COLOR; ?>';" onmouseout="this.style.color='<?php echo SCROLLBAR_COLOR; ?>';" class="btn btn-dark" style="color: <?php echo SCROLLBAR_COLOR; ?>; background-color: <?php echo CARD_COLOR; ?>; font-size: 1.3rem; font-weight: 400; padding: 20px; border-radius: 20px; margin-bottom: 20px;}"><i class="mdi mdi-keyboard-return"></i> Back</a>
</div>
<?php
$_SESSION['redirect'] = "/warrants/warrantDashboard.php";
?>
<div class="row">
<div class="col-xl-6">
<div class="card">
<div class="card-body text-center">
<h3 class="mt-2 mb-1 text-center">Outstanding Warrants</h3><br>
<div class="row">
<?php
$result = $pdo->query("SELECT DISTINCT civid FROM warrants");
foreach ($result as $row)
{
  $civid = $row['civid'];
  
  $result2 = $pdo->query("SELECT * FROM characters WHERE ID='$civid'");
  
  foreach ($result2 as $row)
  {
    if (filter_var($row['image'], FILTER_VALIDATE_URL) === FALSE) {
      $image = "../assets/images/characters/default.png";
    } else {
      $image = $row['image'];
    }
    ?>
    <div class="col-md-4 p-1">
    <div class="">
    <div class="p-3">
    <img src="<?php echo $image; ?>" alt="Image" style="border-radius: 100px; width: 30%;">
    <br>
    <p style="margin: 10px; font-weight: 400;"><span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>;" href="?nameq=<?php echo $row['ID']; ?>"><?php echo $row['name']; ?></a></span></p>	
    </div>
    </div>
    </div>                  	
    <?php
  }
  ?>
  <?php
}
?>
</div>
</div>
</div>
</div>

<div class="col-xl-6">
<?php
if (isset($_GET['nameq']))
{
  $nameq = htmlspecialchars($_GET['nameq']);
  $character = $pdo->query("SELECT * FROM characters WHERE ID='$nameq'");
  
  foreach ($character as $row)
  {
    $character_id = $row['ID'];
    $character_name = $row['name'];
    $character_discordid = $row['discordid'];
    $character_dob = $row['dob'];
    $character_haircolor = $row['haircolor'];
    $character_address = $row['address'];
    $character_gender = $row['gender'];
    $character_race = $row['race'];
    $character_build = $row['build'];
    $character_occupation = $row['occupation'];
    $character_image = $row['image'];
    $dead = $row['dead'];
    if ($dead == 1)
    {
      $deadstatus = "Dead";
      $deadcolor = "red";
    } else {
      $deadstatus = "Alive";
      $deadcolor = "green";
    }
    
    $character_ssn = $row['ssn'];
    
    $bloodtype = $row['bloodtype'];
    $emergcontact = $row['emergcontact'];
    $allergies = $row['allergies'];
    $medication = $row['medication'];
    $organdonor = $row['organdonor'];
    
    $drivers = $row['drivers'];
    $weapons = $row['weapons'];
    $hunting = $row['hunting'];
    $fishing = $row['fishing'];
    $commercial = $row['commercial'];
    $boating = $row['boating'];
    $aviation = $row['aviation'];
  }
  
  $warrants = $pdo->query("SELECT * FROM warrants WHERE civid='$character_id' ORDER BY ID DESC");
  ?>
  <div class="card">
  <div class="card-body">
  <div style="font-size: 20px; text-align: right;">
  <a style="text-decoration: none; color: white;" href="warrantDashboard.php"><span>&times;</span></a>
  </div><br>
  <div class="text-center">
  <img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 15%; <?php if (checkOnline('discord:'.$character_discordid) == true) {echo "border: 3px solid #3ba55d;";} else {echo "border: 3px solid #747f8d;";} ?>">
  </div>
  <br>
  <div class="row">
  <div class="col-md-4">
  <h5>Name: <span class="text-muted"><?php echo $character_name; ?></span></h5>
  <h5>DOB: <span class="text-muted"><?php echo $character_dob; ?></span></h5>
  <h5>SSN: <span class="text-muted"><?php echo $character_ssn; ?></span></h5>
  <h5>Hair Color: <span class="text-muted"><?php echo $character_haircolor; ?></span></h5>
  <h5>Address: <span class="text-muted"><?php echo $character_address; ?></span></h5>
  <h5>Gender: <span class="text-muted"><?php echo $character_gender; ?></span></h5>
  <h5>Race: <span class="text-muted"><?php echo $character_race; ?></span></h5>
  <h5>Build: <span class="text-muted"><?php echo $character_build; ?></span></h5>
  <h5>Occupation: <span class="text-muted"><?php echo $character_occupation; ?></span></h5>
  <h5 style="color: <?php echo $deadcolor; ?>;"><?php echo $deadstatus; ?></h5>
  </div>
  <div class="col-md-4">
  <h5>Licenses:</h5>
  <span class="text-muted">Drivers: <span style="color: <?php echo  getColor($drivers); ?>;"><?php echo $drivers; ?></span></span><br>
  <span class="text-muted">Weapons: <span style="color: <?php echo  getColor($weapons); ?>;"><?php echo $weapons; ?></span></span><br>
  <span class="text-muted">Hunting: <span style="color: <?php echo  getColor($hunting); ?>;"><?php echo $hunting; ?></span></span><br>
  <span class="text-muted">Fishing: <span style="color: <?php echo  getColor($fishing); ?>;"><?php echo $fishing; ?></span></span><br>
  <span class="text-muted">Commercial: <span style="color: <?php echo  getColor($commercial); ?>;"><?php echo $commercial; ?></span></span><br>
  <span class="text-muted">Boating: <span style="color: <?php echo  getColor($boating); ?>;"><?php echo $boating; ?></span></span><br>
  <span class="text-muted">Aviation: <span style="color: <?php echo  getColor($aviation); ?>;"><?php echo $aviation; ?></span></span><br>
  </div>
  <div class="col-md-4">
  <h5>Medical Details:</h5>
  <span>Blood Type: <span class="text-muted"><?php echo $bloodtype; ?></span></span><br>
  <span>Emergency Contact: <span class="text-muted"><?php echo $emergcontact; ?></span></span><br>
  <span>Allergies: <span class="text-muted"><?php echo $allergies; ?></span></span><br>
  <span>Medication: <span class="text-muted"><?php echo $medication; ?></span></span><br>
  <?php
  if ($organdonor == "1")
  {
    ?>
    <h5 style="color: #f55f82;">DONOR</h5>
    <?php
  }
  ?>
  </div>
  </div>
  <br>
  <h5>Warrants: <?php if ($_SESSION['supervisor'] == 1) {?><p data-toggle="modal" data-target="#addWarrantModal" class="btn btn-outline-info m-2">Add</p><?php }?><span class="text-success pl-3" id="warrantsuccess"></span></h5>
  <span class="text-muted">=====================================</span><br>
  <?php
  foreach ($warrants as $row)
  {
    $warrants_unit_identifier = $row['unitidentifier'];
    $warrants_date = $row['date'];
    $warrants_time = $row['time'];
    $warrants_details = $row['details'];
    $warrants_requestingunit = $row['requestingunit'];
    ?>
    <div class="row">
    <div class="col-md-6">
    <span class="text-muted"><b>Details:</b> <?php echo $warrants_details; ?></span><br>
    <span class="text-muted"><b>Date & Time:</b> <?php echo $warrants_date . " | " . $warrants_time; ?></span><br>
    <span class="text-muted"><b>Requesting Unit:</b> <?php echo $warrants_requestingunit; ?></span><br>
    <span class="text-muted"><b>Signing Unit:</b> <?php echo $warrants_unit_identifier; ?></span><br>
    </div>
    <?php
    if ($_SESSION['supervisor'] == 1) {
      ?>
      <div class="col-md-6" style="top: 35px;">
      <button type="button" onclick="deleteWarrant(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger m-2">Delete</button><br>
      </div>
      <?php
    }
    ?>
    </div>
    <span class="text-muted">=====================================</span><br>
    <?php
  }
  
  if ($warrants_unit_identifier == "")
  {
    echo '<span class="text-muted text-success">None</span><br><span class="text-muted">=====================================</span><br>';
  }
  
  ?>
  </div>
  </div>
  <?php
}
?>
</div>
</div>
<br>
<br>

<!-- Warrant Modal -->
<div class="modal fade" id="addWarrantModal" tabindex="-1" role="dialog" aria-labelledby="addArrestsModal" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="addArrestsModal">Add Warrant</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<form class="forms-sample" action="../actions/department_functions.php" method="POST">
<div class="form-group">
<label>Violaters Name</label>
<input type="text" class="form-control p-input" value="<?php echo $character_name; ?>" readonly>
<input type="text" class="form-control p-input" id="warrant_id" name="warrant_id" value="<?php echo $character_id; ?>" hidden>
</div>
<div class="form-group">
<label>Violaters DOB</label>
<input type="text" class="form-control p-input" value="<?php echo $character_dob; ?>" readonly>
</div>
<div class="form-group">
<label>Violaters Address</label>
<input type="text" class="form-control p-input" value="<?php echo $character_address; ?>" readonly>
</div>
<div class="form-group">
<label for="warrant_details">Details</label>
<textarea type="text" class="form-control p-input" id="warrant_details" name="warrant_details" required></textarea>
</div>
<div class="form-group">
<label for="warrant_requestingunit">Requesting Unit</label>
<input type="text" class="form-control p-input" id="warrant_requestingunit" name="warrant_requestingunit" required>
</div>
<div class="form-group">
<label>Date & Time</label>
<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
</div>
<button type="submit" name="add_warrant_btn" class="btn btn-outline-info">Add</button>
</form>
</div>
</div>
</div>
</div>

<!-- FOOTER -->
<?php include "../includes/footer.inc.php"; ?>
</div>
</div>
</div>
</div>
<script>
function deleteWarrant(id) {
  $.ajax({
    type : "POST",
    url  : "../actions/department_functions.php",
    data : { deletewarrant : id },
    success: function(res){  
          // success
          $('#warrantsuccess').html(res); 
          location.reload();
        }
  });
}
</script>
<!-- JS -->
<script src="../assets/vendors/js/vendor.bundle.base.js"></script>
<script src="../assets/vendors/chart.js/Chart.min.js"></script>
<script src="../assets/vendors/progressbar.js/progressbar.min.js"></script>
<script src="../assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
<script src="../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<script src="../assets/js/off-canvas.js"></script>
<script src="../assets/js/hoverable-collapse.js"></script>
<script src="../assets/js/misc.js"></script>
<script src="../assets/js/settings.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
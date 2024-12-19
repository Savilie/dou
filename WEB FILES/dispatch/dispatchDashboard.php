<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
checkBan();
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/dispatch/dispatchDashboard.php";
$_SESSION['plateq'] = "";
$_SESSION['nameq'] = "";
$_SESSION['weaponq'] = "";

if ($_SESSION['dispatchperms'] != 1)
{
	header('Location: ../index.php?notAuthorisedDepartment');
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE discordid='$user_discordid'");

$setdept = $pdo->query("UPDATE users SET currdept='DISPATCH' WHERE discordid='$user_discordid'");

foreach ($result as $row)
{
	$dispatch_identifier = $row['identifier'];
	$dispatch_currstatus = $row['currstatus'];
	$dispatch_currpanic = $row['currpanic'];
	$dispatch_currdivision = $row['currdivision'];
	$dispatch_notepad = $row['notepad'];
	$showsupervisor = $row['showsupervisor'];
	$identifier_check = $row['identifier_check'];
}

// CHECK FOR SIGNAL 100
$signalcheck = $pdo->query("SELECT * FROM users WHERE currsignal='1'");

// GET ALL UNITS FOR MODAL
$activeunits = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7' AND currdept!='None' ORDER BY identifier ASC");
$activeunits3 = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7' AND currdept!='None' ORDER BY identifier ASC");
$activeunits5 = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7' AND currdept!='None' ORDER BY identifier ASC");

// ACTION NOTIFICATIONS
if(isset($_GET['notActive']))
{
	$actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You cannot do this as you are currently 10-7!</div>';
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
	<title><?php echo SERVER_SHORT_NAME; ?> CAD | Dispatch</title>
	<!-- CSS -->
	<link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
	<link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
	<link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../assets/vendors/quill/quill.snow.css">
	<!-- FAVICON -->
	<link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
</head>
<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/timer.js"></script>
<script src="../assets/js/sweetalert.js"></script>
<body id="modalCheck">
	<div class="container-scroller">
		<div class="horizontal-menu">
			<!-- HEADER -->
			<?php include "../includes/header.inc.php"; ?>
			<!-- NAVBAR -->
			<?php include "../includes/navbar.inc.php"; ?>
		</div>

		<?php
		if (CALLPANEL911 == 1)
		{
			?>
			<div id="panel" class="panel">
				<a style="text-decoration: none; color: white; font-size: 1.5em;" href="#"><span>&times;</span></a>
				<h4 class="text-center"><u>New 911 Calls</u></h4>
				<br>
				<div id="get911"></div>
			</div>
			<?php
		}
		?>

		<div class="container-fluid page-body-wrapper">
			<div class="main-panel">
				<div class="content-wrapper">
					<!-- ACTION DISPLAY -->
					<?php if($actionMessage){echo $actionMessage;} ?>
					<div class="row">
						<script>
							fetch('../actions/getzulu.php').then(function (resp) {
								resp.text().then(function (text) {
									$('#getzulu').html(text);
								});
							});
							var auto_refresh = setInterval( function () {
								fetch('../actions/getzulu.php').then(function (resp) {
									resp.text().then(function (text) {
										$('#getzulu').html(text);
									});
								});
							}, 1000);
							$('#get911').load('get911.php');
							var auto_refresh = setInterval( function () {
								$('#get911').load('get911.php');
							}, 5000);
						</script>
						<div class="col-xl-9">
							<div class="card">
								<div class="row">
									<div style="font-family: 'Orbitron', sans-serif; font-size: 30px; z-index: 2; position: absolute; padding: 30px 0px 0px 50px;" class="text-muted" id="getzulu"></div>
									<div class="col-md-12">
										<div class="card-body text-center" style="padding-top: 55px; padding-bottom: 66px;">
											<h3 class="mb-2 text-center">Dispatch Control Panel</h3><br>
											<h5 class="text-center">Identifier: <span class="text-muted"><?php echo $dispatch_identifier; ?> <?php if ($_SESSION['supervisor'] == 1 & $showsupervisor == 1) {echo "(Supervisor)";} ?></span></h5><br>
											<h5 class="text-center">Status: <span class="text-muted" id="changestatus"><?php echo $dispatch_currstatus; ?></span></h5>
											<div id="pingsuccess"></div>
											<div id="getpanic">
												<script type="text/javascript">
													$('#getpanic').load('../actions/getpanic.php');
													var auto_refresh = setInterval( function () {
														$('#getpanic').load('../actions/getpanic.php');
													}, 5000);
												</script>
											</div>
											<div id="getsignal">
												<script type="text/javascript">
													$('#getsignal').load('../actions/getsignal.php');
													var auto_refresh = setInterval( function () {
														$('#getsignal').load('../actions/getsignal.php');
													// KILLING 2 BIRDS WITH 1 STONE
													document.getElementById("bolosuccess").innerHTML = '';
													document.getElementById("pingsuccess").innerHTML = '';
												}, 8000);
											</script>			
										</div>
										<br>
										<button type="button" onclick="changeStatus('10-8')" class="btn btn-outline-success btn-rounded btn-fw p-3 m-2">10-8 | In Service</button>
										<button type="button" onclick="changeStatus('10-7')" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">10-7 | Out of Service</button>
										<?php
										if (sizeof($signalcheck->fetchAll()) == null)
										{
											?>
											<a href="../actions/department_functions.php?activatesignal=1" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Activate Signal 100</a>
											<?php
										} else {
											echo '<a href="../actions/department_functions.php?disablesignal" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Disable Signal 100</a>';
											echo '<a href="../actions/department_functions.php?playtone=1" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Play Signal 100 Tone</a>';
										}
										?>
										<p data-toggle="modal" data-target="#alertModal" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Fire Alert Panel</p>
										<br>
										<p data-toggle="modal" data-target="#createBolo" class="btn btn-outline-info btn-rounded btn-fw p-3 m-2">Create Bolo</p>
										<p data-toggle="modal" data-target="#createCall" class="btn btn-outline-info btn-rounded btn-fw p-3 m-2">Create Call</p>
										<p data-toggle="modal" data-target="#changeUnitIdentifier" class="btn btn-outline-info btn-rounded btn-fw p-3 m-2">Change Unit Identifier</p>
										<p data-toggle="modal" data-target="#markAll10-7" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Mark All 10-7</p>
										<br>
										<p data-toggle="modal" data-target="#Code10" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">10 Codes</p>
										<a href="../warrants/warrantDashboard.php" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Warrants Dashboard</a>
										<?php
										if (CALLPANEL911 == 1)
										{
											?>
											<a href="#panel" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">911 Calls Panel</a>
											<?php
										}

										if (PENAL_CODE_LINK != "#")
										{
											?>
											<a href="<?php echo PENAL_CODE_LINK; ?>" target="_blank" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Penal Code</a>
											<?php 
										}

										if (LIVEMAP_LINK != "#")
										{
											?>
											<a href="<?php echo LIVEMAP_LINK; ?>" target="_blank" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Live Map</a>
											<?php
										}
										?>
										<p data-toggle="modal" data-target="#noteModal" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Notepad</p>
										<button class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2" type="button" data-toggle="collapse" data-target="#showstopwatch" aria-expanded="false" aria-controls="collapseExample">Stopwatch / Timer</button>
										<div class="collapse" id="showstopwatch">
											<div class="mt-5">
												<div class="row">
													<div class="col-md-3"></div>
													<div class="col-md-3">
														<h6>Stopwatch</h6>
														<div class="container">
															<div style="margin: 0 auto; display: inline-block; overflow: hidden; padding-right: 10em; padding-bottom: 3em;">
																<div style="font-family: 'Orbitron', sans-serif; font-size: 30px; position: absolute;" class="text-muted" id="timerLabel">
																	00:00:00
																</div>
															</div>
															<br>
															<input type="button" value="START" class="btn btn-outline-success" onclick="start()" id="startBtn">
															<span class="p-2"></span>
															<input type="button" value="STOP" class="btn btn-outline-danger" onclick="stop()">
															<span class="p-2"></span>
															<input type="button" value="RESET" class="btn btn-outline-warning" onclick="reset()">
														</div>
													</div>
													<div class="col-md-3">
														<h6>Timer</h6>
														<div class="container">
															<div style="margin: 0 auto; display: inline-block; overflow: hidden; padding-right: 5em; padding-bottom: 3em;">
																<div style="font-family: 'Orbitron', sans-serif; font-size: 30px; position: absolute;" class="text-muted" id="timerLabel2">
																	0:00
																</div>
															</div>
															<div class="row">
																<div class="col-md-6">
																	<input type="number" style="text-align: center;" class="form-control p-input" name="timer2minutes" id="timer2minutes" placeholder="Minutes" value="1" required>
																</div>
																<div class="col-md-6">
																	<input type="number" style="text-align: center;" class="form-control p-input" name="timer2seconds" id="timer2seconds" value="00" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" placeholder="Seconds" required>
																</div>
															</div>
															<br>
															<input type="button" value="START" class="btn btn-outline-success" onclick="start2()" id="startBtn2">
															<span class="p-2"></span>
															<input type="button" value="RESET" class="btn btn-outline-warning" onclick="reset2()">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-3 text-center">Livechat</h3><br>
								<link rel="stylesheet" href="../assets/css/livechat.css">
								<div id="wrapper">
									<div id="chatbox" style="margin-left: 20px; height: 187px;">
										<?php
										if(file_exists("../actions/log.html") && filesize("../actions/log.html") > 0){
											$contents = file_get_contents("../actions/log.html");          
											echo $contents;
										}
										?>
									</div>
									<form action="">
										<input placeholder="Start typing..." name="usermsg" class="livechat-input p-input" type="text" id="usermsg" style="width: 20em;" />
										<input name="submitmsg" type="submit" id="submitmsg" value="Send" class="btn btn-outline-info"/>
										<br><br>
										<a href="../actions/department_functions.php?clearchat" class="btn btn-outline-danger btn-fw p-2 m-3">Clear Chat</a><br>
									</form>
								</div>
								<script type="text/javascript">
									$(document).ready(function () {
										$("#submitmsg").click(function () {
											var clientmsg = $("#usermsg").val();
											$.post("../actions/sendchat.php", { text: clientmsg });
											$("#usermsg").val("");
											return false;
										});

										function loadLog() {
											var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;
											$.ajax({
												url: "../actions/log.html",
												cache: false,
												success: function (html) {
													$("#chatbox").html(html);

		                                      var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
		                                      if(newscrollHeight > oldscrollHeight){
		                                      	$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
		                                      }   
		                                  }
		                              });
										}
										setInterval (loadLog, 1000);
									});
								</script>
							</div>
						</div>
					</div>
				</div>
				<br>
				<br>
				<div class="row">
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Active LEO Units</h3><br>
								<span id="leostatus"></span>
								<div id="getleounits">
									<script type="text/javascript">
										var hasFocus = false;
										$('#getleounits').load('getleounits.php');
										var auto_refresh = setInterval( function () {
											hasFocus = $('select').is(':focus');
											if (hasFocus != true)
											{
												$('#getleounits').load('getleounits.php');
											}
										}, 2500);
									</script>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Active Calls</h3><br>
								<div id="getcalls">
									<script type="text/javascript">
										var modalChecking;
										$('#getcalls').load('getcalls.php');
										var auto_refresh = setInterval( function () {
											modalChecking = $('#modalCheck').hasClass('modal-open');
											if (modalChecking == false)
											{
												$('#getcalls').load('getcalls.php');
											}
										}, 2500);
									</script>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<br>
				<div class="row">
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Active EMS/FIRE Units</h3><br>
								<table class="table text-center" id="getfeunits">
									<script type="text/javascript">
										$('#getfeunits').load('getfeunits.php');
										var auto_refresh = setInterval( function () {
											if (hasFocus != true) 
											{
												$('#getfeunits').load('getfeunits.php');
											}
										}, 2500);
									</script>
								</table>
							</div>
						</div>
					</div>
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Active Bolos</h3><span id="bolosuccess"></span><br>
								<div class="table-responsive" id="vehiclesection">
									<table class="table text-center" id="getbolo">
										<script type="text/javascript">
											$('#getbolo').load(`../actions/getbolo.php`);
											var auto_refresh = setInterval( function () {
												$('#getbolo').load(`../actions/getbolo.php`);
											}, 3000);
										</script>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<br>
				<div class="row">
					<div class="col-xl-4">
						<div class="card" id="searched">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-1 text-center">Name Check</h3><br>
								<form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
									<div class="form-group">
										<input type="text" class="form-control p-input" placeholder="Search Name or SSN" onkeyup="showResult(this.value, 'NAME')" style="color: white;">
									</div>
									<div id="namesearch"></div>
								</form>
							</div>
						</div>
						<br>
						<?php
						if (isset($_GET['nameq']))
						{
							$nameq = htmlspecialchars($_GET['nameq']);
							$_SESSION['nameq'] = $nameq;
							$character = $pdo->query("SELECT * FROM characters WHERE ID='$nameq'");

							foreach ($character as $row)
							{
								$character_id = $row['ID'];
								$character_name = $row['name'];
								$character_dob = $row['dob'];
								$character_haircolor = $row['haircolor'];
								$character_address = $row['address'];
								$character_gender = $row['gender'];
								$character_number = $row['number'];
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

								$character_discordid = $row['discordid'];

								if (checkOnline('discord:'.$character_discordid) == true) {
									$player_ingame = true;
								}
							}

							$regvehicle = $pdo->query("SELECT * FROM vehicles WHERE charid='$character_id' ORDER BY ID DESC");

							$warnings = $pdo->query("SELECT * FROM warnings WHERE civid='$character_id' ORDER BY ID DESC");

							$citations = $pdo->query("SELECT * FROM citations WHERE civid='$character_id' ORDER BY ID DESC");

							$arrests = $pdo->query("SELECT * FROM arrests WHERE civid='$character_id' ORDER BY ID DESC");

							$warrants = $pdo->query("SELECT * FROM warrants WHERE civid='$character_id' ORDER BY ID DESC");
							?>
							<div class="card">
								<div class="card-body">
									<div style="font-size: 20px; text-align: right;">
										<a style="text-decoration: none; color: white;" href="dispatchDashboard.php"><span>&times;</span></a>
									</div><br>
									<div class="text-center">
										<img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 20%; <?php if ($player_ingame == true) {echo "border: 3px solid #3ba55d;";} else {echo "border: 3px solid #747f8d;";} ?>">
									</div>
									<h5>Name: <span class="text-muted"><?php echo $character_name; ?></span></h5>
									<h5>DOB: <span class="text-muted"><?php echo $character_dob; ?></span></h5>
									<h5>SSN: <span class="text-muted"><?php echo $character_ssn; ?></span></h5>
									<h5>Hair Color: <span class="text-muted"><?php echo $character_haircolor; ?></span></h5>
									<h5>Address: <span class="text-muted"><?php echo $character_address; ?></span></h5>
									<h5>Gender: <span class="text-muted"><?php echo $character_gender; ?></span></h5>
									<h5>Race: <span class="text-muted"><?php echo $character_race; ?></span></h5>
									<h5>Build: <span class="text-muted"><?php echo $character_build; ?></span></h5>
									<h5>Occupation: <span class="text-muted"><?php echo $character_occupation; ?></span></h5>
									<?php
									if (CIV_PHONE_NUMBERS == true) { echo '<h5>Phone Number: <span class="text-muted">'.$character_number.'</span></h5>'; }
									?>
									<h5 style="color: <?php echo $deadcolor; ?>;"><?php echo $deadstatus; ?></h5>
									<br>
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
									<br>
									<h5>Licenses:</h5>
									<span class="text-muted">Drivers: <span style="color: <?php echo  getColor($drivers); ?>;"><?php echo $drivers; ?></span></span><br>
									<span class="text-muted">Weapons: <span style="color: <?php echo  getColor($weapons); ?>;"><?php echo $weapons; ?></span></span><br>
									<span class="text-muted">Hunting: <span style="color: <?php echo  getColor($hunting); ?>;"><?php echo $hunting; ?></span></span><br>
									<span class="text-muted">Fishing: <span style="color: <?php echo  getColor($fishing); ?>;"><?php echo $fishing; ?></span></span><br>
									<span class="text-muted">Commercial: <span style="color: <?php echo  getColor($commercial); ?>;"><?php echo $commercial; ?></span></span><br>
									<span class="text-muted">Boating: <span style="color: <?php echo  getColor($boating); ?>;"><?php echo $boating; ?></span></span><br>
									<span class="text-muted">Aviation: <span style="color: <?php echo  getColor($aviation); ?>;"><?php echo $aviation; ?></span></span><br>
									<br>
									<h5>Registered Vehicles Plate:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php
									foreach ($regvehicle as $row)
									{
										$regvehicle_plate = $row['plate'];
										?>
										<span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>;" href="?nameq=<?php echo $nameq; ?>&plateq=<?php echo $row['ID']; ?>#searched"><?php echo $regvehicle_plate; ?></a></span><br>
										<?php
									}

									if ($regvehicle_plate == "")
									{
										echo '<span class="text-muted">None</span><br>';
									}
									?>
									<span class="text-muted">=====================================</span><br>
									<br>
									<h5>Warnings:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php
									foreach ($warnings as $row)
									{
										$warnings_unit_identifier = $row['unitidentifier'];
										$warnings_date = $row['date'];
										$warnings_time = $row['time'];
										$warnings_offences = $row['offences'];
										$warnings_note = $row['note'];
										?>
										<span class="text-muted"><b>Offence:</b> <?php echo $warnings_offences; ?></span><br>
										<span class="text-muted"><b>Note:</b> <?php echo $warnings_note; ?></span><br>
										<span class="text-muted"><b>Date & Time:</b> <?php echo $warnings_date . " | " . $warnings_time; ?></span><br>
										<span class="text-muted"><b>Signing Unit:</b> <?php echo $warnings_unit_identifier; ?></span><br>
										<span class="text-muted">=====================================</span><br>
										<?php
									}

									if ($warnings_unit_identifier == "")
									{
										echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
									}
									?>
									<br>
									<h5>Citations:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php
									foreach ($citations as $row)
									{
										$citation_unit_identifier = $row['unitidentifier'];
										$citation_date = $row['date'];
										$citation_time = $row['time'];
										$citation_offences = $row['offences'];
										$citation_fine = $row['fine'];
										$citation_note = $row['note'];
										?>
										<span class="text-muted"><b>Offence:</b> <?php echo $citation_offences; ?></span><br>
										<span class="text-muted"><b>Fine:</b> $<?php echo $citation_fine; ?></span><br>
										<span class="text-muted"><b>Note:</b> <?php echo $citation_note; ?></span><br>
										<span class="text-muted"><b>Date & Time:</b> <?php echo $citation_date . " | " . $citation_time; ?></span><br>
										<span class="text-muted"><b>Signing Unit:</b> <?php echo $citation_unit_identifier; ?></span><br>
										<span class="text-muted">=====================================</span><br>
										<?php
									}

									if ($citation_unit_identifier == "")
									{
										echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
									}

									?>
									<br>
									<h5>Arrests:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php
									foreach ($arrests as $row)
									{
										$arrests_unit_identifier = $row['unitidentifier'];
										$arrests_date = $row['date'];
										$arrests_time = $row['time'];
										$arrests_type = $row['arresttype'];
										$arrests_reason = $row['reason'];
										$arrests_fine = $row['fine'];
										$arrests_jailtime = $row['jailtime'];
										$arrests_note = $row['note'];
										?>
										<span class="text-muted"><b>Type:</b> <?php echo $arrests_type; ?></span><br>
										<span class="text-muted"><b>Reason:</b> <?php echo $arrests_reason; ?></span><br>
										<span class="text-muted"><b>Fine:</b> $<?php echo $arrests_fine; ?></span><br>
										<span class="text-muted"><b>Jail Time:</b> <?php echo $arrests_jailtime; ?> Seconds</span><br>
										<span class="text-muted"><b>Note:</b> <?php echo $arrests_note; ?></span><br>
										<span class="text-muted"><b>Date & Time:</b> <?php echo $arrests_date . " | " . $arrests_time; ?></span><br>
										<span class="text-muted"><b>Signing Unit:</b> <?php echo $arrests_unit_identifier; ?></span><br>
										<span class="text-muted">=====================================</span><br>
										<?php
									}

									if ($arrests_unit_identifier == "")
									{
										echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
									}

									?>
									<br>
									<h5>Warrants:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php
									foreach ($warrants as $row)
									{
										$warrants_unit_identifier = $row['unitidentifier'];
										$warrants_date = $row['date'];
										$warrants_time = $row['time'];
										$warrants_details = $row['details'];
										?>
										<span class="text-muted"><b>Details:</b> <?php echo $warrants_details; ?></span><br>
										<span class="text-muted"><b>Date & Time:</b> <?php echo $warrants_date . " | " . $warrants_time; ?></span><br>
										<span class="text-muted"><b>Signing Unit:</b> <?php echo $warrants_unit_identifier; ?></span><br>
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
					<div class="col-xl-4">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Plate Check</h3><br>
								<form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
									<div class="form-group">
										<input type="text" class="form-control p-input" placeholder="Search Plate" onkeyup="showResult(this.value, 'PLATE')" style="color: white;">
									</div>
									<div id="platesearch"></div>
								</form>
							</div>
						</div>
						<br>
						<?php
						if (isset($_GET['plateq']))
						{
							$plateq = htmlspecialchars($_GET['plateq']);
							$_SESSION['plateq'] = $plateq;
							$vehicles = $pdo->query("SELECT * FROM vehicles WHERE ID='$plateq'");

							foreach ($vehicles as $row)
							{
								$vehicle_id = $row['ID'];
								$vehicle_charid = $row['charid'];
								$vehicle_plate = $row['plate'];
								$vehicle_makemodel = $row['makemodel'];
								$vehicle_color = $row['color'];
								$vehicle_insurance = $row['insurance'];
								$vehicle_regstate = $row['regstate'];
								$vehicle_flags = $row['flags'];
								$vehicle_vin = $row['vin'];

								$owner = $pdo->query("SELECT * FROM characters WHERE ID='$vehicle_charid'");

								foreach ($owner as $row2)
								{
									$owner_name = $row2['name'];
								}

							}
							?>
							<div class="card">
								<div class="card-body">
									<div style="font-size: 20px; text-align: right;">
										<a style="text-decoration: none; color: white; right: 0;" href="dispatchDashboard.php"><span>&times;</span></a><br><br>
									</div>
									<h5>Registered Owner: <span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>;" href="?nameq=<?php echo $vehicle_charid; ?>&plateq=<?php echo $plateq; ?>#searched"><?php echo $owner_name; ?></a></span></h5>
									<h5>Plate: <span class="text-muted"><?php echo $vehicle_plate; ?></span></h5>
									<h5>Make & Model: <span class="text-muted"><?php echo $vehicle_makemodel; ?></span></h5>
									<h5>Color: <span class="text-muted"><?php echo $vehicle_color; ?></span></h5>
									<h5>Insurance: <span style="color: <?php echo  getColor($vehicle_insurance); ?>;"><?php echo $vehicle_insurance; ?></span></h5>
									<h5>Registered State: <span class="text-muted"><?php echo $vehicle_regstate; ?></span></h5>
									<h5>Flags: <span class="<?php if($vehicle_flags=="None"){echo"text-muted";}else{echo"text-danger";} ?>"><?php echo $vehicle_flags; ?></span></h5>
									<?php if ($vehicle_vin != null) {?><h5>VIN: <span class="text-muted"><?php echo $vehicle_vin; ?></span></h5><?php }?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<div class="col-xl-4">
						<div class="card">
							<div class="card-body text-center">
								<h3 class="mt-2 mb-2 text-center">Weapon Check</h3><br>
								<form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
									<div class="form-group">
										<input type="text" class="form-control p-input" placeholder="Search Name" onkeyup="showResult(this.value, 'WEAPON')" style="color: white;">
									</div>
									<div id="weaponsearch"></div>
								</form>
							</div>
						</div>
						<br>
						<?php
						if (isset($_GET['weaponq']))
						{
							$weaponq = htmlspecialchars($_GET['weaponq']);
							$_SESSION['weaponq'] = $weaponq;
							$weapons = $pdo->query("SELECT * FROM weapons WHERE charid='$weaponq'");
							?>
							<div class="card">
								<div class="card-body">
									<div style="font-size: 20px; text-align: right;">
										<a style="text-decoration: none; color: white; right: 0;" href="dispatchDashboard.php"><span>&times;</span></a><br><br>
									</div>
									<h5>Weapons:</h5>
									<span class="text-muted">=====================================</span><br>
									<?php 
									foreach ($weapons as $row)
									{
										$weapon_type = $row['type'];
										$weapon_name = $row['name'];
										$weapon_serialnumber = $row['serialnumber'];

										?>
										<h5>Type: <span class="text-muted"><?php echo $weapon_type; ?></span></h5>
										<h5>Name: <span class="text-muted"><?php echo $weapon_name; ?></span></h5>
										<h5>Serial Number: <span class="text-muted"><?php echo $weapon_serialnumber; ?></span></h5>
										<span class="text-muted">=====================================</span><br>
										<?php
									}

									if ($weapon_serialnumber == "")
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
				<!-- FOOTER -->
				<?php include "../includes/footer.inc.php"; ?>
			</div>
		</div>
	</div>
</div>

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="noteModal">Your Notepad</h5><span class="text-success pl-3" id="notesuccess"></span>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="forms-sample" action="../actions/department_functions.php" method="POST">
					<div class="form-group">
						<div class="row grid-margin">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body">
										<div style="color: white !important;" id="quillExample1" class="quill-container">
											<?php echo $dispatch_notepad; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="text" id="note_content" name="note_content" hidden>
				</form>
				<div class="pt-2">
					<button type="submit" onclick="saveNote()" class="btn btn-outline-info btn-fw">Save & Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Change Unit Identifier Modal -->
<div class="modal fade" id="changeUnitIdentifier" tabindex="-1" role="dialog" aria-labelledby="changeUnitIdentifier" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="changeUnitIdentifier">Change Unit Identifier</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="forms-sample" action="../actions/department_functions.php" method="POST">
					<div class="form-group">
						<label for="change_unit">Select Unit</label>
						<select class="form-control p-input" name="change_unit" id="change_unit" onclick="document.getElementById('unit_identifier').value = this.options[this.selectedIndex].text" style="color: white;" required>
							<?php
							foreach ($activeunits as $row)
							{
								$activeunit_identifier = $row['identifier'];
								$activeunit_ID = $row['ID'];
								?>
								<option value="<?php echo $activeunit_ID; ?>"><?php echo $activeunit_identifier; ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label for="unit_identifier">Identifier</label>
						<input type="text" class="form-control p-input" name="unit_identifier" id="unit_identifier" required>
					</div>
					<div class="form-group">
						<label for="unit_department">Department</label>
						<select class="form-control p-input" id="unit_department" name="unit_department" style="color: white;" required>
							<option value="No Change">No Change</option>
						<?php
		                foreach ($_SESSION['departments_identifiers'] as $identifier)
		                {
		                  if ($identifier != NULL) {
		                    echo '<option value="'.$identifier.'">'.$identifier.'</option>';
		                  }
		                }
		                ?>
		                	<option value="None">None</option>
		                </select>
					</div>
					<button type="submit" name="update_unitsidentifier_btn" class="btn btn-outline-info">Update</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModal" aria-hidden="true">
  <div class="modal-dialog" role="document" style="margin-top: 300px; max-width: 700px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModal">Select Alert Tone</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center pb-3" id="alertsuccess"></div>
        <div style="text-align: center;">
          <button type="submit" onclick="playAlert(1)" class="btn btn-outline-info m-2">All Call</button>
          <button type="submit" onclick="playAlert(2)" class="btn btn-outline-info m-2">LS Stations</button>
          <button type="submit" onclick="playAlert(3)" class="btn btn-outline-info m-2">BC Stations</button>
          <button type="submit" onclick="playAlert(4)" class="btn btn-outline-info m-2" title="Use After Station Tone">EMS</button>
          <button type="submit" onclick="playAlert(5)" class="btn btn-outline-info m-2">Fire Marshal</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Bolo Modal -->
<div class="modal fade" id="createBolo" tabindex="-1" role="dialog" aria-labelledby="createBolo" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createBolo">Create Bolo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="forms-sample" action="../actions/department_functions.php" method="POST">
					<div class="form-group">
						<label for="bolo_type">Bolo Type</label>
						<select onchange="checkVehicle(this);" class="form-control p-input" id="bolo_type" name="bolo_type" style="color: white;" required>
							<option value="Person">Person</option>
							<option value="Vehicle">Vehicle</option>
							<option value="Other">Other</option>
						</select>
					</div>
					<div class="form-group" id="ifVeh" style="display: none;">
						<label for="bolo_plate">Plate</label>
						<input type="text" class="form-control p-input" id="bolo_plate" name="bolo_plate">
					</div>
					<div class="form-group">
						<label for="bolo_details">Details</label>
						<textarea type="text" class="form-control p-input" id="bolo_details" name="bolo_details" required></textarea>
					</div>
					<div class="form-group">
						<label>Date & Time</label>
						<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
					</div>
					<button type="submit" name="add_bolo_btn" class="btn btn-outline-info">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Mark 10-7 Modal -->
<div class="modal fade" id="markAll10-7" tabindex="-1" role="dialog" aria-labelledby="markAll10-7" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="markAll10-7">Mark All 10-7</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="forms-sample" action="../actions/department_functions.php" method="POST">
					<p>Are you sure you want to mark all units 10-7?</p>
					<button type="submit" name="mark10-7_btn" class="btn btn-outline-danger">Yes</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- 10 Codes Modal -->
<div class="modal fade" id="Code10" tabindex="-1" role="dialog" aria-labelledby="Code10" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="margin-top: 50px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="Code10">10 Codes</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
						<?php
						foreach ($CODES10 as $code => $color) {
							echo '<div style="color: '.$color.'">'.$code.'</div>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


	<!-- Identifier Check Modal -->
	<div class="modal fade" id="identifierCheckModal" tabindex="-1" role="dialog" aria-labelledby="identifierCheckModal" aria-hidden="true" style="background: rgba(5, 5, 5, .9);">
	  <div class="modal-dialog" style="margin-top: 300px; max-width: 800px;" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	      	<h4 style="text-align: center;">PLEASE MAKE SURE THE FOLLOWING ARE CORRECT!</h4>
	      	<br>
	        <form class="forms-sample row" action="<?php echo BASE_URL; ?>/actions/civ_functions.php" method="POST">
	            <div class="form-group col-md-6">
	                <label for="user_identifier">Current Identifier</label>
	                <input type="text" class="form-control" id="user_identifier" name="user_identifier" placeholder="Enter Identifier" value="<?php echo $user_identifier; ?>">
	            </div>
	            <div class="form-group col-md-6">
	              <label for="user_department">Current Department</label>
	              <select class="form-control p-input" id="user_department" name="user_department" style="color: white;" required>
	                <?php
	                if (!empty($user_department)) {
	                  echo '<option value="'.$user_department.'">'.$user_department.'</option>';
	                }
	                foreach ($_SESSION['departments_identifiers'] as $identifier)
	                {
	                  if ($identifier != NULL) {
	                    echo '<option value="'.$identifier.'">'.$identifier.'</option>';
	                  }
	                }
	                ?>
	                <option value="None">None</option>
	              </select>
	            </div>
	            <div style="text-align: center; margin: auto;">
	                <button type="submit" name="editprofile_btn" class="btn btn-lg btn-outline-success">All Good!</button>
	            </div>
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

	<?php
		if ($identifier_check != "3" && IDENTIFIER_CHECK == true) {
	?>
	<script>
		var element = document.getElementById("identifierCheckModal");
  		element.classList.add("show");
  		element.style.display = "block";
	</script>
	<?php
		}

		$checkdone = $pdo->query("UPDATE users SET identifier_check='3' WHERE discordid='$user_discordid'");
	?>


<!-- Create Call Modal -->
<div class="modal fade" id="createCall" tabindex="-1" role="dialog" aria-labelledby="createCall" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createCall">Create Call</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="forms-sample" action="../actions/department_functions.php" method="POST">
					<div class="form-group">
						<label for="call_type">Call Type</label>
						<select onchange="checkCall(this);" class="form-control p-input" id="call_type" name="call_type" style="color: white;" required>
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
						</select>
					</div>
					<div class="form-group" id="ifOther" style="display: none;">
						<input type="text" placeholder="Enter other call type..." class="form-control p-input" id="call_type_other" name="call_type_other">
					</div>
					<div class="form-group">
						<label for="call_location">Location</label>
						<input type="text" class="form-control p-input" id="call_location" name="call_location" required>
					</div>
					<div class="form-group">
						<label for="call_postal">Postal</label>
						<input type="text" class="form-control p-input" id="call_postal" name="call_postal" required>
					</div>
					<div class="form-group">
						<label for="call_narrative">Narrative</label>
						<textarea type="text" class="form-control p-input" id="call_narrative" name="call_narrative" required></textarea>
					</div>
					<div class="form-group">
						<label for="call_initiatingunit">Initiating Unit</label>
						<select class="form-control p-input" id="call_initiatingunit" name="call_initiatingunit" style="color: white;" required>
							<option value="None">None</option>
							<?php
							foreach ($activeunits5 as $row)
							{
								$activeunit_identifier = $row['identifier'];
								$activeunit_discordid = $row['discordid'];
								?>
								<option value="<?php echo $activeunit_discordid; ?>"><?php echo $activeunit_identifier; ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label>Date & Time</label>
						<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
					</div>
					<button type="submit" name="add_call_btn" class="btn btn-outline-info">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script>

	function showResult(str, type) {
		if (str.length==0) {
			if (type == 'NAME')
			{
				document.getElementById("namesearch").innerHTML="";
			}
			if (type == 'PLATE')
			{
				document.getElementById("platesearch").innerHTML="";
			}
			if (type == 'WEAPON')
			{
				document.getElementById("weaponsearch").innerHTML="";
			}
			return;
		}
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {  // code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (this.readyState == 4 && this.status == 200) {
				if (type == 'NAME')
				{
					document.getElementById("namesearch").innerHTML = this.responseText;
				}
				if (type == 'PLATE')
				{
					document.getElementById("platesearch").innerHTML = this.responseText;
				}
				if (type == 'WEAPON')
				{
					document.getElementById("weaponsearch").innerHTML = this.responseText;
				}
			}
		}

		if (type == 'NAME')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?nc=" + str, true);
			xmlhttp.send();
		}
		if (type == 'PLATE')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?pc=" + str, true);
			xmlhttp.send();
		}
		if (type == 'WEAPON')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?wc=" + str, true);
			xmlhttp.send();
		}

	}

	function saveNote () {
		var myEditor = document.querySelector('#quillExample1');
		var html = myEditor.children[0].innerHTML;
		document.getElementById("note_content").value = html;
		document.getElementById("notesuccess").innerHTML = "SUCCESS";

		$.ajax({
			type : "POST",
			url  : "../actions/department_functions.php",
			data : { note_content : document.getElementById("note_content").value },
			success: function(res){  
						// success
						$('#noteModal').modal('hide');
					}
		});
	}

	function changeStatus(status) {
		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { dispatchstatus : status },
				success: function(res){  
							// success
							$('#changestatus').html(res); 
						}
		});
	}

	function playAlert(type) {
		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { playfiretone : type },
				success: function(res){  
							// success
							$('#alertsuccess').html(res); 
							location.reload();
						}
		});
	}

	function deleteBolo(id) {
		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { boloid : id },
				success: function(res){  
							// success
							$('#bolosuccess').html(res); 
						}
		});
	}

	function deleteCall(id) {
		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { deletecallid : id },
				success: function(res){  
							// success
							$('#narrativesuccess').html(res); 
						}
		});
	}

	function updateCall(callid, narrativeid, typeid, locationid, postalid, otherid) {
		var narrativeedit = document.getElementById(narrativeid).value;
		var typeedit = document.getElementById(typeid).value;
		var locationedit = document.getElementById(locationid).value;
		var postaledit = document.getElementById(postalid).value;
		var otheredit = document.getElementById(otherid).value;

		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { narrative : narrativeedit, callid : callid, calltype : typeedit, location : locationedit, postal : postaledit, other : otheredit},
				success: function(res){  
							// success
							$('#narrativesuccess').html(res); 
						}
		});
	}

	function unitPing(id) {
		$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { unitping : id },
				success: function(res){  
							// success
							$('#pingsuccess').html(res); 
						}
		});
	}

	function checkVehicle(that) {
		if (that.value == "Vehicle") {
			document.getElementById("ifVeh").style.display = "block";
		} else {
			document.getElementById("ifVeh").style.display = "none";
		}
	}

	function checkCall(that) {
		if (that.value == "Other") {
			document.getElementById("ifOther").style.display = "block";
			document.getElementById("ifOther2").style.display = "block";
		} else {
			document.getElementById("ifOther").style.display = "none";
			document.getElementById("ifOther2").style.display = "none";
		}
	}

	var saveTime = localStorage.getItem("saveTimeKey");
	if (saveTime != "0")
	{
		if (saveTime != "1") {
			var element = document.getElementById("showstopwatch");
			element.classList.add("show");
			start();
		}
	}

	var saveSeconds = localStorage.getItem("saveTimeSeconds");
	if (saveSeconds != "0")
	{
		var element = document.getElementById("showstopwatch");
		element.classList.add("show");
		document.getElementById("startBtn2").disabled = true;
		restart2();
	}

	<?php 
	if (CALLPANEL911 == 1)
	{
		?>
		window.addEventListener('click', function(e){   
			if (document.getElementById('panel').contains(e.target)){
		    // Clicked in box
		} else{
			window.location.href = "#";
		}
	});
		<?php
	}
	?>

	var dutyTime = localStorage.getItem("dutyTimeMinutes");
	if (dutyTime != "0")
	{
		if (dutyTime != "1")
		{
			restart3();
		}
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
<script src="../assets/vendors/quill/quill.min.js"></script>
<script src="../assets/js/editorDemo.js"></script>
</body>
</html>
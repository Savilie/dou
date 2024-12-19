<?php
session_start();
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/actions/discord_functions.php");
checkBan();
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/index.php";

try{
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
  echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$resetstatus = $pdo->query("UPDATE users SET currstatus='10-7', currdivision='None', currdept='None', identifier_check='0' WHERE discordid='$user_discordid'");

// ACTION NOTIFICATIONS
if(isset($_GET['notCivilian']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You are not a civilian, contact staff for more information.</div>';
}

if(isset($_GET['notAuthorisedDepartment']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You are not authorised to this department, contact staff for more information.</div>';
}

// STATS
$characters = $pdo->query("SELECT * FROM characters");
$vehicles = $pdo->query("SELECT * FROM vehicles");
$weapons = $pdo->query("SELECT * FROM weapons");
$unitsonduty = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7'");
$warnings = $pdo->query("SELECT * FROM warnings");
$citations = $pdo->query("SELECT * FROM citations");
$arrests = $pdo->query("SELECT * FROM arrests");
$warrants = $pdo->query("SELECT * FROM warrants");

// YOUR STATS
$yourcharacters = $pdo->query("SELECT * FROM characters WHERE discordid='$user_discordid'");
$yourvehicles = $pdo->query("SELECT * FROM vehicles WHERE discordid='$user_discordid'");
$yourweapons = $pdo->query("SELECT * FROM weapons WHERE discordid='$user_discordid'");
$yourwarnings = $pdo->query("SELECT * FROM warnings WHERE unitdiscordid='$user_discordid'");
$yourcitations = $pdo->query("SELECT * FROM citations WHERE unitdiscordid='$user_discordid'");
$yourarrests = $pdo->query("SELECT * FROM arrests WHERE unitdiscordid='$user_discordid'");
$yourwarrants = $pdo->query("SELECT * FROM warrants WHERE unitdiscordid='$user_discordid'");
$yourmedical = $pdo->query("SELECT * FROM medicalrecords WHERE unitdiscordid='$user_discordid'");

// TOP STATS
$topwarningsunit = "None";
$topwarningsamount = "None";
$topcitationsunit = "None";
$topcitationsamount = "None";
$toparrestsunit = "None";
$toparrestsamount = "None";
$topwarrantsunit = "None";
$topwarrantsamount = "None";

$topwarnings = $pdo->query("SELECT unitidentifier, unitdiscordid, count(unitdiscordid) FROM warnings GROUP BY unitidentifier, unitdiscordid ORDER BY count(unitdiscordid) DESC LIMIT 1");
foreach ($topwarnings as $row)
{
  
  $topwarningsunit = $row['unitidentifier'];
  $topwarningsdiscordid = $row['unitdiscordid'];
  $topwarningsamount = $pdo->query("SELECT * FROM warnings WHERE unitdiscordid='$topwarningsdiscordid'");
  $topwarningsamount = sizeof($topwarningsamount->fetchAll());
}

$topcitations = $pdo->query("SELECT unitidentifier, unitdiscordid, count(unitdiscordid) FROM citations GROUP BY unitidentifier, unitdiscordid ORDER BY count(unitdiscordid) DESC LIMIT 1 ");
foreach ($topcitations as $row)
{
  $topcitationsunit = $row['unitidentifier'];
  $topcitationsdiscordid = $row['unitdiscordid'];
  $topcitationsamount = $pdo->query("SELECT * FROM citations WHERE unitdiscordid='$topcitationsdiscordid'");
  $topcitationsamount = sizeof($topcitationsamount->fetchAll());
}

$toparrests = $pdo->query("SELECT unitidentifier, unitdiscordid, count(unitdiscordid) FROM arrests GROUP BY unitidentifier, unitdiscordid ORDER BY count(unitdiscordid) DESC LIMIT 1 ");
foreach ($toparrests as $row)
{
  $toparrestsunit = $row['unitidentifier'];
  $toparrestsdiscordid = $row['unitdiscordid'];
  $toparrestsamount = $pdo->query("SELECT * FROM arrests WHERE unitdiscordid='$toparrestsdiscordid'");
  $toparrestsamount = sizeof($toparrestsamount->fetchAll());
}

$topwarrants = $pdo->query("SELECT unitidentifier, unitdiscordid, count(unitdiscordid) FROM warrants GROUP BY unitidentifier, unitdiscordid ORDER BY count(unitdiscordid) DESC LIMIT 1 ");
foreach ($topwarrants as $row)
{
  $topwarrantsunit = $row['unitidentifier'];
  $topwarrantsdiscordid = $row['unitdiscordid'];
  $topwarrantsamount = $pdo->query("SELECT * FROM warrants WHERE unitdiscordid='$topwarrantsdiscordid'");
  $topwarrantsamount = sizeof($topwarrantsamount->fetchAll());
}

// GALLERY
$gallery = $pdo->query("SELECT * FROM gallery");

?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo SERVER_SHORT_NAME; ?> CAD | Home</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">

    <!--OPEN GRAPH FOR DISCORD RICH PRESENCE-->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo BASE_URL; ?>" />
    <meta property="og:title" content="<?php echo SERVER_SHORT_NAME; ?>" />
    <meta property="og:description" content="CAD System by Hamz#0001">
    <meta name="theme-color" content="<?php echo LOGS_COLOR; ?>">

    <!-- FAVICON -->
    <link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
  </head>
  <body>
  <?php
  if (PRELOADER == 1)
  {
  ?>
  <div class="spinner-wrapper">
      <div class="square-box-loader">
        <div class="square-box-loader-container">
          <div class="square-box-loader-corner-top"></div>
          <div class="square-box-loader-corner-bottom"></div>
        </div>
        <div class="square-box-loader-square"></div>
      </div>      
  </div>
  <?php
  }
  ?>

    <div class="container-scroller">
      <div class="horizontal-menu">
        <!-- HEADER -->
        <?php include "includes/header.inc.php"; ?>
        <!-- NAVBAR -->
        <?php include "includes/navbar.inc.php"; ?>
      </div>

      <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
          <div class="content-wrapper">
        <!-- ACTION DISPLAY -->
        <?php if($actionMessage){echo $actionMessage;} ?>
            <div class="row">
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($characters->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Total Characters</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($unitsonduty->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Units On Duty</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($vehicles->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Registered Vehicles</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($weapons->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Registered Weapons</h6>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($warnings->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Total Warnings</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($citations->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Total Citations</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($arrests->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Total Arrests</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3 class="mb-3 text-center"><?php echo sizeof($warrants->fetchAll()) ?></h3>
                    <h6 class="text-muted font-weight-normal text-center">Total Warrants</h6>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-muted font-weight-bold text-center">Your Statistics</h4>
                    <div class="table-responsive mt-3">
                        <table class="table text-center">
                          <thead>
                            <tr>
                              <th>Type</th>
                              <th>#</th>
                              <th></th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody style="color: white;">
                            <?php
                              if ($_SESSION['civilianperms'] == 1)
                              {
                            ?>
                            <tr>
                                <td>Total Characters</td>
                                <td><?php echo sizeof($yourcharacters->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Total Vehicles</td>
                                <td><?php echo sizeof($yourvehicles->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Total Weapons</td>
                                <td><?php echo sizeof($yourweapons->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                              }

                              if ($_SESSION['leoperms'] == 1)
                              {
                            ?>
                            <tr>
                                <td>Warnings Given</td>
                                <td><?php echo sizeof($yourwarnings->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Citations Given</td>
                                <td><?php echo sizeof($yourcitations->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Arrests Given</td>
                                <td><?php echo sizeof($yourarrests->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Warrants Given</td>
                                <td><?php echo sizeof($yourwarrants->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                              }

                              if ($_SESSION['fireemsperms'] == 1)
                              {
                            ?>
                            <tr>
                                <td>Medical Records Written</td>
                                <td><?php echo sizeof($yourmedical->fetchAll()) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                              }
                            ?>
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-muted font-weight-bold text-center">Leaderboard</h4>
                    <div class="table-responsive mt-3">
                        <table class="table text-center">
                          <thead>
                            <tr>
                              <th>Type</th>
                              <th>Unit</th>
                              <th>#</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody style="color: white;">
                            <tr>
                                <td>Most Warnings Issued</td>
                                <td><?php echo $topwarningsunit; ?></td>
                                <td><?php echo $topwarningsamount; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Most Citations Issued</td>
                                <td><?php echo $topcitationsunit; ?></td>
                                <td><?php echo $topcitationsamount; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Most Arrests Issued</td>
                                <td><?php echo $toparrestsunit; ?></td>
                                <td><?php echo $toparrestsamount; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Most Warrants Issued</td>
                                <td><?php echo $topwarrantsunit; ?></td>
                                <td><?php echo $topwarrantsamount; ?></td>
                                <td></td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            if (GALLERY == 1)
            {
            ?>
            <br>
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-muted font-weight-bold text-center">Gallery</h4>
                    <div class="owl-carousel owl-theme loop mt-4">
                      <?php
                      foreach ($gallery as $row)
                      {
                      ?>
                      <div class="item pr-2">
                        <img src="<?php echo $row['link']; ?>" />
                      </div>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            }
            ?>
          <!-- FOOTER -->
          <?php include "includes/footer.inc.php"; ?>
          </div>

          <br><br>
          </div>
        </div>
      </div>
    </div>
    <!-- JS -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script>
      <?php
      if (PRELOADER == 1)
      {
      ?>
      $(document).ready(function() {
        preloaderFadeOutTime = 1500;
        function hidePreloader() {
          var preloader = $('.spinner-wrapper');
          preloader.fadeOut(preloaderFadeOutTime);
        }
        hidePreloader();
      });
      <?php
      }
      ?>
    </script>
  </body>
</html>

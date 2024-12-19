<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/actions/discord_functions.php");

// ACTION NOTIFICATIONS
if(isset($_GET['notInDiscord']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You need to be in the discord to use the CAD System!</div>';
}

if (json_decode(verify())->authorised == "true" || checkDomain() == false) {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo SERVER_SHORT_NAME; ?> CAD | Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
    <!--OPEN GRAPH FOR DISCORD RICH PRESENCE-->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo BASE_URL; ?>" />
    <meta property="og:title" content="<?php echo SERVER_SHORT_NAME; ?>" />
    <meta property="og:description" content="CAD System by Hamz#0001">
    <meta name="theme-color" content="<?php echo LOGS_COLOR; ?>">
  </head>
  <body>
    <div class="container-scroller">
      <div class="horizontal-menu">
        <!-- HEADER -->
<nav class="navbar top-navbar col-lg-12 col-12 p-0">
  <div class="container">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo" href="#"><?php echo SERVER_SHORT_NAME; ?> CAD</a>
      <a class="navbar-brand brand-logo-mini" href="#">CAD</a>
    </div>
  </div>
</nav>
      </div>

      <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
          <div class="content-wrapper">
            <?php if($actionMessage){echo $actionMessage;} ?>
            <div class="row justify-content-center" style="margin-top: 150px;">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">  
                            <div class="text-center w-75 m-auto">
                                <a>
                                    <span><img src="<?php echo SERVER_LOGO; ?>" alt="" height="60"></span>
                                </a>
                                <h4 class="text-muted mb-3 mt-3"><?php echo SERVER_SHORT_NAME; ?> CAD</h4>
                                <p class="text-muted mb-4 mt-3">You will need to login using discord.</p>
                            </div>
                            <div class="row">
                              <div class="col-md-3"></div>
                              <div class="col-md-6">
                                <form class="pb-3">
                                    <div class="form-group mb-0 text-center">
                                       <input id="mainBTN" class="btn btn-outline-info btn-block" type=button onClick="location.href='actions/register.php'" value='Log In'>
                                  </div>
                                </form>
                              </div>
                              <div class="col-md-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          <!-- FOOTER -->
          <?php include "includes/footer.inc.php"; ?>
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
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/dashboard.js"></script>
  </body>
</html>
<?php
} else {
  echo "";
}
?>
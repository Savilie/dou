<?php
require_once(__DIR__ . "/../config.php");
session_start();

if (empty($_SESSION['logged_in']))
{
  header('Location: '.BASE_URL.'/login.php');
}

$user_discordid = $_SESSION['user_discordid'];
$user_name = $_SESSION['user_name'];
$user_discriminator = $_SESSION['user_discriminator'];
$user_avatar = $_SESSION['user_avatar'];

try{
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
  echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$identifier = $pdo->query("SELECT * FROM users WHERE discordid='$user_discordid'");

foreach ($identifier as $row)
{
  $user_identifier = $row['identifier'];
  $user_department = $row['department'];
  $showsupervisor = $row['showsupervisor'];
}

?>
<nav class="navbar top-navbar col-lg-12 col-12 p-0">
  <div class="container">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo" href="#"><?php echo SERVER_SHORT_NAME; ?> CAD</a>
      <a class="navbar-brand brand-logo-mini" href="#">CAD</a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="modal" data-target="#editProfileModal" title="Edit Profile" href="" aria-expanded="false">
            <i class="mdi mdi-square-edit-outline"></i>
          </a>
          <a class="nav-link" href="<?php echo BASE_URL; ?>/actions/logout.php" title="Logout" aria-expanded="false">
            <i class="mdi mdi-logout"></i>
          </a>
          <a class="nav-link mr-0">
            <div class="navbar-profile">
              <img class="img-xs rounded-circle" src="<?php echo $user_avatar; ?>" alt="Avatar">
              <p class="mb-0 d-none d-sm-block navbar-profile-name"><?php echo $user_name; ?>#<?php echo $user_discriminator; ?></p>
            </div>
          </a>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
  </div>
</nav>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
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
                <button type="submit" name="editprofile_btn" class="btn btn-outline-info">Update</button>
            </div>
        </form>
        <div class="row text-center">
          <div class="col-md-6">
              <br>
              <a href="<?php echo BASE_URL; ?>/actions/discord_functions.php?refreshdepartmentsessions" class="btn btn-outline-info">Refresh Permissions</a>          
          </div>
          <div class="col-md-6">
              <?php
              if ($_SESSION['supervisor'] == 1 & $showsupervisor == 1)
              {
              ?>
              <br>
              <a href="<?php echo BASE_URL; ?>/actions/department_functions.php?showsupervisor=0" class="btn btn-outline-danger">Hide Supervisor Tag</a>
              <?php
              } else if ($_SESSION['supervisor'] == 1 & $showsupervisor == 0) {
              ?>
              <br>
              <a href="<?php echo BASE_URL; ?>/actions/department_functions.php?showsupervisor=1" class="btn btn-outline-success">Show Supervisor Tag</a>
              <?php
              }
              ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
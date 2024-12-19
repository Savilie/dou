<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
$user_discordid = $_SESSION['user_discordid'];

if (empty($_SESSION['logged_in']))
{
  header('Location: '.BASE_URL.'/login.php');
}

?>
<nav class="bottom-navbar">
  <div class="container">
    <ul class="nav page-navigation">
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">
          <span class="menu-title">Home</span>
        </a>
      </li>
      <?php
        if ($_SESSION['civilianperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/civilian/civilianDashboard.php">
          <span class="menu-title">Civilian Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['dmvperms'] == 1 && DMV_SYSTEM == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/dmv/dmvDashboard.php">
          <span class="menu-title">DMV Dashboard</span>
        </a>
      </li>
      <?php
        }

        if (true == false)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/dot/dotDashboard.php">
          <span class="menu-title">DOT Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['leoperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/leo/leoDashboard.php">
          <span class="menu-title">LEO Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['fireemsperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/fireems/fireemsDashboard.php">
          <span class="menu-title">Fire & EMS Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['dispatchperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/dispatch/dispatchDashboard.php">
          <span class="menu-title">Dispatcher Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['courtperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/court/courtDashboard.php">
          <span class="menu-title">Court Dashboard</span>
        </a>
      </li>
      <?php
        }

        if ($_SESSION['adminperms'] == 1)
        {
      ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/adminDashboard.php">
          <span class="menu-title">Admin Settings</span>
        </a>
      </li>
      <?php
        }
      ?>
    </ul>
  </div>
</nav>
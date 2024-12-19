<?php
require_once(__DIR__ . "/../config.php");
?>
<style>
.footer {
  background: transparent;
}

.horizontal-menu .top-navbar .navbar-brand-wrapper .navbar-brand {
  color: <?php echo ACCENT_COLOR; ?>;
}

.horizontal-menu .top-navbar .navbar-brand-wrapper .navbar-brand:hover {
  color: <?php echo ACCENT_COLOR; ?>;
}

.horizontal-menu .bottom-navbar .page-navigation > .nav-item.active > .nav-link .menu-title {
  color: <?php echo ACCENT_COLOR; ?>;
}

.horizontal-menu .bottom-navbar .page-navigation > .nav-item > .nav-link .menu-title {
  font-weight: 400;
}

.horizontal-menu .bottom-navbar .page-navigation > .nav-item > .nav-link .menu-title:hover {
  color: <?php echo ACCENT_COLOR; ?>;
}

.footer a {
  color: <?php echo ACCENT_COLOR; ?>;
}

body {
    background: <?php echo BACKGROUND_COLOR; ?>;
}

.horizontal-menu .top-navbar {
  background-color: <?php echo BACKGROUND_COLOR; ?>;
}

.container-scroller .content-wrapper {
  background-color: <?php echo BACKGROUND_COLOR; ?>;
}

.horizontal-menu .bottom-navbar {
  background-color: <?php echo CARD_COLOR; ?>;
}

.card .card-body {
  background-color: <?php echo CARD_COLOR; ?>;
}

.form-group label {
  color: <?php echo TEXT_COLOR; ?>;
}

 h5, .h5, h3, .h3 {
  color: <?php echo TEXT_COLOR; ?>;
}

.table th, .jsgrid .jsgrid-table th, .table td, .jsgrid .jsgrid-table td {
  color: <?php echo TEXT_COLOR; ?>;
}

.form-control {
  background-color: <?php echo INPUTBOX_COLOR; ?>;
  color: <?php echo TEXT_COLOR; ?> !important;
}

.form-control:focus {
  background-color: <?php echo INPUTBOX_COLOR; ?>;
  color: <?php echo TEXT_COLOR; ?> !important;
}

.main-panel {
  background-color: <?php echo BACKGROUND_COLOR; ?>;
}

html {
  --scrollbarBG: <?php echo BACKGROUND_COLOR; ?>;
  --thumbBG: <?php echo SCROLLBAR_COLOR; ?>;
}


.swal-overlay--show-modal .swal-modal {
  background-color: <?php echo CARD_COLOR; ?>;
}

.swal-title:not(:last-child) {
  color: <?php echo TEXT_COLOR; ?>;
}

.swal-text {
  color: <?php echo TEXT_COLOR; ?>;
}

.swal-icon--success:before {
  background-color: <?php echo CARD_COLOR; ?>;
}

.swal-icon--success:after {
  background-color: <?php echo CARD_COLOR; ?>;
}

.swal-icon--success__hide-corners {
  background-color: <?php echo CARD_COLOR; ?>;
}

.penal_result {
  padding: 3px;
  color: <?php echo TEXT_COLOR; ?>;
}
.penal_result ul li {
  padding: 5px 0;
  padding-left: 10px;
  background: <?php echo CARD_COLOR; ?>;
}
.penal_result ul li:hover {
  background: <?php echo BACKGROUND_COLOR; ?>;
}
</style>
<footer class="footer container" style="bottom: 0; position: absolute;">
<div class="d-sm-flex justify-content-center justify-content-sm-between">
  <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">HAMZ CAD Made with <i class="mdi mdi-heart text-danger"></i> by <a href="https://discord.gg/3DDWp6w" target="_blank">Hamz#0001</a></span>
</div>
</footer>
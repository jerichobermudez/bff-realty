<?php
  session_start();
  $pageTitle = 'Reports | Sales Reports';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<body class="layout-navbar-fixed layout-fixed sidebar-mini">
  <div class="wrapper">
    <?php require_once('includes/sidebar.php'); ?>
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Sales Reports</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              
            </div>
          </div>
        </div>
      </section>
    </div>
  <?php require_once('includes/footer.php') ?>
</html>
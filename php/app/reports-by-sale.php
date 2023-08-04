<?php
  session_start();
  $pageTitle = 'Reports | Sales Reports';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Sales Report
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-calendar"></i> Sales Report</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="box rounded-0">
      <div class="box-header with-border">
        
      </div>
    </div>
  </section>
</div>
<?php require_once('includes/footer.php') ?>
</html>
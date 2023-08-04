<?php
  session_start();
  $pageTitle = 'Dashboard';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Dashboard
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-chart-pie"></i> Dashboard</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="box rounded-0">
      <div class="box-header with-border">
        <h3 class="box-title">Dashboard</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body">
        
      </div>
    </div>
  </section>
</div>
<?php require_once('includes/footer.php') ?>
</html>
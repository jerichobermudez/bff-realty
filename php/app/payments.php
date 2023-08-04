<?php
  session_start();
  $pageTitle = 'Payments';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Payments
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-hand-holding-dollar"></i> Payments</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="box rounded-0">
      <div class="box-header with-border">
        <h3 class="box-title">Payment Lists</h3>
      </div>
      <div class="box-body text-sm">
        <div class="table-responsive">
          <table class="table table-hover nav-legacy" id="usersTable" width="100%">
            <thead>
              <tr>
                <th style="min-width: 1px;">ID</th>
                <th style="min-width: 100px;">Customer&nbsp;No.</th>
                <th style="min-width: 100px;">Client&nbsp;Name</th>
                <th style="min-width: 100px;">Project</th>
                <th style="min-width: 60px;">Blk/Lot</th>
                <th style="min-width: 60px;">A.R.&nbsp;No.</th>
                <th style="min-width: 60px;">Payment&nbsp;Amount</th>
                <th style="min-width: 60px;">Payment&nbsp;Date</th>
                <th class="text-center" style="min-width: 1px;">Setting</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once('includes/footer.php') ?>
</html>
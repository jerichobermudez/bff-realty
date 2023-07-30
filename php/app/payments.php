<?php
  session_start();
  $pageTitle = 'Payments';
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
              <h1>Payments</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Payment Lists</h3>
            </div>
            <div class="card-body text-sm">
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
        </div>
      </section>
    </div>
  <?php require_once('includes/footer.php') ?>
</html>
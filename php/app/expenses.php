<?php
  session_start();
  $pageTitle = 'Expenses';
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
              <h1>Expenses</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Expense Lists</h3>
            </div>
            <div class="card-body text-sm">
              <div class="table-responsive">
                <table class="table table-hover nav-legacy" id="usersTable" width="100%">
                  <thead>
                    <tr class="text-center">
                      <th style="min-width: 1px;">#</th>
                      <th style="min-width: 100px;">Name.</th>
                      <th style="min-width: 100px;">Particulars</th>
                      <th style="min-width: 60px;">Type&nbsp;of&nbsp;Expense</th>
                      <th style="min-width: 60px;">Type&nbsp;of&nbsp;Receipt</th>
                      <th style="min-width: 10px;">No</th>
                      <th style="min-width: 60px;">Date&nbsp;of&nbsp;Receipt</th>
                      <th style="min-width: 60px;">Amount</th>
                      <th style="min-width: 60px;">Date&nbsp;Added</th>
                      <th style="min-width: 1px;">Setting</th>
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
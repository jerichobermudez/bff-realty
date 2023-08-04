<?php
  session_start();
  $pageTitle = 'Commissions';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Commissions
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dollar"></i> Commissions</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="box rounded-0">
      <div class="box-header with-border">
        <h3 class="box-title">Commissions Lists</h3>
      </div>
      <div class="box-body text-sm">
        <div class="table-responsive">
          <table class="table table-hover nav-legacy" id="commissionsTable" width="100%">
            <thead>
              <tr class="text-center">
                <th style="min-width: 1px;">#</th>
                <th style="min-width: 100px;">Name.</th>
                <th style="min-width: 10px;">No</th>
                <th style="min-width: 60px;">Amount</th>
                <th style="min-width: 60px;">Date&nbsp;Added</th>
                <th style="min-width: 1px;">Setting</th>
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
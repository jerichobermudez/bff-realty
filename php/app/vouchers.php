<?php
  session_start();
  $pageTitle = 'Vouchers';
  require_once('includes/session.php');
  if ((int) $role !== (int) UserRole::ADMIN) { header('Location: /'); }
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Vouchers
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-file"></i> Vouchers</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box rounded-0">
          <div class="box-header with-border p-2">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="box-title">Manage Vouchers</h3>
              <button type="button" class="btn btn-primary btn-flat btn-sm" onclick="generateVoucherCode(event)"> Generate Voucher</button>
            </div>
          </div>
          <div class="box-body text-sm">
            <div class="table-responsive no-border">
              <table class="table table-hover nav-legacy" id="vouchersTable" width="100%">
                <thead>
                  <tr>
                    <th width="1%" style="min-width: 1%;">#</th>
                    <th width="15%">Voucher&nbsp;Code</th>
                    <th width="15%">Used&nbsp;By</th>
                    <th width="15%">Date&nbsp;Created</th>
                    <th width="15%">Expiration</th>
                    <th width="10%">Status</th>
                    <th width="1%" class="text-center" style="min-width: 1%;">Setting</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once('includes/footer.php') ?>
<script src="/assets/pages/voucher.js?ver=<?= $version ?>"></script>
</html>
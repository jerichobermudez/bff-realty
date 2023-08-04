<?php
  session_start();
  $pageTitle = 'Banks';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Banks
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-bank"></i> Banks</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-4">
        <div class="box rounded-0">
          <div class="box-header with-border">
            <h3 class="box-title">Add Bank</h3>
          </div> 
          <div class="box-body pt-1">
            <form id="addBankForm" onsubmit="handleAddBank(event)">
              <div class="form-group">
                <label for="bank_name">Name:</label>
                <input type="text" class="form-control input-sm mt-n1" name="bank_name" id="bank_name" placeholder="Bank Name">
              </div>
              <button type="submit" class="btn btn-primary btn-flat btn-block btn-sm">Save</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="box rounded-0">
          <div class="box-header with-border">
            <h3 class="box-title">Manage Banks</h3>
          </div>
          <div class="box-body text-sm">
            <div class="table-responsive">
              <table class="table border-3 table-hover nav-legacy" id="banksTable" width="100%">
                <thead>
                  <tr>
                    <th width="1%">ID</th>
                    <th width="30%">Bank&nbsp;Name</th>
                    <th width="30%">Date&nbsp;Added</th>
                    <th width="1%" class="text-center">Setting</th>
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
<div class="modal fade" id="editBankModal" tabindex="-1" role="dialog" aria-labelledby="editBankModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header p-3">
        <label class="h4 modal-title" id="editBankModalTitle">Edit Bank</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
        </button>
      </div>
      <form id="editBankForm" onsubmit="handleEditBank(event)">
        <div class="modal-body pb-1">
          <div class="box box-solid rounded-0">
            <div class="box-body">
              <input type="hidden" id="edit_bank_id" name="bank_id">
              <div class="form-group">
                <label for="edit_bank_name">Name:</label>
                <input type="text" class="form-control input-sm mt-n1" name="bank_name" id="edit_bank_name" placeholder="Bank Name">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer p-3">
          <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-flat btn-sm">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php') ?>
<script src="/assets/pages/bank.js?ver=<?= $version ?>"></script>
</html>
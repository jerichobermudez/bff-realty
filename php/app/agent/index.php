<?php
  session_start();
  $version = '1.0.0';
  if (!isset($_SESSION['agentmsaid']) || trim($_SESSION['agentmsaid']) == '') {
    header('location:/');
  }
?>
<html>
  <head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Agent | CMS</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="icon" href="/assets/images/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/font-awesome.all.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/select2.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/bootstrap-datepicker.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/AdminLTE.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/jquery.toast.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/margin-padding.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/custom.css?ver=<?= $version ?>">
    <style>
      .select2.select2-container {
        font-size: 0.875em !important;
      }

      .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.05em + 0.75rem + 2px) !important;
      }

      .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered, .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        line-height: calc(1.05em + .75rem) !important;
      }

      .select2-container--bootstrap4 .select2-selection__clear {
        padding-top: 0.03em !important;
        padding-left: 0.17em !important;
        margin-top: 0.5em !important;
        margin-right: 0em !important;
      }
    </style>
  </head>
  <body style="background-color: #ecf0f5;">
    <div class="container-fluid">
    <?php if (isset($_SESSION['agent'])) { ?>
      <div class="p-2">
        <div class="box box-warning my-2 rounded-0">
          <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex justify-content-between align-items-center">
                <img src="/assets/images/logo.png" class="mr-2" width="40px">
                <span class="h3 m-0 text-bold">BFF Realty and Development Inc.</span>
              </div>
              <div class="text-right">
                Agent: <u><?= $_SESSION['agent_name'] ?? '' ?></u>
                <br>
                <a href="/agent/logout">Logout</a>
              </div>
            </div>
          </div>
          <div class="box-body mt-2">
            <label class="p-0 m-0">All fields marked with an asterisk (<span class="text-red">*</span>) are required</label>
          </div>
        </div>
        <form onsubmit="handleReserveClientForm(event)">
          <div class="row mb-0">
            <div class="col-lg-6 py-2">
              <div class="box rounded-0 my-2">
                <div class="box-header with-border px-3">
                  <h3 class="box-title text-bold">Basic Information</h3>
                </div> 
                <div class="box-body p-3">
                  <div class="form-group mb-2">
                    <label for="entry_date">Entry Date:
                      <span class="text-red">*</span>
                    </label>
                    <input type="text" class="form-control datetimepicker-input mt-n1" name="entry_date" id="entry_date" data-toggle="datetimepicker" data-target="#entry_date" placeholder="Entry Date" value="<?= date('Y-m-d') ?>" autocomplete="off">
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-4">
                      <label for="firstname">Firstname:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="firstname" id="firstname" placeholder="Firstname" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="middlename">Middlename:</label>
                      <input type="text" class="form-control mt-n1" name="middlename" id="middlename" placeholder="Middlename" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="lastname">Lastname:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="lastname" id="lastname" placeholder="Lastname" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group mb-2">
                    <label for="address">Address:
                      <span class="text-red">*</span>
                    </label>
                    <textarea class="form-control mt-n1" name="address" id="address" rows="1" placeholder="Address"></textarea>
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-6">
                      <label for="contact">Contact No:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="contact" id="contact" placeholder="Contact No" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="email">Email:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="email" id="email" placeholder="Email" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="birthday">Birthday:</label>
                      <input type="text" class="form-control datetimepicker-input mt-n1" name="birthday" id="birthday" data-toggle="datetimepicker" data-target="#birthday" placeholder="Birthday" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="marital_status">Marital Status:
                        <span class="text-red">*</span>
                      </label>
                      <select class="form-control mt-n1" name="marital_status" id="marital_status">
                        <option value>Marital Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Separated">Separated</option>
                        <option value="Widowed">Widowed</option>
                      </select>
                    </div>
                  </div>
                  <div class="spouse-details">
                    <div class="form-group mb-2">
                      <label for="spouse_name">Name of Spouse:</label>
                      <input type="text" class="form-control mt-n1" name="spouse_name" id="spouse_name" placeholder="Name of Spouse" autocomplete="off">
                    </div>
                    <div class="row">
                      <div class="form-group mb-2 col-sm-6">
                        <label for="spouse_contact">Spouse Contact No:</label>
                        <input type="text" class="form-control mt-n1" name="spouse_contact" id="spouse_contact" placeholder="Spouse Contact No" autocomplete="off">
                      </div>
                      <div class="form-group mb-2 col-sm-6">
                        <label for="spouse_email">Spouse Email:</label>
                        <input type="text" class="form-control mt-n1" name="spouse_email" id="spouse_email" placeholder="Spouse Email" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 py-2">
              <div class="box rounded-0 my-2">
                <div class="box-header with-border px-3">
                  <h3 class="box-title text-bold">Employment Details</h3>
                </div> 
                <div class="box-body p-3">
                  <div class="form-group mb-2">
                    <label for="company_name">Company Name:</label>
                    <input type="text" class="form-control mt-n1" name="company_name" id="company_name" placeholder="Company Name" autocomplete="off">
                  </div>
                  <div class="form-group mb-2">
                    <label for="company_address">Company Address:</label>
                    <input type="text" class="form-control mt-n1" name="company_address" id="company_address" placeholder="Company Address" autocomplete="off">
                  </div>
                  <div class="form-group mb-2">
                    <label for="company_contact">Company Contact No:</label>
                    <input type="text" class="form-control mt-n1" name="company_contact" id="company_contact" placeholder="Company Contact No" autocomplete="off">
                  </div>
                  <div class="form-group mb-2">
                    <label for="year_of_stay">Length of Service:</label>
                    <input type="text" class="form-control mt-n1" name="year_of_stay" id="year_of_stay" placeholder="Years of stay in the Company" autocomplete="off">
                  </div>
                  <div class="form-group mb-2">
                    <label for="position">Position in the Company:</label>
                    <input type="text" class="form-control mt-n1" name="position" id="position" placeholder="Position in the Company" autocomplete="off">
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-4">
                      <label for="tin_id">TIN ID.:</label>
                      <input type="text" class="form-control mt-n1" name="tin_id" id="tin_id" placeholder="TIN ID." autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="sss_id">SSS ID.:</label>
                      <input type="text" class="form-control mt-n1" name="sss_id" id="sss_id" placeholder="SSS ID." autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="monthly_salary">Monthly Salary:</label>
                      <input type="text" class="form-control mt-n1" name="monthly_salary" id="monthly_salary" placeholder="Monthly Salary" autocomplete="off">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="box rounded-0 my-2">
                <div class="box-header with-border px-3">
                  <h3 class="box-title text-bold">Property Details</h3>
                </div> 
                <div class="box-body edit-property-details p-3">
                  <div class="row">
                    <div class="form-group mb-2 col-sm-6">
                      <label for="project_name">Project Name:
                        <span class="text-red">*</span>
                      </label>
                      <select class="form-control select2-get-projects" name="project_name" id="project_name" data-placeholder="Project Name">
                        <option value>Project Name</option>
                      </select>
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="project_location">Location:</label>
                      <input type="text" class="form-control" name="project_location" id="project_location" placeholder="Project Location" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-4">
                      <label for="property_phase">Phase:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="property_phase" id="property_phase" placeholder="Phase" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="property_block">Block:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="property_block" id="property_block" placeholder="Block" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="property_lot">Lot:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="property_lot" id="property_lot" placeholder="Lot" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-6">
                      <label for="property_lot_area">Lot Area:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="property_lot_area" id="property_lot_area" placeholder="Lot Area" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="property_price_per_sqm">Price per SQM:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="property_price_per_sqm" id="property_price_per_sqm" placeholder="Price per SQM" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-6">
                      <label for="monthly_amortization">Monthly Amortization:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="monthly_amortization" id="monthly_amortization" placeholder="Monthly Amortization" autocomplete="off" readonly>
                    </div>
                    <div class="form-group mb-2 col-sm-6">
                      <label for="terms_of_payment">Terms of Payment(Months):
                        <span class="text-red">*</span>
                      </label>
                      <select name="terms_of_payment" id="terms_of_payment" class="form-control mt-n1">
                          <option value>Select Terms of Payment</option>
                          <option value="12">12</option>
                          <option value="24">24</option>
                          <option value="30">30</option>
                          <option value="36">36</option>
                          <option value="48">48</option>
                          <option value="60">60</option>
                          <option value="72">72</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group mb-2 col-sm-4">
                      <label for="downpayment_amount">Downpayment Amount:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1" name="downpayment_amount" id="downpayment_amount" placeholder="Downpayment Amount" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="downpayment_date">Downpayment Date:
                        <span class="text-red">*</span>
                      </label>
                      <input type="text" class="form-control mt-n1 datetimepicker-input" name="downpayment_date" id="downpayment_date" data-toggle="datetimepicker" data-target="#downpayment_date" placeholder="Downpayment Date" autocomplete="off">
                    </div>
                    <div class="form-group mb-2 col-sm-4">
                      <label for="downpayment_due_date">Downpayment Due Date:</label>
                      <input type="text" class="form-control mt-n1 datetimepicker-input" name="downpayment_due_date" id="downpayment_due_date" data-toggle="datetimepicker" data-target="#downpayment_due_date" placeholder="Downpayment Due Date" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group mb-0">
                    <label for="property_remarks">Remarks:</label>
                    <textarea class="form-control mt-n1" name="property_remarks" id="property_remarks" rows="2" placeholder="Remarks"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <button type="submit" class="btn btn-primary btn-flat btn-block">Submit</button>
            </div>
          </div>
        </form>
      </div>
    <?php } else { ?>
      <div class="modal show" id="inputVoucherModal" data-backdrop="static" role="dialog" aria-labelledby="editClientModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content rounded-0">
            <form class="m-0" onsubmit="handleSubmitAgentVoucher(event)">
              <div class="modal-body p-0">
                <div class="box box-solid rounded-0 m-0">
                  <div class="box-body">
                    <div class="form-group m-1">
                      <input type="text" id="voucher_code" name="voucher_code" class="form-control input-lg p-3 text-lg text-bold text-center" placeholder="Voucher Code" autocomplete="off">
                    </div>
                    <div class="form-group m-1 pt-1">
                      <select class="form-control select2-get-agents" name="user_id" id="user_id" data-placeholder="Choose Agent">
                        <option value>Choose Agent</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer p-2">
                <button type="submit" name="submitVoucher" class="btn btn-sm btn-primary btn-flat btn-block">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php } ?>
    </div>
  </body>
  <script src="/assets/js/jquery.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/jquery-ui.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/bootstrap.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/adminlte.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/jquery.toast.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/moment.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/select2.full.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/bootstrap-datepicker.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/pages/common.js?ver=<?= $version ?>"></script>
  <script src="/assets/pages/agent-voucher.js?ver=<?= $version ?>"></script>
</html>

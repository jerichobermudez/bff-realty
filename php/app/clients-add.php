<?php
  session_start();
  $pageTitle = 'Add Clients';
  require_once('includes/session.php');
  require_once('enums/PaymentTypes.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Clients
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-users"></i> Clients</a></li>
      <li class="active">Add</li>
    </ol>
  </section>

  <section class="content">
    <div class="box rounded-0">
      <div class="box-header with-border">
        <h3 class="box-title">Add Client</h3>
      </div> 
      <div class="box-body">
        <form id="addClientForm" onsubmit="handleAddClient(event)">
          <div class="text-center d-flex align-items-center justify-content-center">
            <div class="">
              <span class="step">1</span>
              <label class="step-title d-xs-none">Client Information</label>
            </div>
            <div class="">
              <span class="step">2</span>
              <label class="step-title d-xs-none">Employment Details</label>
            </div>
            <div class="">
              <span class="step">3</span>
              <label class="step-title d-xs-none">Property Details</label>
            </div>
          </div>
          <hr/>

          <!-- Client Information -->
          <div class="tab">
            <div class="form-group mb-3">
              <label for="client_entry_date">Entry Date:
                <span class="text-red">*</span>
              </label>
              <input type="text" class="form-control field-required datetimepicker-input mt-n1" name="client_entry_date" id="client_entry_date" data-toggle="datetimepicker" data-target="#client_entry_date" placeholder="Entry Date" value="<?= date('Y-m-d') ?>" oninput="handleFormInput(event)" autocomplete="off">
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-4">
                <label for="client_firstname">Firstname:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control field-required mt-n1" name="client_firstname" id="client_firstname" placeholder="Firstname" oninput="handleFormInput(event)" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="client_middlename">Middlename:</label>
                <input type="text" class="form-control mt-n1" name="client_middlename" id="client_middlename" placeholder="Middlename" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="client_lastname">Lastname:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control field-required mt-n1" name="client_lastname" id="client_lastname" placeholder="Lastname" oninput="handleFormInput(event)" autocomplete="off">
              </div>
            </div>
            <div class="form-group mb-3">
              <label for="client_address">Address:
                <span class="text-red">*</span>
              </label>
              <textarea class="form-control field-required mt-n1" name="client_address" id="client_address" rows="3" placeholder="Address" oninput="handleFormInput(event)"></textarea>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="client_contact">Contact No:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control field-required mt-n1" name="client_contact" id="client_contact" placeholder="Contact No" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="client_email">Email:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control field-required mt-n1" name="client_email" id="client_email" placeholder="Email" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="client_birthday">Birthday:</label>
                <input type="text" class="form-control datetimepicker-input mt-n1" name="client_birthday" id="client_birthday" data-toggle="datetimepicker" data-target="#client_birthday" placeholder="Birthday" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="marital_status">Marital Status:
                  <span class="text-red">*</span>
                </label>
                <select class="form-control field-required mt-n1" name="marital_status" id="marital_status" onchange="handleFormInput(event)">
                  <option value="">Marital Status</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Separated">Separated</option>
                  <option value="Widowed">Widowed</option>
                </select>
              </div>
            </div>
            <div class="spouse-details ">
              <div class="form-group mb-3">
                <label for="spouse_name">Name of Spouse:</label>
                <input type="text" class="form-control mt-n1" name="spouse_name" id="spouse_name" placeholder="Name of Spouse" oninput="handleFormInput(event)" autocomplete="off">
              </div>
              <div class="form-group mb-3">
                <label for="spouse_contact">Spouse Contact No:</label>
                <input type="text" class="form-control mt-n1" name="spouse_contact" id="spouse_contact" placeholder="Spouse Contact No" autocomplete="off">
              </div>
              <div class="form-group mb-3">
                <label for="spouse_email">Spouse Email:</label>
                <input type="text" class="form-control mt-n1" name="spouse_email" id="spouse_email" placeholder="Spouse Email" autocomplete="off">
              </div>
            </div>
          </div>

          <!-- Employment Details -->
          <div class="tab">
            <div class="form-group mb-3">
              <label for="company_name">Company Name:</label>
              <input type="text" class="form-control mt-n1" name="company_name" id="company_name" placeholder="Company Name" autocomplete="off">
            </div>
            <div class="form-group mb-3">
              <label for="company_address">Company Address:</label>
              <input type="text" class="form-control mt-n1" name="company_address" id="company_address" placeholder="Company Address" autocomplete="off">
            </div>
            <div class="form-group mb-3">
              <label for="company_contact">Company Contact No:</label>
              <input type="text" class="form-control mt-n1" name="company_contact" id="company_contact" placeholder="Company Contact No" autocomplete="off">
            </div>
            <div class="form-group mb-3">
              <label for="year_of_stay">Length of Service:</label>
              <input type="text" class="form-control mt-n1" name="year_of_stay" id="year_of_stay" placeholder="Years of stay in the Company" autocomplete="off">
            </div>
            <div class="form-group mb-3">
              <label for="position">Position in the Company:</label>
              <input type="text" class="form-control mt-n1" name="position" id="position" placeholder="Position in the Company" autocomplete="off">
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-4">
                <label for="tin_id">TIN ID.:</label>
                <input type="text" class="form-control mt-n1" name="tin_id" id="tin_id" placeholder="TIN ID." autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="sss_id">SSS ID.:</label>
                <input type="text" class="form-control mt-n1" name="sss_id" id="sss_id" placeholder="SSS ID." autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="monthly_salary">Monthly Salary:</label>
                <input type="text" class="form-control mt-n1" name="monthly_salary" id="monthly_salary" placeholder="Monthly Salary" autocomplete="off">
              </div>
            </div>
          </div>

          <!-- Property Details -->
          <div class="tab">
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="project_name">Project Name:
                  <span class="text-red">*</span>
                </label>
                <select class="form-control select2-project-name border-danger" name="project_name" id="project_name" data-placeholder="Project Name">
                  <option value>Project Name</option>
                </select>
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="project_location">Location:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control" name="project_location" id="project_location" placeholder="Project Location" readonly>
              </div>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-4">
                <label for="property_phase">Phase:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="property_phase" id="property_phase" placeholder="Phase" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="property_block">Block:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="property_block" id="property_block" placeholder="Block" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-4">
                <label for="property_lot">Lot:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="property_lot" id="property_lot" placeholder="Lot" autocomplete="off">
              </div>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="property_lot_area">Lot Area:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="property_lot_area" id="property_lot_area" placeholder="Lot Area" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="property_price_per_sqm">Price per SQM:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="property_price_per_sqm" id="property_price_per_sqm" placeholder="Price per SQM" autocomplete="off">
              </div>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="monthly_amortization">Monthly Amortization:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="monthly_amortization" id="monthly_amortization" placeholder="Monthly Amortization" autocomplete="off" readonly>
              </div>
              <div class="form-group mb-3 col-sm-6">
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
              <div class="form-group mb-3 col-sm-6">
                <label for="downpayment_amount">Downpayment Amount:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1" name="downpayment_amount" id="downpayment_amount" placeholder="Downpayment Amount" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="downpayment_date">Downpayment Date:
                  <span class="text-red">*</span>
                </label>
                <input type="text" class="form-control mt-n1 datetimepicker-input" name="downpayment_date" id="downpayment_date" data-toggle="datetimepicker" data-target="#downpayment_date" placeholder="Downpayment Date" autocomplete="off">
              </div>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="downpayment_type">Downpayment Type:
                  <span class="text-red">*</span>
                </label>
                <select name="downpayment_type" id="downpayment_type" class="form-control mt-n1">
                  <option value>Select Downpayment Type</option>
                  <?php
                    $allowedPaymentTypes = [
                      PaymentTypes::DOWNPAYMENT,
                      PaymentTypes::HOLDING_FEE,
                      PaymentTypes::PARTIAL_IN_DOWNPAYMENT,
                    ];
                    foreach ($allowedPaymentTypes as $key => $value) {
                      echo '<option value="' . $value . '">' .
                        PaymentTypes::getTextValue($value) .
                      '</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="downpayment_due_date">Downpayment Due Date:</label>
                <input type="text" class="form-control mt-n1 datetimepicker-input" name="downpayment_due_date" id="downpayment_due_date" data-toggle="datetimepicker" data-target="#downpayment_due_date" placeholder="Downpayment Due Date" autocomplete="off">
              </div>
            </div>
            <div class="row">
              <div class="form-group mb-3 col-sm-6">
                <label for="sales_coordinator">Sales Coordinator:</label>
                <input type="text" class="form-control mt-n1" name="sales_coordinator" id="sales_coordinator" placeholder="Sales Coordinator" autocomplete="off">
              </div>
              <div class="form-group mb-3 col-sm-6">
                <label for="assisting_coordinator">Assisting Coordinator:</label>
                <input type="text" class="form-control mt-n1" name="assisting_coordinator" id="assisting_coordinator" placeholder="Assisting Coordinator" autocomplete="off">
              </div>
            </div>
            <div class="form-group">
              <label for="remarks">Remarks:</label>
              <textarea class="form-control mt-n1" name="remarks" id="remarks" rows="3" placeholder="Remarks"></textarea>
            </div>
          </div>

          <div style="overflow:auto;">
            <div style="float:right;">
              <button type="button" id="prevBtn" class="btn btn-primary btn-md btn-flat" onclick="nextPrev(-1)">Previous</button>
              <button type="button" id="nextBtn" class="btn btn-primary btn-md btn-flat" onclick="nextPrev(1)">Next</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require_once('includes/footer.php') ?>
<script src="/assets/pages/client-add.js?ver=<?= $version ?>"></script>
</html>
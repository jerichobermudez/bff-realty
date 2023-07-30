<?php
  session_start();
  $pageTitle = 'Clients';
  require_once('includes/session.php');
  require_once('enums/PaymentTypes.php');
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
              <h1>Clients</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Clients</a></li>
                <li class="breadcrumb-item active">List</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Manage Clients</h3>
            </div> 
            <div class="card-body">
              <div class="table-responsive">
                <table class="table border-3 table-hover nav-legacy" id="clientsTable" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account&nbsp;ID</th>
                      <th style="min-width: 160px;">Client Name</th>
                      <th style="min-width: 150px;">Project Name</th>
                      <th style="min-width: 70px;">Blk & Lot</th>
                      <th style="min-width: 70px;">Lot Area</th>
                      <th class="text-center" style="min-width: 180px;">Setting</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Edit Client -->
    <div class="modal fade" id="editClientModal" data-backdrop="static" role="dialog" aria-labelledby="editClientModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <label class="h4 modal-title" id="editClientModalTitle">Edit Client</label>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
            </button>
          </div>
          <form id="editClientForm" onsubmit="handleEditClient(event)">
            <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
              <input type="hidden" id="edit_client_id" name="edit_client_id">
              <div class="row">
                <div class="col-lg-6">
                  <div class="card rounded-0">
                    <div class="card-header py-1 px-2 text-lg font-weight-bold">
                      Basic Information
                    </div>
                    <div class="card-body p-2">
                      <div class="form-group mb-1">
                        <label for="edit_customer_no" class="text-sm">Customer No:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" id="edit_customer_no" readonly>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_firstname" class="text-sm">Firstname:*</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_firstname" id="edit_firstname" placeholder="Firstname" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_middlename" class="text-sm">Middlename:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_middlename" id="edit_middlename" placeholder="Middlename" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_lastname" class="text-sm">Lastname:*</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_lastname" id="edit_lastname" placeholder="Lastname" autocomplete="off">
                        </div>
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_address" class="text-sm">Address:*</label>
                        <textarea class="form-control form-control-sm mt-n2" name="edit_address" id="edit_address" rows="1" placeholder="Address"></textarea>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_contact" class="text-sm">Contact No:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_contact" id="edit_contact" placeholder="Contact No" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_email" class="text-sm">Email:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_email" id="edit_email" placeholder="Email" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_birthday" class="text-sm">Birthday:</label>
                          <input type="text" class="form-control form-control-sm datetimepicker-input mt-n2" name="edit_birthday" id="edit_birthday" data-toggle="datetimepicker" data-target="#edit_birthday" placeholder="Birthday" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_marital_status" class="text-sm">Marital Status:</label>
                          <select class="form-control form-control-sm mt-n2" name="edit_marital_status" id="edit_marital_status">
                            <option value>Marital Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Separated">Separated</option>
                            <option value="Widowed">Widowed</option>
                          </select>
                        </div>
                      </div>
                      <div class="spouse-details">
                        <div class="form-group mb-1">
                          <label for="edit_spouse_name" class="text-sm">Name of Spouse:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_spouse_name" id="edit_spouse_name" placeholder="Name of Spouse" autocomplete="off">
                        </div>
                        <div class="row">
                          <div class="form-group mb-1 col-sm-6">
                            <label for="edit_spouse_contact" class="text-sm">Spouse Contact No:</label>
                            <input type="text" class="form-control form-control-sm mt-n2" name="edit_spouse_contact" id="edit_spouse_contact" placeholder="Spouse Contact No" autocomplete="off">
                          </div>
                          <div class="form-group mb-1 col-sm-6">
                            <label for="edit_spouse_email" class="text-sm">Spouse Email:</label>
                            <input type="text" class="form-control form-control-sm mt-n2" name="edit_spouse_email" id="edit_spouse_email" placeholder="Spouse Email" autocomplete="off">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card rounded-0">
                    <div class="card-header py-1 px-2 text-lg font-weight-bold">
                      Employment Details
                    </div>
                    <div class="card-body p-2">
                      <div class="form-group mb-1">
                        <label for="edit_company_name" class="text-sm">Company Name:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" name="edit_company_name" id="edit_company_name" placeholder="Company Name" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_company_address" class="text-sm">Company Address:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" name="edit_company_address" id="edit_company_address" placeholder="Company Address" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_company_contact" class="text-sm">Company Contact No:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" name="edit_company_contact" id="edit_company_contact" placeholder="Company Contact No" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_year_of_stay" class="text-sm">Length of Service:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" name="edit_year_of_stay" id="edit_year_of_stay" placeholder="Years of stay in the Company" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_position" class="text-sm">Position in the Company:</label>
                        <input type="text" class="form-control form-control-sm mt-n2" name="edit_position" id="edit_position" placeholder="Position in the Company" autocomplete="off">
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_tin_id" class="text-sm">TIN ID.:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_tin_id" id="edit_tin_id" placeholder="TIN ID." autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_sss_id" class="text-sm">SSS ID.:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_sss_id" id="edit_sss_id" placeholder="SSS ID." autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_monthly_salary" class="text-sm">Monthly Salary:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_monthly_salary" id="edit_monthly_salary" placeholder="Monthly Salary" autocomplete="off">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12"><hr class="border-dark mt-0 mb-3"></div>
                <div class="col-lg-12">
                  <div class="card rounded-0">
                    <div class="card-header py-1 px-2 text-lg font-weight-bold">
                      Property Details
                    </div>
                    <div class="card-body edit-property-details p-2">
                      <input type="hidden" id="edit_project_id" name="edit_project_id">
                      <div class="row">
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_property_id" class="text-sm">Project Name:</label>
                          <select class="form-control select2-get-edit-properties" name="edit_property_id" id="edit_property_id" data-placeholder="Project Name" onchange="handleGetEditProperty(this.value)">
                            <option value>Project Name</option>
                          </select>
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_project_location" class="text-sm">Location:</label>
                          <input type="text" class="form-control form-control-sm" name="edit_project_location" id="edit_project_location" placeholder="Project Location" readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_property_phase" class="text-sm">Phase:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_property_phase" id="edit_property_phase" placeholder="Phase" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_property_block" class="text-sm">Block:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_property_block" id="edit_property_block" placeholder="Block" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_property_lot" class="text-sm">Lot:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_property_lot" id="edit_property_lot" placeholder="Lot" autocomplete="off">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_property_lot_area" class="text-sm">Lot Area:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_property_lot_area" id="edit_property_lot_area" placeholder="Lot Area" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_property_price_per_sqm" class="text-sm">Price per SQM:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_property_price_per_sqm" id="edit_property_price_per_sqm" placeholder="Price per SQM" autocomplete="off">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_monthly_amortization" class="text-sm">Monthly Amortization:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_monthly_amortization" id="edit_monthly_amortization" placeholder="Monthly Amortization" autocomplete="off" readonly>
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_terms_of_payment" class="text-sm">Terms of Payment(Months):</label>
                          <select name="edit_terms_of_payment" id="edit_terms_of_payment" class="form-control form-control-sm mt-n2">
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
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_downpayment_amount" class="text-sm">Downpayment Amount:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_downpayment_amount" id="edit_downpayment_amount" placeholder="Downpayment Amount" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_downpayment_date" class="text-sm">Downpayment Date:</label>
                          <input type="text" class="form-control form-control-sm mt-n2 datetimepicker-input" name="edit_downpayment_date" id="edit_downpayment_date" data-toggle="datetimepicker" data-target="#edit_downpayment_date" placeholder="Downpayment Date" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-4">
                          <label for="edit_downpayment_due_date" class="text-sm">Downpayment Due Date:</label>
                          <input type="text" class="form-control form-control-sm mt-n2 datetimepicker-input" name="edit_downpayment_due_date" id="edit_downpayment_due_date" data-toggle="datetimepicker" data-target="#edit_downpayment_due_date" placeholder="Downpayment Due Date" autocomplete="off">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_sales_coordinator" class="text-sm">Sales Coordinator:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_sales_coordinator" id="edit_sales_coordinator" placeholder="Sales Coordinator" autocomplete="off">
                        </div>
                        <div class="form-group mb-1 col-sm-6">
                          <label for="edit_assisting_coordinator" class="text-sm">Assisting Coordinator:</label>
                          <input type="text" class="form-control form-control-sm mt-n2" name="edit_assisting_coordinator" id="edit_assisting_coordinator" placeholder="Assisting Coordinator" autocomplete="off">
                        </div>
                      </div>
                      <div class="form-group mb-1">
                        <label for="edit_property_remarks" class="text-sm">Remarks:</label>
                        <textarea class="form-control form-control-sm form-control-sm mt-n2" name="edit_property_remarks" id="edit_property_remarks" rows="2" placeholder="Remarks"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer px-2 py-1">
              <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary btn-flat btn-sm">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Client Details -->
    <div class="modal fade" id="getClientDetailsModal" tabindex="-1" role="dialog" aria-labelledby="getClientDetailsModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <label class="h4 modal-title" id="getClientDetailsModalTitle">Client Details</label>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 250px;">
          </div>
          <div class="modal-footer px-2 py-1">
            <button type="button" class="btn btn-sm btn-secondary btn-flat" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Property -->
    <div class="modal fade" id="addPropertyModal" data-backdrop="static" role="dialog" aria-labelledby="addPropertyModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <h5 class="modal-title" id="addPropertyModalTitle">Add Property</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
            </button>
          </div>
          <form id="addPropertyForm" onsubmit="handleAddProperty(event)">
            <div class="modal-body pb-0">
              <input type="hidden" id="property_client_id" name="client_id">
              <div class="row">
                <div class="form-group mb-1 col-sm-6">
                  <label for="property_project_name">Project Name:</label>
                  <select class="form-control select2-project-name" name="property_project_name" id="property_project_name" data-placeholder="Project Name">
                    <option value>Project Name</option>
                  </select>
                </div>
                <div class="form-group mb-1 col-sm-6">
                  <label for="property_project_location">Location:</label>
                  <input type="text" class="form-control" name="property_project_location" id="property_project_location" placeholder="Project Location" readonly>
                </div>
              </div>
              <div class="row">
                <div class="form-group mb-1 col-sm-4">
                  <label for="property_phase">Phase:</label>
                  <input type="text" class="form-control mt-n2" name="property_phase" id="property_phase" placeholder="Phase" autocomplete="off">
                </div>
                <div class="form-group mb-1 col-sm-4">
                  <label for="property_block">Block:</label>
                  <input type="text" class="form-control mt-n2" name="property_block" id="property_block" placeholder="Block" autocomplete="off">
                </div>
                <div class="form-group mb-1 col-sm-4">
                  <label for="property_lot">Lot:</label>
                  <input type="text" class="form-control mt-n2" name="property_lot" id="property_lot" placeholder="Lot" autocomplete="off">
                </div>
              </div>
              <div class="row">
                <div class="form-group mb-1 col-sm-6">
                  <label for="property_lot_area">Lot Area:</label>
                  <input type="text" class="form-control mt-n2" name="property_lot_area" id="property_lot_area" placeholder="Lot Area" autocomplete="off">
                </div>
                <div class="form-group mb-1 col-sm-6">
                  <label for="property_price_per_sqm">Price per SQM:</label>
                  <input type="text" class="form-control mt-n2" name="property_price_per_sqm" id="property_price_per_sqm" placeholder="Price per SQM" autocomplete="off">
                </div>
              </div>
              <div class="row">
                <div class="form-group mb-1 col-sm-6">
                  <label for="monthly_amortization">Monthly Amortization:</label>
                  <input type="text" class="form-control mt-n2" name="monthly_amortization" id="monthly_amortization" placeholder="Monthly Amortization" autocomplete="off" readonly>
                </div>
                <div class="form-group mb-1 col-sm-6">
                  <label for="terms_of_payment">Terms of Payment(Months):</label>
                  <select name="terms_of_payment" id="terms_of_payment" class="form-control mt-n2">
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
                <div class="form-group mb-1 col-sm-6">
                  <label for="downpayment_amount">Downpayment Amount:</label>
                  <input type="text" class="form-control mt-n2" name="downpayment_amount" id="downpayment_amount" placeholder="Downpayment Amount" autocomplete="off">
                </div>
                <div class="form-group mb-1 col-sm-6">
                  <label for="downpayment_date">Downpayment Date:</label>
                  <input type="text" class="form-control mt-n2 datetimepicker-input" name="downpayment_date" id="downpayment_date" data-toggle="datetimepicker" data-target="#downpayment_date" placeholder="Downpayment Date" autocomplete="off">
                </div>
              </div>
              <div class="row">
                <div class="form-group mb-1 col-sm-6">
                  <label for="downpayment_type">Downpayment Type:</label>
                  <select name="downpayment_type" id="downpayment_type" class="form-control mt-n2">
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
                <div class="form-group mb-1 col-sm-6">
                  <label for="downpayment_due_date">Downpayment Due Date:</label>
                  <input type="text" class="form-control mt-n2 datetimepicker-input" name="downpayment_due_date" id="downpayment_due_date" data-toggle="datetimepicker" data-target="#downpayment_due_date" placeholder="Downpayment Due Date" autocomplete="off">
                </div>
              </div>
              <div class="row">
                <div class="form-group mb-1 col-sm-6">
                  <label for="sales_coordinator">Sales Coordinator:</label>
                  <input type="text" class="form-control mt-n2" name="sales_coordinator" id="sales_coordinator" placeholder="Sales Coordinator" autocomplete="off">
                </div>
                <div class="form-group mb-1 col-sm-6">
                  <label for="assisting_coordinator">Assisting Coordinator:</label>
                  <input type="text" class="form-control mt-n2" name="assisting_coordinator" id="assisting_coordinator" placeholder="Assisting Coordinator" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
                <label for="property_remarks">Remarks:</label>
                <textarea class="form-control mt-n2" name="property_remarks" id="property_remarks" rows="3" placeholder="Remarks"></textarea>
              </div>
            </div>
            <div class="modal-footer px-2 py-1">
              <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary btn-flat btn-sm">Add Property</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Add Payment -->
    <div class="modal fade" id="addPaymentModal" data-backdrop="static" role="dialog" aria-labelledby="addPaymentModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <h5 class="modal-title" id="addPaymentModalTitle">Add Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
            </button>
          </div>
          <form id="addPaymentForm" onsubmit="handleAddPayment(event)">
            <div class="modal-body pb-0">
              <input type="hidden" id="payment_client_id" name="client_id">
              <div class="row">
                <div class="col-sm-5">
                  <div class="card rounded-0">
                    <div class="card-body p-3 pb-0">
                      <div class="form-group mb-1">
                        <label for="property">Property:</label>
                        <select name="property" id="property" class="form-control mt-n2 select2-get-properties" data-placeholder="Project Name" onchange="handleShowPaymentHistory(this.value)">
                          <option value>Choose Property</option>
                        </select>
                      </div>
                      <div class="form-group mb-1">
                        <label for="payment_date">Payment Date:</label>
                        <input type="text" class="form-control mt-n2 datetimepicker-input" name="payment_date" id="payment_date" data-toggle="datetimepicker" data-target="#payment_date" placeholder="Payment Date" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="payment_amount">Payment Amount:</label>
                        <input type="text" class="form-control mt-n2" name="payment_amount" id="payment_amount" placeholder="Payment Amount" autocomplete="off">
                      </div>
                      <div class="form-group mb-1">
                        <label for="payment_type">Payment Type:</label>
                        <select name="payment_type" id="payment_type" class="form-control mt-n2">
                          <option value>Choose Payment Type</option>
                          <?php
                            foreach ($paymentTypes as $key => $value) {
                              echo '<option value="' . $value . '">' .
                                PaymentTypes::getTextValue($value) .
                              '</option>';
                            }
                          ?>
                        </select>
                      </div>
                      <div class="form-group mb-1">
                        <!-- <label for="payment_due_date">Due Date:</label> -->
                        <input type="hidden" class="form-control mt-n2 datetimepicker-input" name="payment_due_date" id="payment_due_date" data-toggle="datetimepicker" data-target="#payment_due_date" placeholder="Payment Due Date">
                      </div>
                      <div class="form-group mb-1">
                        <label for="payment_remarks">Remarks:</label>
                        <textarea class="form-control mt-n2" name="payment_remarks" id="payment_remarks" rows="2" placeholder="Remarks"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-7">
                  <fieldset class="border mt-n2">
                    <legend  class="ml-1 w-auto text-md font-weight-bold">&nbsp;Payment Summary:&nbsp;</legend>
                    <div class="card m-0 shadow-none">
                      <div class="card-body px-2 py-0 m-0 text-sm" style="min-height: 40px; max-height: 427px; overflow-y: auto;">
                        <div id="paymentHistory"></div>
                      </div>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
            <div class="modal-footer px-2 py-1">
              <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary btn-flat btn-sm">Add Payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- SOA Details -->
    <div class="modal fade" id="generateSOAModal" data-backdrop="static" role="dialog" aria-labelledby="generateSOAModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <h5 class="modal-title" id="generateSOAModalTitle">Generate PDF</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle"></span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 250px;">
            <div class="table-responsive text-sm">
              <table class="table table-hover" id="propertiesTable" style="width: 100% !important;">
                <thead>
                  <tr>
                    <th style="min-width: 1px !important;">ID</th>
                    <th style="min-width: 120px !important;">Name</th>
                    <th style="min-width: 200px !important;">Location</th>
                    <th style="min-width: 1px !important;">Phase/Block/Lot</th>
                    <th style="min-width: 80px !important;" class="text-center">Setting</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div class="modal-footer px-2 py-1">
            <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  <?php require_once('includes/footer.php') ?>
  <script src="/assets/pages/client.js?ver=<?= $version ?>"></script>
</html>
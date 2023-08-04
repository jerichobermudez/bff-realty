<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/PaymentTypes.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['editClient'])) {
    $clientId = intval($_POST['edit_client_id']);
    $propertyId = intval($_POST['edit_property_id']);
    $projectId = intval($_POST['edit_project_id']);

    if (!checkClient($conn, $clientId)) {
      echo getResponseStatus(500, $data);

      return;
    }

    $fields = validateFields($_POST, [
      'edit_firstname' => 'This field is required.',
      'edit_lastname' => 'This field is required.',
      'edit_address' => 'This field is required.',
      'edit_marital_status' => 'This field is required.'
    ]);

    $propertyFields = [];
    if ($propertyId) {
      $propertyFields = validateFields($_POST, [
        'edit_property_id' => 'This field is required.',
        'edit_project_location' => 'This field is required.',
        'edit_property_phase' => 'This field is required.',
        'edit_property_block' => 'This field is required.',
        'edit_property_lot' => 'This field is required.',
        'edit_property_lot_area' => 'This field is required.',
        'edit_property_price_per_sqm' => 'This field is required.',
        'edit_monthly_amortization' => 'This field is required.',
        'edit_terms_of_payment' => 'This field is required.',
        'edit_downpayment_amount' => 'This field is required.',
        'edit_downpayment_date' => 'This field is required.'
      ]);
    }

    $validations['fields'] = array_merge($fields, $propertyFields);
    $validations['exists'] = [];

    if (!empty($validations['fields'])) {
      echo getResponseStatus(400, $validations);

      return;
    }

    // Assign clients details
    $firstname = checkField($_POST['edit_firstname']);
    $middlename = checkField($_POST['edit_middlename']);
    $lastname = checkField($_POST['edit_lastname']);
    $address = checkField($_POST['edit_address']);
    $contactNo = checkField($_POST['edit_contact']);
    $email = checkField($_POST['edit_email']);
    $birthday = checkField($_POST['edit_birthday'], true);
    $maritalStatus = checkField($_POST['edit_marital_status']);
    $spouseName = checkField($_POST['edit_spouse_name']);
    $spouseContactNo = checkField($_POST['edit_spouse_contact']);
    $spouseEmail = checkField($_POST['edit_spouse_email']);
    // Assign clients employment details
    $companyName = checkField($_POST['edit_company_name']);
    $companyAddress = checkField($_POST['edit_company_address']);
    $companyContact = checkField($_POST['edit_company_contact']);
    $yearsWorked = checkField($_POST['edit_year_of_stay']);
    $position = checkField($_POST['edit_position']);
    $tin = checkField($_POST['edit_tin_id']);
    $sss = checkField($_POST['edit_sss_id']);
    $monthlySalary = checkField($_POST['edit_monthly_salary']);
    // Assign clients property details
    $phase = checkField($_POST['edit_property_phase']);
    $block = checkField($_POST['edit_property_block']);
    $lot = checkField($_POST['edit_property_lot']);
    $lotArea = checkField($_POST['edit_property_lot_area']);
    $pricePerSqm = checkField($_POST['edit_property_price_per_sqm']);
    $termsOfPayment = checkField($_POST['edit_terms_of_payment']);
    $downpaymentAmount = checkField($_POST['edit_downpayment_amount']);
    $downpaymentDate = checkField($_POST['edit_downpayment_date'], true);
    $downpaymentDueDate = checkField($_POST['edit_downpayment_due_date'], true);
    $salesCoordinator = checkField($_POST['edit_sales_coordinator']);
    $assistantCoordinator = checkField($_POST['edit_assisting_coordinator']);
    $remarks = checkField($_POST['edit_property_remarks']);
    
    if ($propertyId) {
      $qry = "SELECT id, name, location, code FROM tbl_projects WHERE id = ? LIMIT 1";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('i', $projectId);
      $stmt->bind_result($fetchedId, $projectName, $projectLocation, $propertyCode);
      $stmt->execute();
      $stmt->store_result();
      $projectCount = $stmt->num_rows();
      $stmt->fetch();
      $stmt->close();

      if ($projectCount <= 0) {
        echo getResponseStatus(500, $data);

        return;
      }

      $isPropertyExists = checkPropertyLotNumbers($conn, $fetchedId, $projectName, $projectLocation, $phase, $block, $lot, $propertyId);

      if ($isPropertyExists) {
        $validations['exists'] = 'Property already occupied.';
        echo getResponseStatus(400, $validations);

        return;
      }
    }

    // Update clients details
    $qry = 'UPDATE tbl_clients SET firstname = ?, middlename = ?, lastname = ?, address = ?, birthday = ?, contact_no = ?, email = ?, marital_status = ?, spouse_name = ?, spouse_contact_no = ?, spouse_email = ? WHERE id = ?';
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('sssssssssssi',
      $firstname,
      $middlename,
      $lastname,
      $address,
      $birthday,
      $contactNo,
      $email,
      $maritalStatus,
      $spouseName,
      $spouseContactNo,
      $spouseEmail,
      $clientId
    );
    $stmt->execute();
    $stmt->close();

    // Update clients employment details
    $qry = "UPDATE tbl_employment_details SET company_name = ?, company_address = ?, company_contact = ?, years_worked = ?, position = ?, tin_id = ?, sss_id = ?, monthly_salary = ? WHERE client_id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('sssissssi',
      $companyName,
      $companyAddress,
      $companyContact,
      $yearsWorked,
      $position,
      $tin,
      $sss,
      $monthlySalary,
      $clientId
    );
    $stmt->execute();
    $stmt->close();

    if ($propertyId) {
      $monthlyAmortization = computeMonthlyAmortization($lotArea, $pricePerSqm, $termsOfPayment, $downpaymentAmount);
      // Update clients property details
      $qry = 'UPDATE tbl_properties SET project_id = ?, project_name = ?, project_location = ?, phase = ?, block = ?, lot = ?, lot_area = ?, price_per_sqm = ?, monthly_amortization = ?, payment_terms = ?, downpayment_amount = ?, downpayment_date = ?, downpayment_due_date = ?, sales_coordinator = ?, assistant_coordinator = ?, remarks = ? WHERE id = ? AND client_id = ?';
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('ississississssssii',
        $fetchedId,
        $projectName,
        $projectLocation,
        $phase,
        $block,
        $lot,
        $lotArea,
        $pricePerSqm,
        $monthlyAmortization,
        $termsOfPayment,
        $downpaymentAmount,
        $downpaymentDate,
        $downpaymentDueDate,
        $salesCoordinator,
        $assistantCoordinator,
        $remarks,
        $propertyId,
        $clientId
      );
      $stmt->execute();
      $stmt->close();
    }

    $code = 200;
  }

  echo getResponseStatus($code, $data);

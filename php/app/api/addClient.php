<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/PaymentTypes.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['addClient'])) {
    // Generate customer code
    function generateCustomerCode($propertyCode = 'PLL', $lastCustomerNo = null) {
      $currentYear = date('y');
      $currentMonthDate = date('md');
      
      $initialCustomerCode = $currentYear . $propertyCode . $currentMonthDate;

      $customerCode = $initialCustomerCode . '01';

      if ($lastCustomerNo !== null) {
        $lastSequel = str_replace($initialCustomerCode, '', $lastCustomerNo);
        $customerCode = $initialCustomerCode . str_pad((int)substr($lastSequel, -2) + 1, 2, '0', STR_PAD_LEFT);
      }

      return $customerCode;
    }

    $fields = validateFields($_POST, [
      'client_entry_date' => 'This field is required.',
      'client_firstname' => 'This field is required.',
      'client_lastname' => 'This field is required.',
      'client_address' => 'This field is required.',
      'marital_status' => 'This field is required.',
      'project_name' => 'This field is required.',
      'project_location' => 'This field is required.',
      'property_phase' => 'This field is required.',
      'property_block' => 'This field is required.',
      'property_lot' => 'This field is required.',
      'property_lot_area' => 'This field is required.',
      'property_price_per_sqm' => 'This field is required.',
      'monthly_amortization' => 'This field is required.',
      'terms_of_payment' => 'This field is required.',
      'downpayment_amount' => 'This field is required.',
      'downpayment_type' => 'This field is required.',
      'downpayment_date' => 'This field is required.',
    ]);

    $validations['fields'] = $fields;

    $propertyId= $_POST['project_name'];
    
    $qry = "SELECT id, name, location, code FROM tbl_projects WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $propertyId);
    $stmt->bind_result($fetchedId, $propertyName, $propertyLocation, $propertyCode);
    $stmt->execute();
    $stmt->store_result();
    $projectCount = $stmt->num_rows();
    $stmt->fetch();
    $stmt->close();

    if ($projectCount <= 0 && empty($validations['fields'])) {
      $validations['fields']['project_name'] = 'invalid value.';
    }

    // Assign clients property details
    $projectName = $propertyName;
    $projectLocation = checkField($_POST['project_location']);
    $phase = checkField($_POST['property_phase']);
    $block = checkField($_POST['property_block']);
    $lot = checkField($_POST['property_lot']);
    $lotArea = checkField($_POST['property_lot_area']);
    $pricePerSqm = checkField($_POST['property_price_per_sqm']);
    $monthlyAmortization = checkField($_POST['monthly_amortization']);
    $termsOfPayment = checkField($_POST['terms_of_payment']);
    $downpaymentAmount = checkField($_POST['downpayment_amount']);
    $downpaymentType = checkField($_POST['downpayment_type']);
    $downpaymentDate = checkField($_POST['downpayment_date'], true);
    $downpaymentDueDate = checkField($_POST['downpayment_due_date'], true);
    $salesCoordinator = checkField($_POST['sales_coordinator']);
    $assistantCoordinator = checkField($_POST['assisting_coordinator']);
    $remarks = checkField($_POST['remarks']);

    $isPropertyExists = checkPropertyLotNumbers($conn, $fetchedId, $projectName, $projectLocation, $phase, $block, $lot);

    $validations['exists'] = [];
    if ($isPropertyExists && empty($validations['fields'])) {
      $code = 409;
      $validations['exists'] = 'Property already occupied.';
      $data = $validations;
    } elseif (!empty($validations['fields']) || !empty($validations['exists'])) {
      $code = 400;
      $data = $validations;
    } else {
      // Get last customer code in current date if any
      $searchCustomerNo = date('y') . $propertyCode . date('md') . '%';
      $qry = "SELECT customer_no FROM tbl_clients WHERE customer_no LIKE ? ORDER BY customer_no DESC LIMIT 1";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('s', $searchCustomerNo);
      $stmt->bind_result($lastCustomerNo);
      $stmt->execute();
      $stmt->store_result();
      $searchResult = $stmt->num_rows;
      $stmt->fetch();
      $stmt->close();

      $dateEntry = date('Y-m-d', strtotime($_POST['client_entry_date']));
      $customerNo = generateCustomerCode($propertyCode, $lastCustomerNo ?? null);
      $firstname = $_POST['client_firstname'];
      $middlename = checkField($_POST['client_middlename']);
      $lastname = $_POST['client_lastname'];
      $address = checkField($_POST['client_address']);
      $birthday = checkField($_POST['client_birthday'], true);
      $contactNo = checkField($_POST['client_contact']);
      $email = checkField($_POST['client_email']);
      $maritalStatus = checkField($_POST['marital_status']);
      $spouseName = checkField($_POST['spouse_name']);
      $spouseContactNo = checkField($_POST['spouse_contact']);
      $spouseEmail = checkField($_POST['spouse_email']);

      // Save to database
      $qry = 'INSERT INTO tbl_clients (date_entry, customer_no, firstname, middlename, lastname, address, birthday, contact_no, email, marital_status, spouse_name, spouse_contact_no, spouse_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sssssssssssss',
        $dateEntry,
        $customerNo,
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
        $spouseEmail
      );
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();
      $lastInsertedId = $conn->insert_id;

      // Insert other details if success insert client's data
      $code = 500;
      if ($affectedRows > 0) {
        // Assign clients employment details
        $companyName = checkField($_POST['company_name']);
        $companyAddress = checkField($_POST['company_address']);
        $companyContact = checkField($_POST['company_contact']);
        $yearsWorked = checkField($_POST['year_of_stay']);
        $position = checkField($_POST['position']);
        $tin = checkField($_POST['tin_id']);
        $sss = checkField($_POST['sss_id']);
        $monthlySalary = checkField($_POST['monthly_salary']);

        // Insert clients employment details
        $qry = 'INSERT INTO tbl_employment_details (client_id, company_name, company_address, company_contact, years_worked, position, tin_id, sss_id, monthly_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('isssissss',
          $lastInsertedId,
          $companyName,
          $companyAddress,
          $companyContact,
          $yearsWorked,
          $position,
          $tin,
          $sss,
          $monthlySalary
        );
        $stmt->execute();
        $stmt->close();

        $monthlyAmortization = computeMonthlyAmortization($lotArea, $pricePerSqm, $termsOfPayment, $downpaymentAmount);

        // Insert clients property details
        $qry = 'INSERT INTO tbl_properties (client_id, project_id, project_name, project_location, phase, block, lot, lot_area, price_per_sqm, monthly_amortization, payment_terms, downpayment_amount, downpayment_date, downpayment_due_date, sales_coordinator, assistant_coordinator, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('iississississssss',
          $lastInsertedId,
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
          $remarks
        );
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $lastInsertedPropertyId = $conn->insert_id;

        if ($affectedRows > 0) {
          $referenceNo = generateReferenceNo($conn, $downpaymentDate);
          $paymentType = PaymentTypes::getTextValue($downpaymentType);

          $qry = "INSERT INTO tbl_payments (client_id, property_id, reference_no, payment_amount, payment_date, type, payment_type, payment_remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($qry);
          $stmt->bind_param('iiississ', $lastInsertedId, $lastInsertedPropertyId, $referenceNo, $downpaymentAmount, $downpaymentDate, $downpaymentType, $paymentType, $remarks);
          $stmt->execute();
          $affectedRows = $stmt->affected_rows;
          $stmt->close();
          $code = $affectedRows > 0 ? 201 : 501;
        }
      }
    }
  }

  echo getResponseStatus($code, $data);

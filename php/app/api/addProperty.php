<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/PaymentTypes.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];
  
  if (isset($_POST['addProperty'])) {
    $validations = [];

    $fields = validateFields($_POST, [
      'property_project_name' => 'This field is required.',
      'property_project_location' => 'This field is required.',
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

    $clientId = intval($_POST['client_id']);
    $projectId = intval($_POST['property_project_name']);
    $projectLocation = checkField($_POST['property_project_location']);
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
    $assistingCoordinator = checkField($_POST['assisting_coordinator']);
    $remarks = checkField($_POST['property_remarks']);

    $qry = "SELECT id, name, location FROM tbl_projects WHERE id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $projectId);
    $stmt->bind_result($fetchedId, $fetchedName, $fetchedLocation);
    $stmt->execute();
    $stmt->store_result();
    $projectCount = $stmt->num_rows();
    $stmt->fetch();
    $stmt->close();

    if ($projectCount <= 0 && empty($validations['fields'])) {
      $validations['fields']['property_project_name'] = 'invalid value.';
    }

    $isPropertyExists = checkPropertyLotNumbers($conn, $fetchedId, $fetchedName, $fetchedLocation, $phase, $block, $lot);

    $validations['exists'] = [];
    if ($isPropertyExists && empty($validations['fields'])) {
      $code = 409;
      $validations['exists'] = 'Property already occupied.';
      $data = $validations;
    } elseif (!empty($validations['fields']) || !empty($validations['exists'])) {
      $code = 400;
      $data = $validations;
    } else {
      $totalMonthlyAmortization = computeMonthlyAmortization($lotArea, $pricePerSqm, $termsOfPayment, $downpaymentAmount);

      $qry = "INSERT INTO tbl_properties (client_id, project_id, project_name, project_location, phase, block, lot, lot_area, price_per_sqm, monthly_amortization, payment_terms, downpayment_amount, downpayment_date, downpayment_due_date, sales_coordinator, assistant_coordinator, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('iississssssssssss',
        $clientId,
        $fetchedId,
        $fetchedName,
        $fetchedLocation,
        $phase,
        $block,
        $lot,
        $lotArea,
        $pricePerSqm,
        $totalMonthlyAmortization,
        $termsOfPayment,
        $downpaymentAmount,
        $downpaymentDate,
        $downpaymentDueDate,
        $salesCoordinator,
        $assistingCoordinator,
        $remarks
      );
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();
      $lastInsertedId = $conn->insert_id;

      $code = 500;
      if ($affectedRows > 0) {
        $referenceNo = generateReferenceNo($conn, $downpaymentDate);
        $paymentType = PaymentTypes::getTextValue($downpaymentType);

        $qry = "INSERT INTO tbl_payments (client_id, property_id, reference_no, payment_amount, payment_date, type, payment_type, payment_remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('iiississ', $clientId, $lastInsertedId, $referenceNo, $downpaymentAmount, $downpaymentDate, $downpaymentType, $paymentType, $remarks);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        $code = $affectedRows > 0 ? 201 : 501;
      }
    }
  }

  echo getResponseStatus($code, $data);

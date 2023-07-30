<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/PaymentTypes.php');
  require_once('../enums/PropertyStatus.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['addPayment'])) {
    $validations = validateFields($_POST, [
      'property' => 'This field is required.',
      'payment_date' => 'This field is required.',
      'payment_amount' => 'This field is required.',
      'payment_type' => 'This field is required.',
    ]);

    $clientId = intval($_POST['client_id']);
    $propertyId = intval($_POST['property']);
    $paymentDate = checkField($_POST['payment_date'], true);
    $paymentAmount = str_replace(',', '', checkField($_POST['payment_amount']));
    $type = checkField($_POST['payment_type']);
    $paymentType = PaymentTypes::getTextValue(intval($type));
    $paymentDueDate = checkField($_POST['payment_due_date'], true);
    $remarks = checkField($_POST['payment_remarks']);
    $paymentAmount = $paymentAmount ? str_replace(',', '', $paymentAmount) : 0;

    $qry = "SELECT id, status FROM tbl_properties WHERE client_id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids = [];
    $property = [];
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
        $property[$row['id']] = $row['status'];
      }
    }
    $stmt->close();

    if (isset($property[$propertyId])) {
      if ($property[$propertyId] === PropertyStatus::FULLY_PAID) {
        $data = ['status' => 'Property already paid.'];
        echo getResponseStatus(500, $data);

        return;
      }
    }

    if (!array_key_exists('payment_amount', $validations)) {
      if (!is_numeric($paymentAmount)) {
        $validations['payment_amount']['invalid'] = true;
      }

      if ($paymentAmount <= 0) {
        $validations['payment_amount']['min'] = true;
      }
    }

    $paymentHistory = getPaymentHistory($conn, $propertyId);
    $propertyDetails = getPropertyDetails($conn, $propertyId);
    $lotArea = $propertyDetails['lot_area'] ?? 0;
    $pricePerSqm = $propertyDetails['price_per_sqm'] ?? 0;
    $total = $paymentHistory['total'] ?? 0;
    $lotArea = $lotArea ? str_replace(',', '', $lotArea) : 0;
    $pricePerSqm = $pricePerSqm ? str_replace(',', '', $pricePerSqm) : 0;
    $totalPropertyPrice = $lotArea * $pricePerSqm;
    $remainingBalance = $totalPropertyPrice - str_replace(',', '', $total);

    if ($remainingBalance > 0) {
      if ($paymentAmount > $remainingBalance && is_numeric($paymentAmount)) {
        $validations['payment_amount']['max'] = true;
      }

      if (!in_array($propertyId, $ids) && $propertyId === '') {
        $validations['project_name'] = 'invalid value.';
      }
    } else {
      if (empty($validations)) {
        $data = ['status' => 'Property already paid.'];
        echo getResponseStatus(500, $data);

        return;
      }
    }

    if (!empty($validations)) {
      $code = 400;
      $data = $validations;
    } else {
      $downpaymentTypes = [
        PaymentTypes::DOWNPAYMENT,
        PaymentTypes::HOLDING_FEE,
        PaymentTypes::PARTIAL_IN_DOWNPAYMENT
      ];

      if (in_array($type, $downpaymentTypes)) {
        $placeholders = implode(', ', array_fill(0, count($downpaymentTypes), '?'));
        $dpTypeParams = str_repeat('i', count($downpaymentTypes));

        $qry = "SELECT SUM(payment_amount) AS total_payment FROM tbl_payments WHERE client_id = ? AND property_id = ? AND type IN ($placeholders)";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('ii' . $dpTypeParams, $clientId, $propertyId, ...$downpaymentTypes);
        $stmt->bind_result($totalPaymentAmount);
        $stmt->execute();
        $stmt->store_result();
        $resultCount = $stmt->num_rows;
        $stmt->fetch();
        $stmt->close();

        if ($resultCount > 0) {
          $totalDPAmount = $totalPaymentAmount + $paymentAmount;
          $qry = "UPDATE tbl_properties SET downpayment_amount = ? WHERE id = ? AND client_id = ?";
          $stmt = $conn->prepare($qry);
          $stmt->bind_param('sii', $totalDPAmount, $propertyId, $clientId);
          $stmt->execute();
          $stmt->close();
        }
      }

      $referenceNo = generateReferenceNo($conn, $paymentDate);

      $qry = "INSERT INTO tbl_payments (client_id, property_id, reference_no, payment_amount, payment_date, payment_due_date, type, payment_type, payment_remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('iiisssiss', $clientId, $propertyId, $referenceNo, $paymentAmount, $paymentDate, $paymentDueDate, $type, $paymentType, $remarks);
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();

      $code = 500;
      if ($affectedRows > 0) {
        if ((float) $remainingBalance === (float) $paymentAmount) {
          $status = PropertyStatus::FULLY_PAID;
          $qry = "UPDATE tbl_properties SET status = ? WHERE id = ?";
          $stmt = $conn->prepare($qry);
          $stmt->bind_param('ii', $status, $propertyId);
          $stmt->execute();
          $stmt->close();
        }

        $code = 201;
      }

      // $code = $affectedRows > 0 ? 201 : 500;
      $data = ['property_id' => $propertyId];
    }
  }

  echo getResponseStatus($code, $data);

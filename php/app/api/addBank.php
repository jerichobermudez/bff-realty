<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];
  
  if (isset($_POST['addBank'])) {
    $validations = validateFields($_POST, [
      'bank_name' => 'This field is required.'
    ]);

    $bankName = checkField($_POST['bank_name']);

    $qry = "SELECT id FROM tbl_banks WHERE name = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $bankName);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['bank_name'] = 'already exists.';
    }

    if (!empty($validations)) {
      $code = 400;
      $data = $validations;
    } else {
      $qry = "INSERT INTO tbl_banks (name) VALUES (?)";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('s', $bankName);
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();

      $code = $affectedRows > 0 ? 201 : 500;
    }
  }

  echo getResponseStatus($code, $data);

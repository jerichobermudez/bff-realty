<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['bank_id'])) {
    $bankId = intval($_GET['bank_id']);
    $qry = "SELECT id, name FROM tbl_banks WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $bankId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->store_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($result->num_rows > 0) {
      $code = 200;
      $data = $row;
    } else {
      $code = 404;
    }
  }

  if (isset($_POST['editBank'])) {
    $validations = validateFields($_POST, [
      'bank_name' => 'This field is required.'
    ]);

    $bankId = intval($_POST['bank_id']);
    $bankName = checkField($_POST['bank_name']);

    $qry = "SELECT id FROM tbl_banks WHERE id != ? AND name = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('is', $bankId, $bankName);
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
      $qry = "UPDATE tbl_banks SET name = ? WHERE id = ?";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('si', $bankName, $bankId);
      $stmt->execute();
      $stmt->close();

      $code = 200;
    }
  }

  echo getResponseStatus($code, $data);

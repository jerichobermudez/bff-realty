<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['generateVoucher'])) {
    $maxAttempts = 10;
    $voucher = generateVoucherCode($conn);

    $qry = "SELECT id FROM tbl_vouchers WHERE code = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $voucher);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows();
    $stmt->close();

    $attempts = 0;
    while ($count > 0 && $attempts < $maxAttempts) {
      $voucher = '';
      for ($i = 0; $i < $length; $i++) {
        $voucher .= $characters[rand(0, strlen($characters) - 1)];
      }
      $attempts++;
    }
    if ($attempts >= $maxAttempts) {
      echo getResponseStatus(500, $data);

      return;
    }

    $qry = "INSERT INTO tbl_vouchers (code, expiration) VALUES (?, DATE_ADD(NOW(), INTERVAL 3 HOUR))";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $voucher);
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    $code = $affectedRows > 0 ? 200 : 500;
  }

  echo getResponseStatus($code, $data);

  return;

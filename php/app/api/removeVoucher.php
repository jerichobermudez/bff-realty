<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['removeVoucher'])) {
    $voucherId = intval($_POST['voucher_id']);

    $qry = "SELECT id FROM tbl_vouchers WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $voucherId);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result === 0) {
      echo getResponseStatus(500, $data);

      return;
    }

    $qry = "DELETE FROM tbl_vouchers WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $voucherId);
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    $code = $affectedRows > 0 ? 200 : 500;
  }

  echo getResponseStatus($code, $data);

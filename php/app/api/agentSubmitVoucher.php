<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/UserRole.php');
  require_once('../enums/VoucherStatus.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['submitVoucher'])) {
    $code = $_POST['voucher_code'];
    $userId = $_POST['user_id'];

    $validations = validateFields($_POST, [
      'voucher_code' => 'Voucher Code is required.',
      'user_id' => 'Please choose agent.'
    ]);

    if (!array_key_exists('voucher_code', $validations)) {
      $statusOpen = VoucherStatus::AVAILABLE;
      $qry = "SELECT id, code FROM tbl_vouchers WHERE BINARY code = ? AND status = ? AND expiration > NOW() LIMIT 1";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('si', $code, $statusOpen);
      $stmt->bind_result($voucherId, $voucherCode);
      $stmt->execute();
      $stmt->store_result();
      $count = $stmt->num_rows;
      $stmt->fetch();
      $stmt->close();

      if ($count === 0) {
        $validations['voucher_code'] = 'Invalid voucher code.';
      }
    }
    
    if (!empty($validations)) {
      echo getResponseStatus(400, $validations);

      return;
    }

    $agentRole = UserRole::AGENT;
    $qry = "SELECT id, name FROM tbl_users WHERE id = ? AND role = ? AND status != 0";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('ii', $userId, $agentRole);
    $stmt->bind_result($agentId, $agentName);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->fetch();
    $stmt->close();

    if ($count === 0) {
      echo getResponseStatus(404, ['user_id' => 'Invalid agent user.']);

      return;
    }

    $statusWIP = VoucherStatus::NOT_AVAILABLE;
    $qry = "UPDATE tbl_vouchers SET user_id = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('iii', $statusWIP, $userId, $voucherId);
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    $code = 500;
    if ($affectedRows > 0) {
      session_start();
      $_SESSION['agent'] = true;
      $_SESSION['agent_id'] = $agentId;
      $_SESSION['agent_name'] = $agentName;
      $_SESSION['voucher_id'] = $voucherId;
      $_SESSION['voucher_code'] = $voucherCode;
      $code = 200;
    }
  }

  echo getResponseStatus($code, $data);

  return;

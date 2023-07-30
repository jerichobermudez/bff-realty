<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_POST['activateDeactivate'])) {
    $userId = intval($_POST['user_id']);
    $status = intval($_POST['status']);

    $qry = "SELECT id FROM tbl_users WHERE id = ? AND status = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('ii', $userId, $status);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result === 0) {
      echo getResponseStatus(500, $data);

      return;
    }

    $status = $status === 1 ? 0 : 1;
    $qry = "UPDATE tbl_users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('ii', $status, $userId);
    $stmt->execute();
    $stmt->close();

    $code = 200;
  }

  echo getResponseStatus($code, $data);

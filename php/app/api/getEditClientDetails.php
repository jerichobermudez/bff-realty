<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['client_id'])) {
    $clientId = intval($_GET['client_id']);

    $data = getClientDetails($conn, $clientId);

    echo getResponseStatus(200, $data);

    return;
  }

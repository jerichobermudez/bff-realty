<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['property_id'])) {
    $propertyId = intval($_GET['property_id']);
    
    $propertyDetails = getPropertyDetails($conn, $propertyId);
    
    if (count($propertyDetails) <= 0) {
      echo getResponseStatus(404, $data);

      return;
    }

    $data = $propertyDetails;

    echo getResponseStatus(200, $data);

    return;
  }

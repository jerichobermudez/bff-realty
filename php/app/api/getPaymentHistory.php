<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['property_id'])) {
    $propertyId = intval($_GET['property_id']);

    $data = getPaymentHistory($conn, $propertyId);
    $propertyDetails = getPropertyDetails($conn, $propertyId);
    
    $lotArea = $propertyDetails['lot_area']
      ? str_replace(',', '', $propertyDetails['lot_area'])
      : 0;
    $pricePerSqm = $propertyDetails['price_per_sqm']
      ? str_replace(',', '', $propertyDetails['price_per_sqm'])
      : 0;
    $totalPropertyPrice = $lotArea * $pricePerSqm;
    $remainingBalance = $totalPropertyPrice - str_replace(',', '', $data['total']);
    $data['total_contract_price'] = number_format((float) $totalPropertyPrice, 2, '.', ',');
    $data['remaining_balance'] = number_format((float) $remainingBalance, 2, '.', ',');

    $code = count($data) > 0 ? 200 : 404;
  }

  echo getResponseStatus($code, $data);

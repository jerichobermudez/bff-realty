<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];
  
  if (isset($_GET['client_id'])) {
    $clientId = intval($_GET['client_id']);
    $searchKey = '%%';
    if (isset($_GET['search'])) {
      $searchKey = '%'. $_GET['search'] .'%';
    }

    $qry = "SELECT id, project_id, project_name, phase, block, lot
      FROM tbl_properties 
      WHERE
        client_id = ?
        AND (
          project_name LIKE ?
          OR project_location LIKE ?
          OR phase LIKE ?
          OR block LIKE ?
          OR lot LIKE ?
        )
      ORDER BY project_name ASC";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('isssss', $clientId, $searchKey, $searchKey, $searchKey, $searchKey, $searchKey);
    $stmt->execute();
    $result = $stmt->get_result();

    $results = [];
    while ($row = $result->fetch_assoc()) {
      $propertyName = $row['project_name'] . ' (Blk. ' . $row['block'] . ' Lot. ' . $row['lot'] .')';
      $results[] = [
        'id' => $row['id'],
        'text' => $propertyName
      ];
    }
    $stmt->close();

    $code = 200;
    $data['results'] = $results;
  }

  echo getResponseStatus($code, $data);

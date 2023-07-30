<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');

  $searchKey = '%%';
  if (isset($_GET['search'])) {
    $searchKey = '%'. $_GET['search'] .'%';
  }

  $qry = "SELECT id, name, location FROM tbl_projects WHERE name LIKE ? AND status = 1";
  $stmt = $conn->prepare($qry);
  $stmt->bind_param('s', $searchKey);
  $stmt->execute();
  $result = $stmt->get_result();

  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'id' => $row['id'],
      'text' => $row['name'],
      'location' => $row['location']
    ];
  }
  $stmt->close();

  $jsonData = getResponseStatus(200, ['results' => $data]);

  echo $jsonData;
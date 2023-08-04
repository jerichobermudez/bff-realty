<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../enums/UserRole.php');
  header('Content-Type: application/json');

  $searchKey = '%%';
  if (isset($_GET['search'])) {
    $searchKey = '%'. $_GET['search'] .'%';
  }
  $agentId = null;
  if (isset($_GET['agent_id'])) {
    $agentId = intval($_GET['agent_id']);
  }

  $roleAgent = UserRole::AGENT;

  $qry = "SELECT id, name FROM tbl_users WHERE name LIKE ? AND role = ?";
  $stmt = $conn->prepare($qry);
  $stmt->bind_param('si', $searchKey, $roleAgent);
  $stmt->execute();
  $result = $stmt->get_result();

  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'id' => $row['id'],
      'text' => $row['name'],
      'selected' => $row['id'] === $agentId ? true : false
    ];
  }
  $stmt->close();

  $jsonData = getResponseStatus(200, ['results' => $data]);

  echo $jsonData;
<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/common.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['client_id'])) {
    $clientID = intval($_GET['client_id']);

    $data = getClientDetails($conn, $clientID);

    $qry = "SELECT * FROM tbl_properties WHERE client_id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['properties'] = [];
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $data['properties'][] = $row;
      }
    }
    $stmt->close();

    $htmlLayout = file_get_contents('../templates/client-details.html');

    $code = 200;
    $data = json_encode([
      'html' => replacePlaceholdersRecursive($htmlLayout, $data)
    ]);
  }

  echo getResponseStatus($code, $data);

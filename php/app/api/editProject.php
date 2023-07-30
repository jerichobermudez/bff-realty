<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['project_id'])) {
    $propertyId = $_GET['project_id'];
    $qry = "SELECT id, name, location, code FROM tbl_projects WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->store_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($result->num_rows > 0) {
      $code = 200;
      $data = $row;
    } else {
      $code = 404;
    }
  }

  if (isset($_POST['editProject'])) {
    $validations = validateFields($_POST, [
      'project_name' => 'This field is required.',
      'project_location' => 'This field is required.',
      'property_code' => 'This field is required.'
    ]);

    $projectId = $_POST['project_id'];
    $projectName = $_POST['project_name'];
    $projectLocation = $_POST['project_location'];
    $propertyCode = strtoupper($_POST['property_code']);

    $qry = "SELECT id FROM tbl_projects WHERE id != ? AND code = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('is', $projectId, $propertyCode);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['property_code'] = 'already used.';
    }

    if (!empty($validations)) {
      $code = 400;
      $data = $validations;
    } else {
      $qry = "UPDATE tbl_projects SET name = ?, location = ?, code = ? WHERE id = ?";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sssi', $projectName, $projectLocation, $propertyCode, $projectId);
      $stmt->execute();
      $stmt->close();

      $code = 200;
    }
  }

  echo getResponseStatus($code, $data);

<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];
  
  if (isset($_POST['addProject'])) {
    $validations = validateFields($_POST, [
      'project_name' => 'This field is required.',
      'project_location' => 'This field is required.',
      'property_code' => 'This field is required.'
    ]);

    $projectName = checkField($_POST['project_name']);
    $projectLocation = checkField($_POST['project_location']);
    $propertyCode = checkField($_POST['property_code']);

    $qry = "SELECT id FROM tbl_projects WHERE code = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $propertyCode);
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
      $qry = "INSERT INTO tbl_projects (name, location, code) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sss', $projectName, $projectLocation, $propertyCode);
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();

      $code = $affectedRows > 0 ? 201 : 500;
    }
  }

  echo getResponseStatus($code, $data);

<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/UserRole.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];
  
  if (isset($_POST['addUser'])) {
    $validations = validateFields($_POST, [
      'name' => 'This field is required.',
      'username' => 'This field is required.',
      'email' => 'This field is required.',
      'role' => 'This field is required.',
      'password' => 'This field is required.',
      'confirm_password' => 'This field is required.'
    ]);

    $name = checkField($_POST['name']);
    $username = checkField($_POST['username']);
    $email = checkField($_POST['email']);
    $contactNo = checkField($_POST['phone']);
    $role = checkField($_POST['role']);
    $password = checkField($_POST['password']);
    $confirmPassword = checkField($_POST['confirm_password']);

    $qry = "SELECT id FROM tbl_users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['username'] = 'already used.';
    }

    $qry = "SELECT id FROM tbl_users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['email'] = 'already used.';
    }

    if (!empty($role) && !in_array($role, $userRole)) {
      $validations['role'] = 'invalid value.';
    }

    if (!empty($password) && $password != $confirmPassword) {
      $validations['password'] = 'does not match.';
      $validations['confirm_password'] = 'does not match.';
    }

    if (!empty($validations)) {
      $code = 400;
      $data = $validations;
    } else {
      $password = md5($password);
      $qry = "INSERT INTO tbl_users (name, username, email, password, contact_no, role) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sssssi', $name, $username, $email, $password, $contactNo, $role);
      $stmt->execute();
      $affectedRows = $stmt->affected_rows;
      $stmt->close();

      $code = $affectedRows > 0 ? 201 : 500;
    }
  }

  echo getResponseStatus($code, $data);

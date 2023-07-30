<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../includes/validation.php');
  require_once('../enums/UserRole.php');
  header('Content-Type: application/json');
  $code = 403;
  $data = [];

  if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $qry = "SELECT id, name, username, email, contact_no, role FROM tbl_users WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $userId);
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

  if (isset($_POST['editUser'])) {
    $validations = validateFields($_POST, [
      'edit_name' => 'This field is required.',
      'edit_username' => 'This field is required.',
      'edit_email' => 'This field is required.',
      'edit_role' => 'This field is required.'
    ]);

    $userId = intval($_POST['user_id']);
    $name = checkField($_POST['edit_name']);
    $username = checkField($_POST['edit_username']);
    $email = checkField($_POST['edit_email']);
    $contactNo = checkField($_POST['edit_phone']);
    $role = checkField($_POST['edit_role']);
    $password = checkField($_POST['edit_password']);
    $confirmPassword = checkField($_POST['edit_confirm_password']);

    $qry = "SELECT id FROM tbl_users WHERE username = ? AND id != ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('si', $username, $userId);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['edit_username'] = 'already used.';
    }

    $qry = "SELECT id FROM tbl_users WHERE email = ? AND id != ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('si', $email, $userId);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows();
    $stmt->close();

    if ($result > 0) {
      $validations['edit_email'] = 'already used.';
    }

    if (!empty($role) && !in_array($role, $userRole)) {
      $validations['edit_role'] = 'invalid value.';
    }

    $isPasswordUpdate = true;
    if (!empty($password) && $password != $confirmPassword) {
      $validations['edit_password'] = 'does not match.';
      $validations['edit_confirm_password'] = 'does not match.';
      $isPasswordUpdate = false;
    }

    if (!empty($validations)) {
      $code = 400;
      $data = $validations;
    } else {
      $qry = "UPDATE tbl_users SET name = ?, username = ?, email = ?, contact_no = ?, role = ? WHERE id = ?";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('ssssii', $name, $username, $email, $contactNo, $role, $userId);
      $stmt->execute();
      $stmt->close();

      if ($isPasswordUpdate) {
        $password = md5($password);
        $qry = "UPDATE tbl_users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('si', $password, $userId);
        $stmt->execute();
        $stmt->close();
      }

      $code = 200;
    }
  }

  echo getResponseStatus($code, $data);

<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');

  if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordHash = md5($_POST['password']);
    $validation = [];

    if (empty($username)) {
      $validation['username'] = 'This field is required.';
    }
    
    if (empty($password)) {
      $validation['password'] = 'This field is required.';
    }

    if (!empty($validation)) {
      $jsonData = getResponseStatus(400, $validation);
    } else {
      $qry = "SELECT id, username FROM tbl_users WHERE password = ? AND status = 1 AND ( username = ? OR email = ? )";
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sss', $passwordHash, $username, $username);
      $stmt->bind_result($id, $userName);
      $stmt->execute();
      $stmt->store_result();
      $logcount = $stmt->num_rows;
      $stmt->fetch();
      $stmt->close();

      if ($logcount >= 1) {
        session_start();
        $_SESSION['clientmsaid'] = $id;
        $_SESSION['login'] = $username;

        $jsonData = getResponseStatus(200);
      } else {
        $jsonData = getResponseStatus(401);
      }
    }
  } else {
    $jsonData = getResponseStatus(401);
  }

  echo $jsonData;

  return;

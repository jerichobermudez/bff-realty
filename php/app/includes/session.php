<?php
    require_once('connection.php');
    require_once('enums/UserRole.php');
    
    if (!isset($_SESSION['clientmsaid']) || trim($_SESSION['clientmsaid']) == '') {
      header('location:/');
    }
    
    $sessionId = $_SESSION['clientmsaid'];

    $qry = "SELECT id, name, username, email, contact_no, role, date_created FROM tbl_users WHERE id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $sessionId);
    $stmt->bind_result($userId, $name, $username, $email, $contact, $role, $created);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();
    $stmt->close();
?>

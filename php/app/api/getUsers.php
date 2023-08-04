<?php
  session_start();
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../enums/UserRole.php');

  if (isset($_POST['draw'])) {
    $roleAdmin = UserRole::ADMIN;
    $column = ['id', 'name', 'username', 'email', 'role', ''];

    $qry = "SELECT id, name, username, email, role, status FROM tbl_users ";

    if ($_POST['search']['value'] != '') {
      $searchParam = '%' . $_POST['search']['value'] . '%';
      $qry .= ' WHERE
        role != ' . $roleAdmin . '
        AND (
          id LIKE "' . $searchParam . '" OR
          name LIKE "' . $searchParam . '" OR
          username LIKE "' . $searchParam . '" OR
          email LIKE "' . $searchParam . '" OR
          ( role = "' . UserRole::ADMIN . '" AND "' . UserRole::getTextValue(UserRole::ADMIN) . '" LIKE "' .$searchParam . '" )
          OR ( role = "' . UserRole::USER . '" AND "' . UserRole::getTextValue(UserRole::USER) . '" LIKE "' . $searchParam . '" )
        )';
    } else {
      $qry .= ' WHERE role != ' . $roleAdmin;
    }


    if (isset($_POST['order'])) {
      $columnIndex = $_POST['order']['0']['column'];
      $columnOrder = $_POST['order']['0']['dir'];
      $orderBy = $column[$columnIndex];
      $qry .= ' ORDER BY ' . $orderBy . ' ' . $columnOrder;
    } else {
      $qry .= ' ORDER BY id DESC';
    }

    $stmt = $conn->prepare($qry);
    $stmt->execute();
    $stmt->store_result();
    $totalFilteredRecords = $stmt->num_rows();
    $stmt->close();

    $qry1 = '';
    if ($_POST["length"] != -1) {
      $qry1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    $stmt = $conn->prepare($qry . $qry1);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    foreach($result as $row) {
      $subArray = [];
      $subArray[] = $row['id'];
      $subArray[] = $row['name'];
      $subArray[] = $row['username'];
      $subArray[] = $row['email'];
      $subArray[] = UserRole::getTextValue($row['role']);
      $subArray[] = "
        <div class='d-flex justify-content-center align-items-center'>
          <button class='btn btn-link btn-sm m-0' onclick='handleGetEditUser(" . $row['id'] . ")'>Edit</button> |
          <button class='btn btn-link btn-sm m-0' onclick='handleDeactivateUser(" . $row['id'] . ", " . $row['status'] . ")'>" . ($row['status'] === 1 ? 'Deactivate' : 'Activate') . "</button>
        </div>
      ";
      $data[] = $subArray;
    }

    // Total records in the table
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM tbl_users WHERE role != ?";
    $totalRecordsStmt = $conn->prepare($totalRecordsQuery);
    $totalRecordsStmt->bind_param('i', $roleAdmin);
    $totalRecordsStmt->execute();
    $totalRecordsResult = $totalRecordsStmt->get_result();
    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

    $output = [
      'draw' => intval($_POST['draw']),
      'recordsTotal' => intval($totalRecords),
      'recordsFiltered' => intval($totalFilteredRecords),
      'data' => $data
    ];

    echo json_encode($output);

    return;
  } else {
    echo getResponseStatus(401);

    return;
  }

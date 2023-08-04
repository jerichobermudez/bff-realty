<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');

  if (isset($_POST['draw'])) {
    $column = ['id', 'name', 'date_created', ''];

    $qry = "SELECT id, name, date_created FROM tbl_banks ";

    if ($_POST['search']['value'] != '') {
      $searchParam = '%' . $_POST['search']['value'] . '%';
      $qry .= ' WHERE 
        id LIKE "' . $searchParam . '" OR
        name LIKE "' . $searchParam . '" ';
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
      $subArray[] = date('F d, Y h:i:s A', strtotime($row['date_created']));
      $subArray[] = "
        <div class='d-flex justify-content-center align-items-center'>
          <button class='btn btn-link btn-sm m-0' onclick='handleGetEditBank(" . $row['id'] . ")'>Edit</button> |
          <button class='btn btn-link btn-sm m-0' onclick='handleDeleteBank(" . $row['id'] . ")'>Delete</button>
        </div>
      ";
      $data[] = $subArray;
    }

    // Total records in the table
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM tbl_projects";
    $totalRecordsStmt = $conn->prepare($totalRecordsQuery);
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

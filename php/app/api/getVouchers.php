<?php
  session_start();
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  require_once('../enums/VoucherStatus.php');

  if (isset($_POST['draw'])) {
    $column = ['voucher_id', 'code', 'name', 'date_added', 'expiration', 'voucher_status', ''];

    $qry = "SELECT V.id AS voucher_id, code, name, expiration, V.status AS voucher_status, V.date_created AS date_added
      FROM tbl_vouchers AS V
      LEFT JOIN tbl_users AS U ON U.id = V.user_id ";

    if ($_POST['search']['value'] != '') {
      $searchParam = '%' . $_POST['search']['value'] . '%';
      $qry .= ' WHERE
        (
          V.id LIKE "' . $searchParam . '" OR
          code LIKE "' . $searchParam . '" OR
          name LIKE "' . $searchParam . '" OR
          expiration LIKE "' . $searchParam . '" OR
          ( V.status = "' . VoucherStatus::AVAILABLE . '" AND "' . VoucherStatus::getTextValue(VoucherStatus::AVAILABLE) . '" LIKE "' .$searchParam . '" )
          OR ( V.status = "' . VoucherStatus::NOT_AVAILABLE . '" AND "' . VoucherStatus::getTextValue(VoucherStatus::NOT_AVAILABLE) . '" LIKE "' . $searchParam . '" )
        )';
    }

    if (isset($_POST['order'])) {
      $columnIndex = $_POST['order']['0']['column'];
      $columnOrder = $_POST['order']['0']['dir'];
      $orderBy = $column[$columnIndex];
      $qry .= ' ORDER BY ' . $orderBy . ' ' . $columnOrder;
    } else {
      $qry .= ' ORDER BY V.id DESC';
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
      $isExpired = time() > strtotime($row['expiration']);
      $textColor = '#f56954';
      $voucherStatus = $row['voucher_status'];
      if ($row['voucher_status'] === 1) {
        $textColor = $isExpired ? '#f39c12' : '#00a65a';
        $voucherStatus = $isExpired ? 3 : $voucherStatus;
      }
      $subArray = [];
      $subArray[] = $row['voucher_id'];
      $subArray[] = '<label class="text-md">' . $row['code'] . '<label>';
      $subArray[] = $row['name'] ?? '------';
      $subArray[] = date('Y-m-d h:i:s A', strtotime($row['date_added']));
      $subArray[] = date('Y-m-d h:i:s A', strtotime($row['expiration']));
      $subArray[] = '<span class="text-bold" style="color: '. $textColor .';">' . VoucherStatus::getTextValue($voucherStatus) . '</span>';
      $subArray[] = "
        <div class='text-center'>
          <button class='btn btn-link btn-sm m-0' onclick='handleRemoveVoucher(" . $row['voucher_id'] . ")'>Delete</button>
        </div>
      ";
      $data[] = $subArray;
    }

    // Total records in the table
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM tbl_vouchers";
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

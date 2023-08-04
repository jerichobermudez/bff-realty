<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');

  if (isset($_POST['draw'])) {
    $column = ['unique_id', 'customer_no', 'client_name', 'project_name', 'block_lot', 'lot_area', ''];

    $qry = "SELECT
        unique_id,
        customer_no,
        client_name,
        project_names,
        block_lots,
        lot_areas
      FROM (
        SELECT
          C.id AS unique_id,
          customer_no,
          CONCAT_WS(' ', TRIM(firstname), TRIM(middlename), TRIM(lastname)) AS client_name,
          GROUP_CONCAT(P.project_name SEPARATOR ', ') AS project_names,
          GROUP_CONCAT(CONCAT_WS(' ', P.block, P.lot) SEPARATOR ', ') AS block_lots,
          GROUP_CONCAT(P.lot_area SEPARATOR ', ') AS lot_areas
        FROM tbl_clients AS C
        LEFT JOIN tbl_properties AS P ON P.client_id = C.id
        GROUP BY C.id
      ) AS subquery";

    if ($_POST['search']['value'] != '') {
      $searchParam = '%' . $_POST['search']['value'] . '%';
      $qry .= ' WHERE
        unique_id LIKE "' . $searchParam . '" OR
        customer_no LIKE "' . $searchParam . '" OR
        client_name LIKE "' . $searchParam . '" OR
        project_names LIKE "' . $searchParam . '" OR
        block_lots LIKE "' . $searchParam . '" OR
        lot_areas LIKE "' . $searchParam . '" ';
    }

    if (isset($_POST['order'])) {
      $columnIndex = $_POST['order']['0']['column'];
      $columnOrder = $_POST['order']['0']['dir'];
      $orderBy = $column[$columnIndex];
      $qry .= ' ORDER BY ' . $orderBy . ' ' . $columnOrder;
    } else {
      $qry .= ' ORDER BY unique_id DESC';
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
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $projectNames = $row['project_names'];
        if (strlen($projectNames) > 30) {
          $projectNames = '<span title="' . $projectNames . '">' . substr($projectNames, 0, 30) . '...</span>';
        }
        $blockLots = $row['block_lots'];
        if (strlen($blockLots) > 30) {
          $blockLots = '<span title="' . $blockLots . '">' . substr($blockLots, 0, 30) . '...</span>';
        }
        $lotArea = $row['lot_areas'];
        if (strlen($lotArea) > 30) {
          $lotArea = '<span title="' . $lotArea . '">' . substr($lotArea, 0, 30) . '...</span>';
        }
        $subArray = [];
        $subArray[] = $row['unique_id'];
        $subArray[] = utf8_encode($row['customer_no']);
        $subArray[] = utf8_encode($row['client_name']);
        $subArray[] = $projectNames;
        $subArray[] = trim($row['block_lots']) ? $blockLots : '------';
        $subArray[] = $lotArea ?? '------';
        $subArray[] = "
          <div class='d-flex justify-content-center align-items-center gap-3'>
            <button type='button' class='btn btn-flat btn-primary btn-xs' data-toggle='tooltip' data-placement='left' title='Edit' onclick='handleGetEditClient(" . $row['unique_id'] . ")'>
              <span class='fa fa-edit fa-fw fa-sm'></span>
            </button>
            <button type='button' class='btn btn-flat btn-primary btn-xs' data-toggle='tooltip' data-placement='left' title='View' onclick='handleGetClientDetails(" . $row['unique_id'] . ")'>
              <span class='fa fa-eye fa-fw fa-sm'></span>
            </button>
            <div class='dropdown'>
              <button type='button' class='btn btn-flat btn-primary btn-xs dropdown-toggle' data-toggle='dropdown'>
                <span class='glyphicon glyphicon-option-vertical'></span>
              </button>
              <div class='dropdown-menu rounded-0 p-0 m-0' role='menu' style='margin-left: -135px !important;'>
                <li>
                  <a href='javascript:void(0)' class='pl-4 p-2' onclick='handleViewPropertyModal(" . $row['unique_id'] . ")'><span class='fa fa-fw fa-plus fa-sm mr-1'></span>Add Property</a>
                </li>
                <li>
                  <a href='javascript:void(0)' class='pl-4 p-2' onclick='handleViewPaymentModal(" . $row['unique_id'] . ")'><span class='fa fa-fw fa-plus fa-sm mr-1'></span>Add Payment</a>
                </li>
                <li>
                  <a href='javascript:void(0)' class='pl-4 p-2' onclick='handleViewSOAModal(" . $row['unique_id'] . ")'><span class='fa fa-fw fa-file-pdf fa-sm mr-1'></span>Generate PDF</a>
                </li>
              </div>
            </div>
          </div>
        ";
        $data[] = $subArray;
      }
    }
    $stmt->close();

    // Total records in the table
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM tbl_clients";
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

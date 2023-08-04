<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');

  if (isset($_POST['draw']) && isset($_POST['client_id'])) {
    $clientId = intval($_POST['client_id']);
    $column = ['unique_id', 'project_name', 'project_location', 'phase_block_lot'];

    $qry = "SELECT
        unique_id,
        project_name,
        project_location,
        phase_block_lot
      FROM (
        SELECT
          id AS unique_id,
          client_id AS client_id,
          project_name AS project_name,
          project_location AS project_location,
          CONCAT(TRIM(phase), '/', TRIM(block), '/', TRIM(lot)) AS phase_block_lot
        FROM tbl_properties WHERE client_id = ?
      ) AS subquery";
    
    if ($_POST['search']['value'] != '') {
      $searchParam = '%' . $_POST['search']['value'] . '%';
      $qry .= ' WHERE
        unique_id LIKE "' . $searchParam . '" OR
        project_name LIKE "' . $searchParam . '" OR
        project_location LIKE "' . $searchParam . '" OR
        phase_block_lot LIKE "' . $searchParam . '" ';
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
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $stmt->store_result();
    $totalFilteredRecords = $stmt->num_rows();
    $stmt->close();

    $qry1 = '';
    if ($_POST["length"] != -1) {
      $qry1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    $stmt = $conn->prepare($qry . $qry1);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    foreach($result as $row) {
      $subArray = [];
      $subArray[] = $row['unique_id'];
      $subArray[] = $row['project_name'];
      $subArray[] = $row['project_location'];
      $subArray[] = $row['phase_block_lot'] ?? '------';
      $subArray[] = "
        <div class='d-flex justify-content-center align-items-center' style='gap: 0.3rem;'>
          <div class='dropdown'>
            <button type='button' class='btn btn-flat btn-primary btn-xs dropdown-toggle' data-toggle='dropdown' aria-expanded='false' title='View'>
              <span class='fa fa-eye fa-fw fa-sm'></span>
            </button>
            <div class='dropdown-menu rounded-0 py-0' style='margin-left: -120px !important;'>
              <li>
                <a href='javascript:void(0)' class='p-0'>
                  <form action='/api/getStatementOfAccount' method='POST' target='_blank'>
                    <input type='hidden' name='property_id' value='" . $row['unique_id'] . "'>
                    <button class='btn btn-flat btn-link dropdown-item mt-0' name='view'>
                      <span class='fa fa-eye fa-fw fa-sm'></span> Statement Of Account
                    </button>
                  </form>
                </a>
              </li>
              <li>
                <a href='javascript:void(0)' class='p-0'>
                  <form action='/api/getPaymentSummary' method='POST' target='_blank'>
                    <input type='hidden' name='property_id' value='" . $row['unique_id'] . "'>
                    <button class='btn btn-flat btn-link dropdown-item mt-0' name='view'>
                      <span class='fa fa-eye fa-fw fa-sm'></span> Payment Summary
                    </button>
                  </form>
                </a>
              </li>
            </div>
          </div>

          <div class='dropdown'>
            <button type='button' class='btn btn-flat btn-primary btn-xs dropdown-toggle' data-toggle='dropdown' aria-expanded='false' title='Download'>
              <span class='fa fa-download fa-fw fa-sm'></span>
            </button>
            <div class='dropdown-menu rounded-0 py-0' style='margin-left: -148px !important;'>
              <li>
                <a href='javascript:void(0)' class='p-0'>
                  <form action='/api/getStatementOfAccount' method='POST' target='_blank'>
                    <input type='hidden' name='property_id' value='" . $row['unique_id'] . "'>
                    <button class='btn btn-flat btn-link dropdown-item mt-0' name='download'>
                      <span class='fa fa-download fa-fw fa-sm'></span> Statement Of Account
                    </button>
                  </form>
                </a>
              </li>
              <li>
                <a href='javascript:void(0)' class='p-0'>
                  <form action='/api/getPaymentSummary' method='POST' target='_blank'>
                    <input type='hidden' name='property_id' value='" . $row['unique_id'] . "'>
                    <button class='btn btn-flat btn-link dropdown-item mt-0' name='download'>
                      <span class='fa fa-download fa-fw fa-sm'></span> Payment Summary
                    </button>
                  </form>
                </a>
              </li>
            </div>
          </div>
        </div>
      ";
      $data[] = $subArray;
    }

    // Total records in the table
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM tbl_properties WHERE client_id = ?";
    $totalRecordsStmt = $conn->prepare($totalRecordsQuery);
    $totalRecordsStmt->bind_param('i', $clientId);
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

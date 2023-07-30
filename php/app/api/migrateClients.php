<?php
  require_once('../includes/connection.php');
  require_once('../includes/response.php');
  header('Content-Type: application/json');

  function insertClientsData($data) {
    global $conn;

    $qry = 'SELECT id, customer_no FROM tbl_clients WHERE customer_no = ? LIMIT 1';
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $data['Customer_No']);
    $stmt->bind_result($id, $customer_no);
    $stmt->execute();
    $stmt->store_result();
    $searchResult = $stmt->num_rows;
    $stmt->fetch();
    $stmt->close();

    $insertedData = [];
    if ($searchResult === 0) {
      $dateEntry = $data['Date_Entry'] == '0000-00-00' ? null : date('Y-m-d', strtotime($data['Date_Entry']));
      $customerNo = $data['Customer_No'];
      $firstname = $data['First_Name'];
      $middlename = $data['Middle_Name'] ?? null;
      $lastname = $data['Last_Name'];
      $address = $data['Addres'] ?? null;
      $birthday = $data['Birthday'] == '0000-00-00' ? null : date('Y-m-d', strtotime($data['Birthday']));
      $contactNo = $data['Contact_No'] ?? null;
      $email = $data['Email_Add'] ?? null;
      $maritalStatus = $data['Marital_Status'] ?? null;
      $spouseName = $data['Name_of_Spouse'] ?? null;
      $spouseContactNo = $data['Contact_No_of_Spouse'] ?? null;
      $spouseEmail = $data['Email_Add_of_Spouse'] ?? null;
      $companyName = $data['Name_of_Company'] ?? null;
      $companyAddress = $data['Add_of_Company'] ?? null;
      $companyContact = $data['Contact_No_of_Company'] ?? null;
      $yearsWorked = $data['Years_of_Stay'] ? $data['Years_of_Stay'] : null;
      $position = $data['Position'] ?? null;
      $tin = $data['TIN'] ?? null;
      $sss = $data['SSS_GSIS_No'] ?? null;
      $monthlySalary = $data['Monthly_Salary'] ?? null;
      $projectName = $data['Project_Name'] ?? null;
      $projectLocation = $data['Locatn'] ?? null;
      $phase = $data['Phase'] ? $data['Phase'] : null;
      $block = $data['Blck'] ?? null;
      $lot = $data['Lot'] ?? null;
      $lotArea = $data['Lot_Area'] ? $data['Lot_Area'] : null;
      $pricePerSqm = $data['Price_Per_Sqm'] ?? null;
      $monthlyAmortization = $data['Monthly_Amort'] ?? null;
      $paymentTerms = $data['Payment_Terms_Months'] ? $data['Payment_Terms_Months'] : null;
      $downpaymentAmount = $data['Downpayment_Amount'] ?? null;
      $downpaymentDate = $data['Downpayment_Date'] == '0000-00-00' ? null : date('Y-m-d', strtotime($data['Downpayment_Date']));
      $salesCoordinator = $data['Sales_Coordinator'] ?? null;
      $assistantCoordinator = $data['Assisted_By'] ?? null;
      $remarks = $data['Remarks'] ?? null;

      // Insert clients data
      $qry = 'INSERT INTO tbl_clients (date_entry, customer_no, firstname, middlename, lastname, address, birthday, contact_no, email, marital_status, spouse_name, spouse_contact_no, spouse_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('sssssssssssss',
        $dateEntry,
        $customerNo,
        $firstname,
        $middlename,
        $lastname,
        $address,
        $birthday,
        $contactNo,
        $email,
        $maritalStatus,
        $spouseName,
        $spouseContactNo,
        $spouseEmail
      );
      $stmt->execute();
      $stmt->close();
      $lastInsertedId = $conn->insert_id;

      $qry = 'SELECT id FROM tbl_employment_details WHERE client_id = ? LIMIT 1';
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('i', $lastInsertedId);
      $stmt->bind_result($eid);
      $stmt->execute();
      $stmt->store_result();
      $searchResultEmployment = $stmt->num_rows;
      $stmt->fetch();
      $stmt->close();

      if ($searchResultEmployment === 0) {
        // Insert clients employment details
        $qry = 'INSERT INTO tbl_employment_details (client_id, company_name, company_address, company_contact, years_worked, position, tin_id, sss_id, monthly_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('isssissss',
          $lastInsertedId,
          $companyName,
          $companyAddress,
          $companyContact,
          $yearsWorked,
          $position,
          $tin,
          $sss,
          $monthlySalary
        );
        $stmt->execute();
        $stmt->close();
      }

      $qry = 'SELECT id FROM tbl_properties WHERE client_id = ? LIMIT 1';
      $stmt = $conn->prepare($qry);
      $stmt->bind_param('i', $lastInsertedId);
      $stmt->bind_result($pid);
      $stmt->execute();
      $stmt->store_result();
      $searchResultProperties = $stmt->num_rows;
      $stmt->fetch();
      $stmt->close();

      if ($searchResultProperties === 0) {
        // Insert clients property details
        $qry = 'INSERT INTO tbl_properties (client_id, project_name, project_location, phase, block, lot, lot_area, price_per_sqm, monthly_amortization, payment_terms, downpayment_amount, downpayment_date, sales_coordinator, assistant_coordinator, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($qry);
        $stmt->bind_param('ississississsss',
          $lastInsertedId,
          $projectName,
          $projectLocation,
          $phase,
          $block,
          $lot,
          $lotArea,
          $pricePerSqm,
          $monthlyAmortization,
          $paymentTerms,
          $downpaymentAmount,
          $downpaymentDate,
          $salesCoordinator,
          $assistantCoordinator,
          $remarks
        );
        $stmt->execute();
        $stmt->close();
      }

      $insertedData = [
        'id' => $lastInsertedId,
        'customer_no' => $data['Customer_No']
      ];
    }

    return $insertedData;
  }
  
  $query = "SELECT * FROM marigoldclients";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
  $stmt->close();

  $result = [];
  foreach ($data as $key => $value) {
    $inserted = insertClientsData($value);
    if ($inserted) { $result[] = $inserted; }
  }

  if (empty($result)) {
    echo getResponseStatus(404);

    return;
  }

  echo getResponseStatus(200, $result);

  return;

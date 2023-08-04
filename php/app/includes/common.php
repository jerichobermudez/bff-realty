<?php
  require_once('../enums/PaymentTypes.php');

  function replacePlaceholdersRecursive($htmlLayout, $data) {
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $htmlLayout = replacePlaceholdersRecursive($htmlLayout, $value);

        $loopStart = '{% loop ' . $key . ' %}';
        $loopEnd = '{% endloop %}';

        $loopStartPos = strpos($htmlLayout, $loopStart);
        $loopEndPos = strpos($htmlLayout, $loopEnd);

        if ($loopStartPos !== false && $loopEndPos !== false) {
          $loopTemplate = substr($htmlLayout, $loopStartPos + strlen($loopStart), $loopEndPos - ($loopStartPos + strlen($loopStart)));
          $loopContent = '';

          foreach ($value as $index => $item) {
            $itemContent = $loopTemplate;

            foreach ($item as $itemKey => $itemValue) {
              $placeholder = '{{ ' . $key . '.' . $itemKey . ' }}';
              $placeholderIndex = '{{ ' . $key . '.index }}';
              $replacement = !empty($itemValue) ? $itemValue : '------';
              $itemContent = str_replace($placeholder, $replacement, $itemContent);$itemContent = str_replace($placeholderIndex, $index + 1, $itemContent);
            }

            $loopContent .= $itemContent;
          }

          $htmlLayout = substr_replace($htmlLayout, $loopContent, $loopStartPos, $loopEndPos + strlen($loopEnd) - $loopStartPos);
        }
      } else {
        $placeholder = '{{ ' . $key . ' }}';
        $replacement = !empty($value) ? $value : '------';
        $htmlLayout = str_replace($placeholder, $replacement, $htmlLayout);
      }
    }
    return $htmlLayout;
  }

  function generateReferenceNo($conn, $date) {
    $initialReferencecNo = intval(date('mdy', strtotime($date)));
    $referenceNo = $initialReferencecNo . 1;

    $searchRefNo = $initialReferencecNo . '%';
    $qry = "SELECT reference_no FROM tbl_payments WHERE reference_no LIKE ? ORDER BY reference_no DESC LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('s', $searchRefNo);
    $stmt->bind_result($lastRefNo);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
      $lastSequel = str_replace($initialReferencecNo, '', $lastRefNo);
      $referenceNo = $initialReferencecNo . ((int)substr($lastSequel, -2) + 1);
    }

    return intval($referenceNo);
  }

  function getClientDetails($conn, $clientId) {
    $qry = "SELECT
        customer_no,
        CONCAT_WS(' ', C.firstname, C.middlename, C.lastname) AS client_name,
        C.firstname AS client_firstname,
        C.middlename AS client_middlename,
        C.lastname AS client_lastname,
        C.address AS client_address,
        C.contact_no AS client_contact,
        C.email AS client_email,
        C.birthday AS client_birthday,
        C.marital_status AS marital_status,
        C.spouse_name AS spouse_name,
        C.spouse_contact_no AS spouse_contact,
        C.spouse_email AS spouse_email,
        E.company_name AS company_name,
        E.company_address AS company_address,
        E.company_contact AS company_contact,
        E.years_worked AS years_worked,
        E.position AS position,
        E.tin_id AS tin_id,
        E.sss_id AS sss_id,
        E.monthly_salary AS monthly_salary
      FROM tbl_clients AS C
      LEFT JOIN tbl_employment_details AS E ON E.client_id = C.id
      WHERE C.id = ?
    ";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    $data = [];
    if ($count > 0) {
      $row = $result->fetch_assoc();
      foreach ($row as $key => $value) {
        $data[$key] = trim($value);
      }
    }
    $stmt->close();

    return $data;
  }

  function getPropertyDetails($conn, $propertyId) {
    $qry = "SELECT
      customer_no,
      CONCAT_WS(' ', TRIM(C.firstname), TRIM(C.middlename), TRIM(C.lastname)) AS client_name,
      address,
      project_id,
      project_name,
      project_location,
      phase,
      block,
      lot,
      lot_area,
      price_per_sqm,
      monthly_amortization,
      payment_terms,
      downpayment_amount,
      downpayment_date,
      downpayment_due_date,
      sales_coordinator,
      assistant_coordinator,
      agent_id,
      is_reserved,
      remarks
    FROM tbl_properties AS P
    LEFT JOIN tbl_clients AS C ON C.id = P.client_id
    WHERE P.id = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    $data = [];
    if ($count > 0) {
      $row = $result->fetch_assoc();
      foreach ($row as $key => $value) {
        $data[$key] = trim($value);
      }
    }
    $stmt->close();

    return $data;
  }

  function checkClient($conn, $clientId) {
    $qry = "SELECT id FROM tbl_clients WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();

    if ($count <= 0) {
      return false;
    }

    return true;
  }

  function checkPropertyLotNumbers(
    $conn,
    $projectId,
    $projectName,
    $projectLocation,
    $phase,
    $block,
    $lotNumberArray,
    $propertyId = null
  ) {
    $qry = "SELECT lot FROM tbl_properties
      WHERE
        NOT ( block = 'Tbd' OR lot = 'Tbd' )
        AND (
          ( project_id = ? AND block = ? )
          OR ( project_id = ? AND phase = ? AND block = ? )
          OR ( project_name = ? AND block = ? )
          OR ( project_name = ? AND phase = ? AND block = ? )
          OR ( project_location = ? AND block = ? )
          OR ( project_location = ? AND phase = ? AND block = ? )
        )
    ";

    if ($propertyId) {
      $qry .= " AND id != $propertyId";
    }

    $stmt = $conn->prepare($qry);
    $stmt->bind_param(
      'isiissssissssis',
      $projectId, $block,
      $projectId, $phase, $block,
      $projectName, $block,
      $projectName, $phase, $block, 
      $projectLocation, $block,
      $projectLocation, $phase, $block      
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    $lotNumbers = [];
    if ($count > 0) {
      while ($row = $result->fetch_assoc()) {
        $numbersArray = getLotNumbersAndConvertToArray($row['lot']);
        $lotNumbers = array_merge($lotNumbers, $numbersArray);
      }

      $lotNumbers = array_unique($lotNumbers);
    }

    $searchLotNumber = getLotNumbersAndConvertToArray($lotNumberArray);

    $result = searchLotNumbers($lotNumbers, $searchLotNumber);

    return $result;
  }

  function getPaymentHistory(
    $conn,
    $propertyId,
    $order = 'DESC',
    $totalPropertyPrice = 0,
    $totalMonthlyAmortization = 0
  ) {
    $qry = "SELECT reference_no, payment_amount, payment_date, payment_due_date, type, payment_type, mode_of_payment, pr_no, payment_remarks FROM tbl_payments WHERE property_id = ? ORDER BY id $order";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param('i', $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    $data = [];
    $totalPayment = 0;
    $totalAmortization = 0;
    $outstandingBalance = $totalPropertyPrice;
    $lastRefNo = '';
    $lastPaymentAmount = 0;
    $lastPaymentDate = '';
    $index = 0;
    if ($count > 0) {
      while ($row = $result->fetch_assoc()) {
        $paymentAmount = str_replace(',', '', $row['payment_amount']);
        $outstandingBalance = $outstandingBalance - $paymentAmount;
        $isAmortization = strtolower($row['payment_type']) === 'monthly amortization';
        $data['payments'][] = [
          'reference_no' => $row['reference_no'],
          'amortization' =>  $isAmortization ? $totalMonthlyAmortization : '',
          'outstanding_balance' => number_format((float) $outstandingBalance, 2),
          'payment_amount' => number_format((float) $paymentAmount, 2),
          'payment_date' => date('y/m/d', strtotime($row['payment_date'])),
          'payment_due_date' => $row['payment_due_date'] ? date('y/m/d', strtotime($row['payment_due_date'])) : '',
          'type' => $row['type'],
          'mode_of_payment' => $row['mode_of_payment'],
          'pr_no' => $row['pr_no'],
          'payment_type' => PaymentTypes::getTextValue($row['type']),
          'payment_remarks' => $row['payment_remarks'] ?? ''
        ];

        if ($isAmortization) {
          $totalAmortization += str_replace(',', '', $totalMonthlyAmortization);
        }
        $totalPayment += $paymentAmount;
        $index++;
        $lastRefNo = $row['reference_no'];
        $lastPaymentAmount = number_format((float) $paymentAmount, 2);
        $lastPaymentDate = $row['payment_date'];
      }
      $data['total'] = number_format((float) $totalPayment, 2);
      $data['total_amortization'] = number_format((float) $totalAmortization, 2);
      $data['total_outstanding_balance'] = number_format((float) $outstandingBalance, 2);
      $data['last_reference_no'] = $lastRefNo;
      $data['last_payment_amount'] = $lastPaymentAmount;
      $data['last_payment_date'] = $lastPaymentDate;
    }
    $stmt->close();

    return $data;
  }

  function computeMonthlyAmortization(
    $lotArea, 
    $pricePerSqm, 
    $termsOfPayment, 
    $downpaymentAmount
  ) {
    $lotArea = $lotArea ? str_replace(',', '', $lotArea) : 0;
    $pricePerSqm = $pricePerSqm ? str_replace(',', '', $pricePerSqm) : 0;
    $termsOfPayment = $termsOfPayment ? $termsOfPayment : 12;
    $downpaymentAmount = $downpaymentAmount ? str_replace(',', '', $downpaymentAmount) : 0;

    $miscelaneousFee = 0.07; // 7%
    $totalPropertyPrice = $lotArea * $pricePerSqm;
    $totalPropertyPrice = $totalPropertyPrice + ($totalPropertyPrice * $miscelaneousFee);
    $remainingBalance = $totalPropertyPrice - $downpaymentAmount;
    $totalMonthlyAmortization = $remainingBalance / $termsOfPayment;
    $totalMonthlyAmortization = $totalMonthlyAmortization > 0
      ? $totalMonthlyAmortization
      : 0;
      
    $totalMonthlyAmortization = number_format((float) $totalMonthlyAmortization, 2, '.', '');

    return $totalMonthlyAmortization;
  }

  function getLotNumbersAndConvertToArray($string) {
    $numbersOnly = trim(preg_replace('/[^0-9]+/', ' ', $string));
    $numbersArray = explode(' ', $numbersOnly);
    array_unique($numbersArray);

    return $numbersArray;
  }

  function searchLotNumbers($lotNumbers, $searchLotNumber) {
    foreach ($searchLotNumber as $number) {
      if (in_array($number, $lotNumbers)) {
        return true;

        break;
      }
    }
    return false;
  }
  
  function generateVoucherCode($conn) {
    $length = 8;
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
      $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
  }

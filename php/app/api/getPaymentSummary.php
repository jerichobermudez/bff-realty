<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../enums/PaymentTypes.php');
  require_once('../vendors/tcpdf/tcpdf.php');
  $code = 403;
  $data = [];

  if (isset($_POST['property_id'])) {
    $propertyId = intval($_POST['property_id']);
    $action = isset($_POST['view']) ? 'I' : 'D';

    // Get Property Details
    $propertyDetails = getPropertyDetails($conn, $propertyId);

    if (count($propertyDetails) <= 0) {
      echo getResponseStatus(404, $data);

      return;
    }

    // Instantiate Property Details
    $customerNo = $propertyDetails['customer_no'];
    $clientName = $propertyDetails['client_name'];
    $clientAddress = $propertyDetails['address'];
    $projectName = $propertyDetails['project_name'];
    $projectLocation = $propertyDetails['project_location'];
    $phase = $propertyDetails['phase'];
    $block = $propertyDetails['block'];
    $lot = $propertyDetails['lot'];
    $lotArea = $propertyDetails['lot_area'];
    $pricePerSqm = $propertyDetails['price_per_sqm'];
    $monthlyAmortization = $propertyDetails['monthly_amortization'];
    $paymentTerms = $propertyDetails['payment_terms'];
    $downpaymentAmount = $propertyDetails['downpayment_amount'];
    $downpaymentDate = $propertyDetails['downpayment_date'];
    $salesCoordinator = $propertyDetails['sales_coordinator'];
    $assistantCoordinator = $propertyDetails['assistant_coordinator'];

    $lotArea = $lotArea ? str_replace(',', '', $lotArea) : 0;
    $pricePerSqm = $pricePerSqm ? str_replace(',', '', $pricePerSqm) : 0;
    $termsOfPayment = $paymentTerms ? $paymentTerms : 12;
    $downpaymentAmount = $downpaymentAmount ? str_replace(',', '', $downpaymentAmount) : 0;
    $downpaymentDate = $downpaymentDate ? date('F d, Y', strtotime($downpaymentDate)) : '';

    $netSellingPrice = $lotArea * $pricePerSqm;
    $miscelaneousFee = $netSellingPrice * 0.07;
    $totalPropertyPrice = $netSellingPrice + $miscelaneousFee;
    $remainingBalance = $totalPropertyPrice - $downpaymentAmount;
    $totalMonthlyAmortization = $remainingBalance / $termsOfPayment;
    $totalMonthlyAmortization = $totalMonthlyAmortization > 0
      ? $totalMonthlyAmortization
      : 0;

    // $totalMonthlyAmortization = number_format((float) $totalMonthlyAmortization, 2, '.', ',');

    $paymentHistory = getPaymentHistory($conn, $propertyId, 'ASC', $totalPropertyPrice, $totalMonthlyAmortization);
    $totalPayment = $paymentHistory['total'] ?? '0.00';
    $totalAmortization = $paymentHistory['total_amortization'] ?? '0.00';
    $totalOutstandingBalance = $paymentHistory['total_outstanding_balance'] ?? '0.00';
    $lastRefNo = $paymentHistory['last_reference_no'] ?? '';
    $lastPaymentAmount = $paymentHistory['last_payment_amount'] ?? '';
    $lastPaymentDate = $paymentHistory['last_payment_date'] ?? '';

    $startDate = new DateTime($downpaymentDate);
    $inputDay = (int)$startDate->format('d');

    $monthlyAmortizationDates[0] = $startDate->format('m/d/Y');
    for ($i = 1; $i <= $paymentTerms; $i++) {
      $startDate->modify('first day of next month');
      $lastDay = (int)$startDate->format('t');
      $nextMonthDay = min($inputDay, $lastDay);
      $startDate->setDate(
        $startDate->format('Y'),
        $startDate->format('m'),
        $nextMonthDay
      );
      $monthlyAmortizationDates[$i] = $startDate->format('m/d/Y');
    }

    $paymentSummary = [];
    foreach ($paymentHistory['payments'] ?? $paymentHistory as $index => $row) {
      $paymentSummary[$index] = $row;
    }

    // Create new PDF document
    $pdf = new TCPDF();

    // Set Document Information
    $today = date('Y-m-d');
    $title = 'Payment Schedule - ' . $clientName . ' ' . $customerNo . ' ' . $today . '.pdf';
    $pdf->SetTitle($title);
    $pdf->SetAuthor('Company Name');
    $pdf->SetSubject('Statement of Account');
    $pdf->SetKeywords('Company Name');
    $pdf->SetCreator('CMS');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(18, 18, 18);
    $pdf->SetAutoPageBreak(TRUE, 16);
    $pdf->SetFont('helvetica', 'B', 7);

    // Set Page Size
    $width = 215.9;
    $height = 355.6;

    // Add Page
    $pdf->AddPage('P', [$width, $height]);
    $pdf->Cell(0, 0, 'BFF REALTY AND DEVELOPMENT INC', 0, 1, 'C', 0, '', 0);
    $pdf->Cell(0, 0, 'STATEMENT OF ACCOUNT', 0, 1, 'C', 0, '', 0);
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 7);
    $clientsInfoTable = '<table cellpadding="1" width="100%">
      <tr>
        <td colspan="2" width="15%">NAME:</td>
        <td colspan="3" width="35%"><b>' . strtoupper($clientName) . '</b></td>
        <td colspan="2" width="25%">NET SELLING PRICE:</td>
        <td colspan="3" width="25%" align="right">' . number_format((float) $netSellingPrice, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td colspan="2" width="15%">PROJECT NAME</td>
        <td colspan="3" width="35%">' . strtoupper($projectName) . '</td>
        <td colspan="2" width="25%">ADD: MISCELANEOUS FEE</td>
        <td colspan="3" width="25%" align="right">' . number_format((float) $miscelaneousFee, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td colspan="2" width="15%">ADDRESS:</td>
        <td colspan="3" width="35%">' . strtoupper($projectLocation) . '</td>
        <td colspan="2" width="25%">TOTAL CONTRACT PRICE</td>
        <td colspan="3" width="25%" align="right">' . number_format((float) $totalPropertyPrice, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td colspan="2" width="15%">BLOCK:</td>
        <td width="12%" align="right"><b>' . $block . '</b></td>
        <td width="12%">LOT: <b>' . str_pad($lot, 2, '0', STR_PAD_LEFT) . '</b></td>
        <td width="11%">PHASE: <b>' . str_pad($phase, 2, '0', STR_PAD_LEFT) . '</b></td>
        <td colspan="2" width="25%">TERMS:</td>
        <td colspan="3" width="25%" align="right">' . $paymentTerms . ' MONTHS &nbsp; &nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" width="15%">LOT AREA:</td>
        <td colspan="3" width="35%">' . $lotArea . 'SQM</td>
        <td colspan="2" width="25%">PRICE PER SQM</td>
        <td colspan="3" width="25%" align="right"><s>P</s> ' . number_format((float) $pricePerSqm, 2, '.', ',') . '</td>
      </tr>
    </table>';

    $paymentSummaryBody = '';
    $newKey = 0;
    $balance = $remainingBalance;
    foreach($monthlyAmortizationDates as $key => $value) {
      $numbering = $key === 1 ? '1.1' : ($key === 0 ? 1 : $newKey);

      $payment = $paymentSummary[$key] ?? '';
      $paymentAmount = $payment ? str_replace(',', '', $payment['payment_amount']) : 0;
      $paymentDate = $payment ? $payment['payment_date'] : null;
      $paymentType = $payment ? $payment['type'] : null;
      $paymentPRNo = $payment ? $payment['pr_no'] : null;
      $paymentRemarks = $payment ? $payment['payment_remarks'] : null;

      // Payment Schedule Amount
      $paymentScheduleAmount = $key === 0
        ? ($paymentAmount ? number_format((float) $paymentAmount, 2, '.', ',') : '')
        : number_format((float) $totalMonthlyAmortization, 2, '.', ',');

      // Payment Made
      $paymentMade = $paymentAmount
        ? number_format((float) $paymentAmount, 2, '.', ',')
        : '';

      // Remaining Balance on Payments
      $remainingBalanceOnPayment = '';
      if ($key > 0) {
        $remainingBalanceOnPayment = $totalMonthlyAmortization - $paymentAmount;
        $remainingBalanceOnPayment = $remainingBalanceOnPayment
          ? number_format((float) $remainingBalanceOnPayment, 2, '.', ',')
          : '';

        $balance = $balance - $totalMonthlyAmortization;
        $balance = round($balance, 2);
      }

      $penaltyAmount = '';
      // if ($paymentScheduleAmount > $paymentMade && $paymentMade > 0) {
      //   $penaltyAmount = $remainingBalanceOnPayment;
      // }

      // Balance
      $totalBalance = $key === 0 ? $totalPropertyPrice : $balance;
      $totalBalance = number_format((float) $totalBalance, 2, '.', ',');

      // Current Balance
      $totalCurrentBalance = $key === 0
        ? $totalPropertyPrice
        : $balance - str_replace(',', '', $remainingBalanceOnPayment);
      $totalCurrentBalance = number_format((float) $totalCurrentBalance, 2, '.', ',');

      $paymentSummaryBody .= '<tr>
        <td align="right"><b>' . $numbering . '</b></td>
        <td align="center">' . $value . '</td>
        <td align="right">' . $totalBalance . '</td>
        <td align="right">' . $paymentScheduleAmount . '</td>
        <td align="right">' . $paymentMade . '</td>
        <td align="right">' . $remainingBalanceOnPayment . '</td>
        <td align="right">' . $penaltyAmount . '</td>
        <td align="right">' . $totalCurrentBalance . '</td>
        <td align="right">' . $paymentPRNo . '</td>
        <td align="right">' . $paymentRemarks . '</td>
      </tr>';

      $newKey++;
    }

    $paymentSummaryTable = '<table border="0.5" cellpadding="1.5" width="100%">
      <tr style="font-size: 6.5; font-weight: bold; background-color: #f6dea7;">
        <td width="3%" align="center"></td>
        <td width="12%" align="center" style="line-height: 15px;">DATE</td>
        <td width="12%" align="center" style="line-height: 15px;">BALANCE</td>
        <td width="12%" align="center">SCHEDULE OF PAYMENTS</td>
        <td width="11%" align="center">PAYMENTS MADE</td>
        <td width="17%" align="center" style="line-height: 15px;">REM. BAL. ON PAYMENTS</td>
        <td width="8%" align="center" style="line-height: 15px;">PENALTIES</td>
        <td width="9%" align="center">CURRENT BALANCE</td>
        <td width="7%" align="right" style="line-height: 15px;">REF</td>
        <td width="9%" align="center" style="line-height: 15px;">REMARKS</td>
      </tr>
      ' . $paymentSummaryBody . '
    </table>';

    $pdf->writeHTML($clientsInfoTable, true, false, false, false, '');
    $pdf->writeHTML($paymentSummaryTable, true, false, false, false, '');

    $pdf->Output($title, $action);
  } else {
    echo getResponseStatus($code, $data);
  }

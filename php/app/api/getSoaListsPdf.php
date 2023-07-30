<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
  require_once('../vendors/tcpdf/tcpdf.php');
  require_once('../enums/PaymentTypes.php');
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

    // Compute the monthly amortization
    $totalPropertyPrice = $lotArea * $pricePerSqm;
    $remainingBalance = $totalPropertyPrice - $downpaymentAmount;
    $totalMonthlyAmortization = $remainingBalance / $termsOfPayment;
    $totalMonthlyAmortization = $totalMonthlyAmortization > 0
      ? $totalMonthlyAmortization
      : 0;

    $totalMonthlyAmortization = number_format((float) $totalMonthlyAmortization, 2, '.', ',');

    $paymentHistory = getPaymentHistory($conn, $propertyId, 'ASC', $totalPropertyPrice, $totalMonthlyAmortization);
    $totalPayment = $paymentHistory['total'] ?? '0.00';
    $totalAmortization = $paymentHistory['total_amortization'] ?? '0.00';
    $totalOutstandingBalance = $paymentHistory['total_outstanding_balance'] ?? '0.00';

    // Create new PDF document
    $pdf = new TCPDF();

    // Set Document Information
    $today = date('F d, Y');
    $title = 'AR-' . $clientName . '; ' . $customerNo . '; ' . $today . '.pdf';
    $pdf->SetTitle($title);
    $pdf->SetAuthor('Company Name');
    $pdf->SetSubject('Statement of Account');
    $pdf->SetKeywords('Company Name');
    $pdf->SetCreator('CMS');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 16);
    $pdf->SetFont('helvetica', '', 7);

    // Set Page Size
    $width = 215.9;
    $height = 279.4;

    // Add Page
    $pdf->AddPage('P', [$width, $height]);

    // Page Content
    $border = 'border: 0.1px solid black; ';
    $propertyTable = '<table cellpadding="2" width="100%" style="'. $border .' font-weight: bold;">
      <tr>
        <td colspan="6" align="center" width="63%" style="' . $border . ' font-size: 13pt;">STATEMENT OF ACCOUNT</td>
        <td colspan="3" width="37%" style="border: none;"></td>
      </tr>
      <tr>
        <td colspan="6" align="center" style="'. $border .' font-size: 9pt;">As of ' . $today . '</td>
        <td colspan="3" style="font-size: 10pt;"></td>
      </tr>
      <tr>
        <td colspan="9" style="'. $border .'"></td>
      </tr>
      <tr>
        <td width="15%" colspan="2" style="'. $border .'">Buyer:</td>
        <td width="48%" colspan="4" style="'. $border .'">' . strtoupper($clientName) . '</td>
        <td width="11%" colspan="1" style="'. $border .'">Broker:</td>
        <td width="26%" colspan="2" style="'. $border .'">' . strtoupper($salesCoordinator) . '</td>
      </tr>
      <tr>
        <td colspan="2" style="'. $border .'">Residential Address:</td>
        <td colspan="4" style="'. $border .'">' . strtoupper($clientAddress) . '</td>
        <td colspan="1" style="'. $border .'">Date Reserved:</td>
        <td colspan="2" align="center" style="'. $border .'">' . $downpaymentDate . '</td>
      </tr>
      <tr>
        <td width="15%" colspan="2" style="'. $border .'">Project:</td>
        <td width="48%" colspan="4" style="'. $border .'">' . strtoupper($projectName) . '</td>
        <td width="25%" colspan="2" style="'. $border .'">Downpayment:</td>
        <td width="12%" align="right" style="'. $border .'">' . number_format((float) $downpaymentAmount, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td colspan="2" style="'. $border .'">Project Location:</td>
        <td colspan="4" style="'. $border .'">' . strtoupper($projectLocation) . '</td>
        <td colspan="2" style="'. $border .'">PRICE PER SQM:</td>
        <td align="right" style="'. $border .'">' . number_format((float) $pricePerSqm, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td width="15%" colspan="2" style="'. $border .'">Block:</td>
        <td width="15%" align="center" style="'. $border .'">' . $block . '</td>
        <td width="10%" style="'. $border .'">Lot:</td>
        <td width="10%" align="center" style="'. $border .'">' . $lot . '</td>
        <td width="13%" style="'. $border .'"></td>
        <td width="25%" colspan="2" style="'. $border .'">PAYMENT TERMS(MONTH):</td>
        <td width="12%" align="center" style="'. $border .'">' . $paymentTerms . '</td>
      </tr>
      <tr>
        <td colspan="2" style="'. $border .'">Phase:</td>
        <td align="center" style="'. $border .'">' . $phase . '</td>
        <td style="'. $border .'">Lot Area:</td>
        <td align="center" style="'. $border .'">' . $lotArea . '</td>
        <td align="center" style="'. $border .'">sqm</td>
        <td colspan="2" style="'. $border .'">TOTAL CONTRACT PRICE:</td>
        <td align="right" style="'. $border .'">' . number_format((float) $totalPropertyPrice, 2, '.', ',') . '</td>
      </tr>
      <tr>
        <td colspan="6" style="'. $border .'"></td>
        <td colspan="2" style="'. $border .'">MONTHLY AMORTIZATION:</td>
        <td align="right" style="'. $border .'">' . $totalMonthlyAmortization . '</td>
      </tr>
    </table>';

    $pdf->writeHTML($propertyTable, true, false, false, false, '');
    $pdf->Ln();

    $paymentData = '';
    $startDate = new DateTime($downpaymentDate);
    $inputDay = (int)$startDate->format('d');
    foreach ($paymentHistory['payments'] ?? $paymentHistory as $index => $row) {
      $paymentDueDate = '-';
      if ($row['type'] === PaymentTypes::MONTHLY_AMORTIZATION) {
        $startDate->modify('first day of next month');
        $lastDay = (int)$startDate->format('t');
        $nextMonthDay = min($inputDay, $lastDay);
        $startDate->setDate($startDate->format('Y'), $startDate->format('m'), $nextMonthDay);

        $paymentDueDate = $startDate->format('m/d/Y');
      }

      $paymentData .= '
        <tr>
          <td align="center" style="' . $border . '">'. ($index + 1) .'</td>
          <td align="center" style="' . $border . '">' . $paymentDueDate . '</td>
          <td style="' . $border . '">' . $row['payment_type'] . '</td>
          <td align="right" style="' . $border . '">' . $row['amortization'] . '</td>
          <td align="center" style="' . $border . '">' . $row['reference_no'] . '</td>
          <td align="right" style="' . $border . '">' . $row['payment_amount'] . '</td>
          <td align="center" style="' . $border . '">' . $row['payment_date'] . '</td>
          <td align="right" style="' . $border . '">' . $row['outstanding_balance'] . '</td>
          <td style="' . $border . '">' . $row['payment_remarks'] . '</td>
        </tr>
      ';
    }

    if (count($paymentHistory) <= 0) {
      $paymentData .= '<tr>
        <td align="center" colspan="9" style="' . $border . ' font-size: 9pt;">No payment has been made.</td>
      </tr>';
    }

    $paymentTable = '<table cellpadding="1" width="100%" style="font-size: 7pt;">
      <tr>
        <td width="5%" align="center" style="' . $border . ' font-weight: bold;">No.</td>
        <td width="10%" align="center" style="' . $border . ' font-weight: bold;">Due&nbsp;Date</td>
        <td width="15%" align="center" style="' . $border . ' font-weight: bold;">Payment&nbsp;Type</td>
        <td width="10%" align="center" style="' . $border . ' font-weight: bold;">Amortization</td>
        <td width="10%" align="center" style="' . $border . ' font-weight: bold;">Reference&nbsp;No.</td>
        <td width="13%" align="center" style="' . $border . ' font-weight: bold;">Payment&nbsp;Amount</td>
        <td width="11%" align="center" style="' . $border . ' font-weight: bold;">Payment&nbsp;Date</td>
        <td width="14%" align="center" style="' . $border . ' font-weight: bold;">Outstanding&nbsp;Balance</td>
        <td width="12%" align="center" style="' . $border . ' font-weight: bold;">Remarks</td>
      </tr>
      ' . $paymentData . '
      <tr>
        <td colspan="3"></td>
        <td align="right"><table width="100%" cellpadding="0" cellspacing="1.2">
          <tr><td style="border-bottom: 0.8px solid black;"></td></tr><tr><td style="border-top: 0.8px solid black;">' . $totalAmortization . '</td></tr></table>
        </td>
        <td></td>
        <td align="right"><table width="100%" cellpadding="0" cellspacing="1.2">
          <tr><td style="border-bottom: 0.8px solid black;"></td></tr><tr><td style="border-top: 0.8px solid black;">' . $totalPayment . '</td></tr></table>
        </td>
        <td></td>
        <td align="right"><table width="100%" cellpadding="0" cellspacing="1.2">
          <tr><td style="border-bottom: 0.8px solid black;"></td></tr><tr><td style="border-top: 0.8px solid black;">' . $totalOutstandingBalance . '</td></tr></table>
        </td>
        <td></td>
      </tr>
      <tr>
        <td colspan="9"></td>
      </tr>
      <tr>
        <td colspan="2" style="' . $border . ' font-weight: bold;">Prepared by:</td>
        <td colspan="3" style="' . $border . ' font-weight: bold;"></td>
        <td rowspan="2" style="' . $border . ' font-weight: bold;">Validated & Noted by:</td>
        <td colspan="3" rowspan="2" style="' . $border . ' font-weight: bold;"></td>
      </tr>
      <tr>
        <td colspan="2" style="' . $border . ' font-weight: bold;">Checked by:</td>
        <td colspan="3" style="' . $border . ' font-weight: bold;"></td>
      </tr>
    </table>';

    $pdf->writeHTML($paymentTable, true, false, false, false, '');

    $pdf->Output($title, $action);
  } else {
    echo getResponseStatus($code, $data);
  }

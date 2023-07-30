<?php
  require_once('../includes/connection.php');
  require_once('../includes/common.php');
  require_once('../includes/response.php');
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
    $lastRefNo = $paymentHistory['last_reference_no'] ?? '';
    $lastPaymentAmount = $paymentHistory['last_payment_amount'] ?? '';
    $lastPaymentDate = $paymentHistory['last_payment_date'] ?? '';

    $startDate = new DateTime($downpaymentDate);
    $inputDay = (int)$startDate->format('d');

    $monthlyAmortizationDates = [];
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

    $totalRows = 72;
    $rowsPerColumn = 30;
    $monthlyAmortData = '';
    for ($row = 1; $row <= $rowsPerColumn; $row++) {
      $monthlyAmortData .= '<tr>';
      for ($column = 1; $column <= 3; $column++) {
          $currentCount = $row + ($column - 1) * $rowsPerColumn;
          if ($currentCount <= $totalRows) {
              $monthlyAmortData .= '
                <td width="6%" align="right" style="font-weight: normal;">
                  ' . ($currentCount <= 60 ? $currentCount : ($paymentTerms > 60 ? $currentCount : '')) . '
                </td>
                <td width="27.33%" align="center" style="font-weight: bold;">'
                  . ($currentCount <= $paymentTerms 
                    ? '<i>' . ($monthlyAmortizationDates[$currentCount] ?? '') . '</i>
                      <span style="font-weight: normal;">&nbsp; &nbsp; &nbsp;' . $totalMonthlyAmortization .
                      '&nbsp; &nbsp; &nbsp; &nbsp;</span>'
                    : '') .
                '</td>';
          } else {
              $monthlyAmortData .= '<td colspan="2"></td>';
          }
      }
      $monthlyAmortData .= '</tr>';
    }

    // Create new PDF document
    $pdf = new TCPDF();

    // Set Document Information
    $today = date('F d, Y');
    $title = 'CMAS ' . $clientName . '-' . $customerNo . ' (' . date('m.d.Y') . ').pdf';
    $pdf->SetTitle($title);
    $pdf->SetAuthor('Company Name');
    $pdf->SetSubject('Statement of Account');
    $pdf->SetKeywords('Company Name');
    $pdf->SetCreator('CMS');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(18, 18, 18);
    $pdf->SetAutoPageBreak(TRUE, 16);
    $pdf->SetFont('helvetica', '', 7);

    // Set Page Size
    $width = 215.9;
    $height = 355.6;

    // Add Page
    $pdf->AddPage('P', [$width, $height]);

    // Page Content
    $chiefName = 'OFFICER NAME';
    $chiefTitle = 'POSITION';
    $border = 'border: 0.1px solid black; ';
    $borderBottom = 'border-bottom: 1.3px solid black;';
    $propertyTable = '<table cellpadding="1" width="100%" style="font-weight: bold; font-size: 9;">
      <tr>
        <td width="19%"></td>
        <td width="81%" colspan="5" style="font-size: 10pt;"></td>
      </tr>
      <tr>
        <td width="19%"></td>
        <td width="81%" colspan="5" style="font-size: 10pt;">CLIENT&rsquo;S MONTHLY AMORTIZATION SCHEDULE</td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="57%" colspan="3"></td>
        <td width="43%" colspan="2"> <i>DATE OF RESERVATION/</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>CLIENT&rsquo;S NAME:</i></td>
        <td width="36%" colspan="2" style="' . $borderBottom . '"><i>' . strtoupper($clientName) . '</i></td>
        <td width="26%"> <i>DOWNPAYMENT</i></td>
        <td width="16%" align="center" style="' . $borderBottom . '"><i>' . ($downpaymentDate ? date('m/d/Y', strtotime($downpaymentDate)) : '') . '</i></td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="21%" align="center"><i>ADDRESS:</i></td>
        <td width="36%" colspan="2" style="' . $borderBottom . '"><i>' . strtoupper($clientAddress) . '</i></td>
        <td width="26%"> <i>LOT AREA:</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . $lotArea . '</i></td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="21%" align="center"><i>PROJECT NAME:</i></td>
        <td width="36%" colspan="2" style="' . $borderBottom . '">' . strtoupper($projectName) . '</td>
        <td width="26%"> <i>PRICE PER SQM:</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . number_format((float) $pricePerSqm, 2, '.', ',') . '</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>ADDRESS:</i></td>
        <td width="36%" colspan="2" style="' . $borderBottom . ' font-size: 7pt; font-weight: normal;"><table width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="line-height: 11px;">' . strtoupper($projectLocation) . '</td></tr></table>
        </td>
        <td width="26%"> <i>PAYMENT TERMS(MONTH):</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . $paymentTerms . '</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>PHASE:</i></td>
        <td width="15%" align="center" style="' . $borderBottom . '"><i>' . $phase . '</i></td>
        <td width="21%"></td>
        <td width="26%"> <i>TOTAL CONTRACT PRICE:</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . number_format((float) $totalPropertyPrice, 2, '.', ',') . '</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>BLOCK:</i></td>
        <td width="15%" align="center" style="' . $borderBottom . '"><i>' . $block . '</i></td>
        <td width="21%"></td>
        <td width="26%"> <i>DOWNPAYMENT:</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . number_format((float) $downpaymentAmount, 2, '.', ',') . '</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>LOT:</i></td>
        <td width="15%" align="center" style="' . $borderBottom . '"><i>' . $lot . '</i></td>
        <td width="21%"></td>
        <td width="26%"> <i>MONTHLY AMORTIZATION:</i></td>
        <td width="16%" align="right" style="' . $borderBottom . '"><i>' . $totalMonthlyAmortization . '</i></td>
      </tr>
      <tr>
        <td width="21%" align="center"><i>CUSTOMER NO:</i></td>
        <td width="15%" style="' . $borderBottom . '"><i>' . $customerNo . '</i></td>
        <td width="64%" colspan="4"></td>
      </tr>
      <tr>
        <td colspan="6"><table width="100%" cellpadding="0" cellspacing="2.7">
          <tr><td style="border-bottom: 2px solid black;"></td></tr></table>
        </td>
      </tr>
      <tr>
        <td width="10.33%"></td>
        <td width="23%"><i>DATE</i></td>
        <td width="10.33%"></td>
        <td width="23%"><i>' . ($paymentTerms > 30 ? 'DATE' : '') . '</i></td>
        <td width="10.33%"></td>
        <td width="23%"><i>' . ($paymentTerms > 60 ? 'DATE' : '') . '</i></td>
      </tr>
      ' . $monthlyAmortData . '
      <tr>
        <td colspan="6">
          <table width="100%" cellpadding="0" cellspacing="2.7">
            <tr><td style="border-bottom: 1.3px solid black;"></td></tr>
            <tr>
              <td style="font-size: 11pt; border-top: 1.3px solid black;"><table width="100%">
                <tr>
                  <td width="55%" align="right">TOTAL</td>
                  <td width="40%" align="center">' . number_format((float) $totalPropertyPrice, 2, '.', ',') . '</td>
                  <td width="5%"></td>
                </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr><td colspan="6" style="height:22px;"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2" style="font-weight: normal;"><i>Prepared By:</i></td>
        <td width="16%"></td>
        <td width="40%" colspan="2" style="font-weight: normal;"><i>Received By:</i></td>
      </tr>
      <tr><td colspan="6" style="height:22px;"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2" style="font-weight: normal;"><table width="100%" cellpadding="0" cellspacing="0">
          <tr><td align="center" style="font-size: 11pt; border-bottom: 2.5px solid black; height:17px;"></td></tr></table>
        </td>
        <td width="16%"></td>
        <td width="40%" colspan="2"><table width="100%" cellpadding="0" cellspacing="2.7">
          <tr><td align="center" style="font-size: 11pt; border-bottom: 2.5px solid black; height:13px;">' . strtoupper($clientName) . '</td></tr></table>
        </td>
      </tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2" align="center" style="font-size: 10.5pt; font-weight: normal;">Position</td>
        <td width="16%"></td>
        <td width="40%" colspan="2" align="center" style="font-size: 10.5pt; font-weight: normal;">LOT BUYER</td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2"style="font-weight: normal;"><i>Approved By:</i></td>
        <td width="16%"></td>
        <td width="40%" colspan="2" style="font-weight: normal;"><table width="100%" cellpadding="0" cellspacing="2.7">
        <tr><td style="border-bottom: 2.5px solid black; height:13px;"><i>Date:</i></td></tr></table>
      </td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2" style="font-weight: bold;"><table width="100%" cellpadding="0" cellspacing="0">
          <tr><td align="center" style="font-size: 12pt; border-bottom: 1.8px solid black; height:17px;">' . $chiefName . '</td></tr></table>
        </td>
        <td width="56%" colspan="3"></td>
      </tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2" align="center" style="font-size: 10.5pt; font-weight: normal;">' . $chiefTitle . '</td>
        <td width="56%" colspan="3"></td>
      </tr>
    </table>';

    $pdf->writeHTML($propertyTable, true, false, false, false, '');

    $pdf->Output($title, $action);
  } else {
    echo getResponseStatus($code, $data);
  }

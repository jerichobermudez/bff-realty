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

    // Create new PDF document
    $pdf = new TCPDF();

    // Set Document Information
    $today = date('F d, Y');
    $title = 'SOA-' . $clientName . '; ' . $customerNo . '; ' . $today . '.pdf';
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
    $height = 279.4;

    // Add Page
    $pdf->AddPage('P', [$width, $height]);

    // Page Content
    $chiefName = 'OFFICER NAME';
    $chiefTitle = 'POSITION';
    $border = 'border: 0.1px solid black; ';
    $borderBottom = 'border-bottom: 1.3px solid black;';
    $propertyTable = '<table cellpadding="1" width="100%" style="font-weight: bold; font-size: 9.5;">
      <tr><td colspan="6" align="center" style="font-size: 10pt;"></td></tr>
      <tr><td colspan="6" align="center" style="font-size: 10pt;">STATEMENT OF ACCOUNT</td></tr>
      <tr>
        <td colspan="6" align="center" style="font-weight: normal; font-size: 10pt;">As of ' . $today . '</td>
      </tr>
      <tr><td colspan="6" style="height: 18px;"></td></tr>
      <tr>
        <td width="5%" rowspan="3"></td>
        <td width="25%" align="center"><i>CUSTOMER NO:</i></td>
        <td width="42%" colspan="2" style="' . $borderBottom . '"><i>' . $customerNo . '</i></td>
        <td width="28%" rowspan="3" colspan="2"></td>
      </tr>
      <tr>
        <td width="25%" align="center"><i>CLIENT&rsquo;S NAME:</i></td>
        <td width="42%" colspan="2" style="' . $borderBottom . '"><i>' . strtoupper($clientName) . '</i></td>
      </tr>
      <tr>
        <td width="25%" align="center"><i>ADDRESS:</i></td>
        <td width="42%" colspan="2" style="' . $borderBottom . '"><i>' . strtoupper($clientAddress) . '</i></td>
      </tr>
      <tr>
        <td colspan="6"><table width="100%" cellpadding="0" cellspacing="2.7">
          <tr><td style="border-bottom: 2px solid black; height:17px;"></td></tr></table>
        </td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>PROJECT NAME:</i></td>
        <td width="42%" colspan="2" align="center" style="' . $borderBottom . '">' . strtoupper($projectName) . '</td>
        <td width="28%" rowspan="2" colspan="2"></td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>ADDRESS:</i></td>
        <td width="42%" colspan="2" style="' . $borderBottom . '">' . strtoupper($projectLocation) . '</td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>PHASE:</i></td>
        <td width="20%" align="center" style="' . $borderBottom . '"><i>' . $phase . '</i></td>
        <td width="50%" rowspan="4" colspan="3"></td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>BLOCK:</i></td>
        <td width="20%" align="center" style="' . $borderBottom . '"><i>' . $block . '</i></td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>LOT:</i></td>
        <td width="20%" align="center" style="' . $borderBottom . '"><i>' . $lot . '</i></td>
      </tr>
      <tr>
        <td width="30%" colspan="2" align="center"><i>DATE OF RESERVATION:</i></td>
        <td width="20%" align="center" style="' . $borderBottom . '"><i>' . ($downpaymentDate ? date('F d, Y', strtotime($downpaymentDate)) : '') . '</i></td>
      </tr>
      <tr>
        <td colspan="6"><table width="100%" cellpadding="0" cellspacing="2.7">
          <tr><td style="border-bottom: 1.3px solid black;"></td></tr><tr><td style="border-top: 1.3px solid black;"></td></tr></table>
        </td>
      </tr>
      <tr>
        <td width="4%" rowspan="6"></td>
        <td width="46%" colspan="2"><i>LOT AREA:</i></td>
        <td width="25%" colspan="2" align="center" style="font-weight: normal;"><i>' . $lotArea . '</i></td>
        <td width="25%" rowspan="4"></td>
      </tr>
      <tr>
        <td width="46%" colspan="2"><i>PRICE PER SQM:</i></td>
        <td width="25%" colspan="2" align="center" style="font-weight: normal;"><i>' . number_format((float) $pricePerSqm, 2, '.', ',') . '</i></td>
      </tr>
      <tr>
        <td width="46%" colspan="2"><i>PAYMENT TERMS(MONTHS):</i></td>
        <td width="25%" colspan="2" align="center" style="font-weight: normal;"><i>' . $paymentTerms . '</i></td>
      </tr>
      <tr>
        <td width="46%" colspan="2"><i>MONTHLY AMORTIZATION:</i></td>
        <td width="25%" colspan="2" align="center" style="font-weight: normal;"><i>' . $totalMonthlyAmortization . '</i></td>
      </tr>
      <tr>
        <td width="46%" colspan="2"><i>TOTAL CONTRACT PRICE:</i></td>
        <td width="19%" colspan="2" align="right" style="font-weight: normal;"><i>' . number_format((float) $totalPropertyPrice, 2, '.', ',') . '</i></td>
        <td width="31%"></td>
      </tr>
      <tr>
        <td width="35%" colspan="2" style="font-size: 9pt; font-weight: normal;"><i>(with 10% interest for more than 60 months term)</i></td>
        <td width="61%" colspan="3"></td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="46%" colspan="2"><i>LESS: PAYMENT/S:</i></td>
        <td width="19%" colspan="2" align="right" style="font-weight: normal;"><i>' . $totalPayment . '</i></td>
        <td width="31%"></td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="46%" colspan="2"><i>TOTAL AMOUNT DUE:</i></td>
        <td width="19%" colspan="2" align="right" style=""><i>' . $totalOutstandingBalance . '</i></td>
        <td width="31%"></td>
      </tr>
      <tr><td colspan="6"></td></tr>
      <tr>
        <td width="4%"></td>
        <td width="65%" colspan="4" style="font-weight: normal;"><i>Note: The last payment considered in this statement was on ' . ($lastPaymentDate ? date('F d, Y', strtotime($lastPaymentDate)) : '') . ' under A.R. No. ' . $lastRefNo . ' in the amount of ' . $lastPaymentAmount . '</i></td>
        <td width="31%"></td>
      </tr>
      <tr>
        <td colspan="6"><table width="100%" cellpadding="0" cellspacing="2.7">
          <tr><td style="border-bottom: 1.3px solid black; height: 22px;"></td></tr><tr><td style="border-top: 1.3px solid black;"></td></tr></table>
        </td>
      </tr>
      <tr>
        <td width="4%"></td>
        <td width="40%" colspan="2"style="font-weight: normal;"><i>Prepared By:</i></td>
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

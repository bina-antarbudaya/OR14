<?php

ini_set('max_execution_time', 600);

// Create new PDF document
// Disable Unicode for performance
$pdf = new SkynetPDF('P', PDF_UNIT, 'A4', false, 'ISO-8859-1', true);

// Disable font subsetting for performance
$pdf->setFontSubsetting(false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Yayasan Bina Antarbudaya');
$pdf->SetTitle('Tanda Peserta Seleksi Bina Antarbudaya');
$pdf->SetSubject('Tanda Peserta Seleksi Bina Antarbudaya');
$pdf->SetKeywords('Bina Antarbudaya');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(10, 10, 10, 10);

// Set auto page breaks
$pdf->SetAutoPageBreak(FALSE, 0);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// Add a page
$pdf->AddPage();

// Load template
$pdf->setSourceFile(HELIUM_PARENT_PATH . '/assets/kartu-peserta-tpl-alt.pdf');
$pdf->useTemplate($pdf->importPage(1));

// Set line height
$pdf->setCellHeightRatio(1.6);

// Print test ID
$code = $applicant->test_id;
$pdf->setFont('helvetica', 'B', 28);
$pdf->MultiCell(180, 30, $code, 0, 'L', false, 1, 8.8, 28);

// Print applicant info
$pdf->setFont('helvetica', 'B', 12);
$pdf->MultiCell(120, 30, $applicant->sanitized_full_name, 0, 'L', false, 1, 8.8, 57.5);
$pdf->MultiCell(120, 30, $applicant->sanitized_high_school_name, 0, 'L', false, 1, 8.8, 69);
$pdf->MultiCell(120, 30, $applicant->expires_on->format('l, j F Y'), 0, 'L', false, 1, 8.8, 93);
$pdf->MultiCell(100, 30, $applicant->chapter->chapter_name, 0, 'L', false, 1, 98.8, 57.5);
$pdf->MultiCell(100, 30, $applicant->applicant_mobilephone, 0, 'L', false, 1, 98.8, 69);

// Print address
$st = str_replace("\n", ', ', $applicant->applicant_address_street);
$address_oneline = "$st, {$applicant->applicant_address_city} {$applicant->applicant_address_postcode}";
if (strlen($address_oneline) > 60) {
	// Long addresses
	$pdf->setFont('helvetica', '', 9);
	$pdf->setCellHeightRatio(1.2);
	$pdf->MultiCell(140, 30, $address_oneline, 0, 'L', false, 1, 8.8, 81.5);
}
else {
	// Short addresses
	$pdf->setFont('helvetica', '', 12);
	$pdf->MultiCell(140, 30, $address_oneline, 0, 'L', false, 1, 8.8, 81);
}

// Print QR code of applicant control URI
$code = PathsComponent::build_url(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id));
$pdf->write2DBarcode($code, 'QRCODE', 152, 94, 20, 20);

// Print 1D barcode of test ID
$pdf->write1DBarcode($applicant->test_id . chr(13), 'C93', 10, 42, 120, 8);

// Print participant photo
if ($picture) {
	$picture_path = $picture->get_cropped_path();
	$pdf->Image($picture_path, 152, 25, 48, 64);
}

// ---------------------------------------------------------

// Close and output PDF document
// TODO: Save this somewhere so we only need to generate once

$pdf->Output('kartu-peserta.pdf', 'I');
exit;
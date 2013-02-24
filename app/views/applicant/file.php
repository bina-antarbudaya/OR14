<?php

// PDF

// create new PDF document
$pdf = new SkynetPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Yayasan Bina Antarbudaya');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setSourceFile(HELIUM_PARENT_PATH . '/assets/statements-tpl.pdf');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10, 10);

//set auto page breaks
$pdf->SetAutoPageBreak(FALSE, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

switch ($file):

case 'acceleration_statement':
	
$pdf->SetTitle('Surat Pernyataan Siswa Akselerasi');
$pdf->AddPage();
$pdf->useTemplate($pdf->importPage(1));

$pdf->setCellHeightRatio(1.6);

// Coordinates:
// X: 60 mm
// Y: starting at 97.7 mm
//    + increments of 6 mm

$st = str_replace("\n", ', ', $applicant->applicant_address_street);
$address = "$st, {$applicant->applicant_address_city} {$applicant->applicant_address_postcode}";
$texts = array($applicant->sanitized_full_name, $applicant->date_of_birth->format('j F Y'), $applicant->test_id, $address, $applicant->sanitized_high_school_name);

$pdf->setFont('helvetica', 'B', 12);
$x = 60;
$y = 96.7;
foreach ($texts as $t) {
	$pdf->MultiCell(120, 30, $t, 0, 'L', false, 1, $x, $y);
	$y += 6;
}

// ---------------------------------------------------------

//Close and output PDF document
//TODO: Save this somewhere so we only need to generate once
$pdf->Output('acceleration-statement.pdf', 'I');
	
break;

case 'parents_statement':

$pdf->SetTitle('Surat Izin Orang Tua/Wali');
$pdf->AddPage();
$pdf->useTemplate($pdf->importPage(2));

$pdf->setCellHeightRatio(1.6);

// Coordinates:
// X: 60 mm
// Y: starting at 60.6 mm
//    + increments of 6 mm

$st = str_replace("\n", ', ', $applicant->applicant_address_street);
$address = "$st, {$applicant->applicant_address_city} {$applicant->applicant_address_postcode}";
$texts = array($applicant->sanitized_full_name, $applicant->test_id);

$pdf->setFont('helvetica', 'B', 12);
$x = 60;
$y = 61.1;
foreach ($texts as $t) {
	$pdf->MultiCell(120, 30, $t, 0, 'L', false, 1, $x, $y);
	$y += 6;
}

// ---------------------------------------------------------

//Close and output PDF document
//TODO: Save this somewhere so we only need to generate once
$pdf->Output('parental-permission.pdf', 'I');

break;

case 'recommendation_letters':

$this->http_redirect('/assets/rekomendasi.pdf');

endswitch;
exit;
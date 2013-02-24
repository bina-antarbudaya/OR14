<?php

ini_set('memory_limit', '512M');

$all = array(array('A' => 'test id', 'B' => 'name', 'C' => 'school', 'D' => 'phone', 'E' => 'email', 'F' => 'address', 'G' => 'confirmed'));

// Header cells
$cells = array(
	'B3' => 'No. Peserta',
	'C3' => 'NAMA',
	'D3' => 'Sekolah',
	'E3' => 'Tahap I', // E3:J3
	'K3' => 'Tahap II', // K3:N3
	'O3' => 'Tahap III', // O3:P3
	'Q3' => 'NILAI AKUMULATIF TERBOBOT',
	'R3' => 'Nilai Rata-rata Raport SMA',
	'S3' => 'Sejarah Kepemimpinan & Kemandirian (A/B/C)',
	'T3' => 'Rekomendasi Pewawancara (Sangat Diunggulkan/Diunggulkan/Dapat Diterima/Ditolak)',
	'U3' => 'Rank Hasil Seleksi',
	'V3' => 'LULUS / TIDAK LULUS',
	'W3' => 'Keterangan',
	'E4' => 'Pengetahuan Umum',
	'G4' => 'Bahasa Inggris',
	'I4' => 'Esai',
	'K4' => 'Wawancara Kepribadian',
	'M4' => 'Wawancara Bahasa Inggris',
	'O4' => 'Interaksi Kelompok',
);

$i = 6;

$original_cols = array('E', 'G', 'I', 'K', 'M', 'O');
$weighted_cols = array('F' => '0.1', 'H' => '0.1', 'J' => '0.15', 'L' => '0.3', 'N' => '0.1', 'P' => '0.25');

foreach ($original_cols as $l)
	$cells[$l . '5'] = 'Nilai';
foreach ($weighted_cols as $l => $w)
	$cells[$l . '5'] = 'Nilai terbobot (' . ($w * 100) . '%)';

// load data
foreach ($participants as $a) {
	$cells['B' . $i] = $a->test_id;
	$cells['C' . $i] = str_replace('  ', ' ', $a->sanitized_full_name);
	$cells['D' . $i] = $a->sanitized_high_school_name;
	foreach ($original_cols as $l)
		$cells[$l . $i] = 0;

	$weighted_cells = array();
	foreach ($weighted_cols as $k => $v) {
		$cells[$k . $i] = '=' . $v . '*' . chr(ord($k) - 1) . $i;
		$weighted_cells[] = $k . $i;
	}
	
	$cells['Q' . $i] = '=SUM(' . implode(',', $weighted_cells) . ')';
	
	$grade = str_replace(',', '.', $a->grades_y10t1_average);
	if (is_numeric($grade)) {
		$grade = (float) $grade;
		if ($grade <= 10)
			$grade = $grade * 10;
		while ($grade > 100)
			$grade = $grade / 10;
		$grade = round($grade);
	}

	$cells['R' . $i] = $grade;

	$i++;
}

$i--;
$last_cell = 'W' . $i;
$last_row = $i;

// Create new PHPExcel object
$xlsx = new PHPExcel();

// Set properties
$xlsx->getProperties()->setCreator("Bina Antarbudaya")
							 ->setLastModifiedBy("Bina Antarbudaya")
							 ->setTitle("Tabulasi Kumulatif Seleksi Bina Antarbudaya")
							 ->setSubject("Tabulasi Kumulatif Seleksi Bina Antarbudaya")
							 ->setDescription("Tabulasi kumulatif Seleksi Bina Antarbudaya")
							 ->setKeywords("bina antarbudaya binaantarbudaya")
							 ->setCategory("List");


$sheet = $xlsx->setActiveSheetIndex(0);
$sheet->setTitle('Tabulasi Kumulatif');

$widths = array(
	'A' => 5,
	'B' => 20,
	'C' => 40,
	'D' => 30,
	'T' => 24,
	'W' => 30
);

foreach ($original_cols as $l)
	$widths[$l] = 6;
foreach (array_keys($weighted_cols) as $l)
	$widths[$l] = 18;
for ($i = ord('Q'); $i <= ord('V'); $i++)
	if ($i != ord('T'))
		$widths[chr($i)] = 14;

foreach ($widths as $l => $w)
	$sheet->getColumnDimension($l)->setWidth($w);

for ($i = 3; $i <= 5; $i++)
	$sheet->getRowDimension($i)->setRowHeight(20);
for ($i = 6; $i <= $last_row; $i++)
	$sheet->getRowDimension($i)->setRowHeight(18);
	
// cell merges
$merges = array(
	'E3:J3',
	'K3:N3',
	'O3:P3',
);
for ($i = ord('E'); $i < ord('Q'); $i += 2)
	$merges[] = chr($i) . '4:' . chr($i + 1) . '4';
for ($i = ord('B'); $i <= ord('D'); $i ++)
	$merges[] = chr($i) . '3:' . chr($i) . '5';
for ($i = ord('Q'); $i <= ord('W'); $i ++)
	$merges[] = chr($i) . '3:' . chr($i) . '5';

foreach ($merges as $m)
	$sheet->mergeCells($m);

$wraps = array();
for ($i = ord('B'); $i <= ord('D'); $i ++)
	$wraps[] = chr($i) . '3';
for ($i = ord('Q'); $i <= ord('W'); $i ++)
	$wraps[] = chr($i) . '3';

foreach ($wraps as $cell)
	$sheet->getStyle($cell)->getAlignment()->setWrapText(true);

// content
$sheet->getStyle('B6:' . $last_cell)->applyFromArray(
		array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
		)
);
$sheet->getStyle('B6:B' . $last_row)->applyFromArray(
		array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		)
);
$sheet->getStyle('E6:W' . $last_row)->applyFromArray(
		array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		)
);

// header
$sheet->getStyle('B3:W5')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'borders' => array(
				'allborders'     => array(
 					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
 				),
			),
		)
);

$i = 1;
foreach ($cells as $k => $v) {
	$sheet->setCellValue($k, $v);
}

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tabulasi-kumulatif.xlsx"');
header('Cache-Control: max-age=0');

$writer = PHPExcel_IOFactory::createWriter($xlsx, 'Excel2007');
$writer->save('php://output');
exit;


<?php


switch ($output_mode) {
	
	case 'xlsx':
	
		ini_set('memory_limit', '512M');
	
		$all = array(array('A' => 'test id', 'B' => 'name', 'C' => 'school', 'D' => 'phone', 'E' => 'email', 'F' => 'address', 'G' => 'confirmed'));

		$cells = array(
			'A1' => 'No. Peserta',
			'B1' => 'Nama Lengkap',
			'C1' => 'Asal Sekolah',
			'D1' => 'No. HP',
			'E1' => 'Alamat E-mail',
			'F1' => 'Alamat Rumah',
			'G1' => 'V'
		);
		$i = 2;
		foreach ($participants as $a) {
			$cells['A' . $i] = $a->test_id;
			$cells['B' . $i] = $a->sanitized_full_name;
			$cells['C' . $i] = $a->sanitized_high_school_name;
			$cells['D' . $i] = $a->applicant_mobilephone;
			$cells['E' . $i] = $a->applicant_email;
			$cells['F' . $i] = $a->applicant_address;
			$cells['G' . $i] = $a->confirmed;
			$i++;
		}
		
		// var_dump($all); exit;

		// Create new PHPExcel object
		$xlsx = new PHPExcel();

		// Set properties
		$xlsx->getProperties()->setCreator("Bina Antarbudaya Skynet")
									 ->setLastModifiedBy("Bina Antarbudaya Skynet")
									 ->setTitle("Bina Antarbudaya Applicant List")
									 ->setSubject("Bina Antarbudaya Applicant List")
									 ->setDescription("List of Bina Antarbudaya participants.")
									 ->setKeywords("bina antarbudaya binaantarbudaya")
									 ->setCategory("List");


		foreach (array('all') as $k => $name) {
			// set active sheet
			if ($k > 0)
				$xlsx->createSheet();
			$sheet = $xlsx->setActiveSheetIndex(0);
			$sheet->setTitle('Participants');

			// set column width
			$sheet->getColumnDimension('A')->setWidth(19); // id
			$sheet->getColumnDimension('B')->setWidth(40); // name
			$sheet->getColumnDimension('C')->setWidth(40); // school
			$sheet->getColumnDimension('D')->setWidth(13); // phone
			$sheet->getColumnDimension('E')->setWidth(32); // email
			$sheet->getColumnDimension('F')->setWidth(80); // address
			$sheet->getColumnDimension('G')->setWidth(2); // confirmed

			$sheet->getStyle('A1:G1')->applyFromArray(
					array(
						'font'    => array(
							'bold'      => true
						),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						),
						'borders' => array(
							'bottom'     => array(
			 					'style' => PHPExcel_Style_Border::BORDER_THIN
			 				)
						),
					)
			);

			$i = 1;
			foreach ($cells as $k => $v) {
				$sheet->setCellValue($k, $v);
				if (is_numeric($v))
					$sheet->getStyle($k)->getNumberFormat()->setFormatCode(str_repeat('0', strlen($v)));
			}
			// foreach ($all as $cols) {
			// 	foreach ($cols as $k => $v) {
			// 		$cell = $k . $i;
			// 		$sheet->setCellValue($cell, $v);
			// 		if (is_numeric($v))
			// 			$sheet->getStyle($cell)->getNumberFormat()->setFormatCode(str_repeat('0', strlen($v)));
			// 	}
			// 	$i++;
			// }
		}

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		// $xlsx->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="participants.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = PHPExcel_IOFactory::createWriter($xlsx, 'Excel2007');
		$writer->save('php://output');
		exit;
	
	
}
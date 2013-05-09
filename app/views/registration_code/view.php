<?php

$exp = $expires_on->format('l, j F Y, H.i.s ') . $timezone;

if ($format == 'xlsx'):

// require_once HELIUM_PARENT_PATH . '/app/includes/PHPExcel.php';

// Create new PHPExcel object
$xlsx = new PHPExcel();

// Set properties
$xlsx->getProperties()->setCreator("Bina Antarbudaya")
							 ->setLastModifiedBy("Bina Antarbudaya")
							 ->setTitle("Bina Antarbudaya Registration Code List")
							 ->setSubject("Bina Antarbudaya Registration Code List")
							 ->setDescription("List of Bina Antarbudaya registration codes.")
							 ->setKeywords("bina antarbudaya binaantarbudaya")
							 ->setCategory("List");

$cells = array('A1' => 'PIN', 'B1' => 'Chapter', 'C1' => 'Kadaluarsa');

$exp = $expires_on->format('l, j F Y');

$i = 2;
foreach ($codes as $code) {
	$cells['A' . $i] = $code->token;
	$cells['B' . $i] = $chapter_name;
	$cells['C' . $i] = $exp;
	$i++;
}

$sheet = $xlsx->setActiveSheetIndex(0);
$sheet->setTitle($chapter_name);

// Column widths
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);

// Cell styles
$sheet->getStyle('A1:C1')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'borders' => array(
				'bottom'     => array(
 					'style' => PHPExcel_Style_Border::BORDER_THIN
 				)
			),
		)
);

$sheet->getStyle('A2:A' . $i)->getFont()->setName('Consolas');

foreach ($cells as $cell => $value) {
	$sheet->setCellValue($cell, $value);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pinpendaftaran.xlsx"');
header('Cache-Control: max-age=0');

$writer = PHPExcel_IOFactory::createWriter($xlsx, 'Excel2007');
$writer->save('php://output');

elseif ($format == 'csv'):

$exp = $expires_on->format('Y-m-d H.i.s ') . $timezone;

@header('Content-Type: text/csv');
@header('Content-Disposition: attachment; filename=pinpendaftaran.csv');

echo "PIN,Chapter,Kadaluarsa\n";

foreach ($codes as $code)
	printf("%s,%s,%s\n", $code->token, $chapter_name, $exp);

else:

$this->header(); ?>
<div class="container">
	<?php if ($error): ?>
	<div class="message error">
		<header>Pembukaan PIN gagal</header>
		<p><?php echo $error ?></p>
	</div>
	<?php else: ?>
	<div class="message">
		<header>Perhatian</header>
		<p>Pastikan tidak ada PIN pendaftaran yang tercetak dua kali.</p>
		<p><a href="#" onclick="window.print()">Cetak laman ini</a></p>
	</div>
	<table class="codes">
		<thead>
			<tr>
				<td colspan="2">Chapter <strong><?php echo $chapter_name ?></strong> - Berlaku sampai <strong><?php echo $exp ?></strong></td>
			</tr>
		</thead>
		<?php
		foreach ($codes as $i => $b):
		?>
		<?php if ($i % 12 == 0 && $i != 0): ?>
		</tbody>
	</table>
	
	<table class="codes">
		<thead>
			<tr>
				<td colspan="2">Chapter <strong><?php echo $chapter_name ?></strong> - Berlaku sampai <strong><?php echo $exp ?></strong></td>
			</tr>
		</thead>
		<tbody>
		<?php endif; if ($i % 2 == 0) echo '<tr>' ?>

			<td class="chapter_name"<?php if ($b->availability == false) echo ' style="opacity: 0.2"' ?>>
				<span class="header">PIN Pendaftaran Seleksi Bina Antarbudaya</span>
					<img src="<?php L('/assets/dove.png') ?>" alt="">
				<span class="chapter-name">Chapter <strong><?php echo $chapter_name ?></strong></span>
				<span class="token"<?php if ($b->availability == false) echo ' style="text-decoration: line-through"' ?>><?php echo $b->token ?></span>
				<span class="expires-on">Berlaku sampai <strong><?php echo $exp ?></strong></span>
				<span class="footer"><?php L('/') ?></span>
			</td>
		<?php if ($i % 2 == 1) echo '</tr>' ?>

		<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php endif; ?>
</div>
<?php $this->footer();

endif;

?>
<?php

function print_address($a, $name, $kota = true, $provinsi = true, $kodepos = true, $telepon = true, $hp = true, $fax = true, $email = true) {
	?>
	<p class="value block"><?php
	$v = $name . '_address_street';
	$a1 = $a->$v;
	$k = $name . '_address_city';
	if ($kota && $a->$k) {
		$a1 .= "\n" . $a->$k;

		$v = $name . '_address_postcode';
		if ($kodepos && $a->$v)
			$a1 .= ' ' . $a->$v;
	}

	$v = $name . '_address_province';
	if ($provinsi && $a->$v)
		$a1 .= ($a->$k ? ', ' : "\n") . $a->$v;

	echo nl2br(trim($a1));
	?>
	</p>
	<?php
	$v = $name . '_mobilephone';
	if ($hp && $a->$v):
	?>
	<label class="subpoint">HP</label>
	<span class="value"><?php echo $a->$v; ?></span>
	<br>
	<?php
	endif;
	$v = $name . '_phone_number';
	if ($telepon && $a->$v):
	?>
	<label class="subpoint">Telepon</label>
	<span class="value"><?php $v = $name . '_phone_areacode'; if ($a->$v) echo '(' . $a->$v . ') '; ?><?php $v = $name . '_phone_number'; echo $a->$v; ?></span>
	<br>
	<?php
	endif;
	$v = $name . '_fax_number';
	if ($fax && $a->$v):
	?>
	<label class="subpoint">Faks</label>
	<span class="value"><?php $v = $name . '_fax_areacode'; if ($a->$v) echo '(' . $a->$v . ') '; ?><?php $v = $name . '_fax_number'; echo $a->$v; ?></span>
	<br>
	<?php
	endif;
	$v = $name . '_email';
	if ($email && $a->$v):
	?>
	<label class="subpoint">E-mail</label>
	<span class="value"><?php echo $a->$v; ?></span>
	<?php endif;
}

?>
<!DOCTYPE html>

<html lang="id">

	<head>
		<meta charset="utf-8">
		<title>Formulir Pendaftaran Pendaftaran Seleksi Bina Antarbudaya</title>
		<base href="<?php L('/') ?>">
		<!-- <link rel="stylesheet" href="assets/css/applicant/transcript.css"> -->
		<style>
		<?php readfile(HELIUM_PARENT_PATH . '/assets/css/deprecated/global/reset.css'); ?>
		<?php readfile(HELIUM_PARENT_PATH . '/assets/css/deprecated/applicant/transcript.css'); ?>
		</style>
	</head>
	<body>

<!-- begin form -->

<div class="instructions">
	Cetaklah transkrip ini dengan orientasi <strong>portrait</strong> pada kertas <strong>HVS A4</strong>.
	Gunakan <strong>Firefox/Safari/Chrome/Internet Explorer</strong> untuk mencetak, <strong>dilarang</strong> menggunakan Word.
	<br>
	Untuk menyimpan transkrip ini, tekan Ctrl+S (atau Cmd+S di Mac) dan pilih jenis '<em>Web page, complete</em>'.
	<br>
	Pada saat mencetak, pastikan tulisan pada formulir ini terbaca seluruhnya (tidak terlalu kecil).
	Petunjuk ini tidak akan ditampilkan saat mencetak.
	<br>
	<p><a class="print-link" href="javascript:window.print()">Cetak laman ini</a></p>
</div>

<section id="cover" style="page-break-after: always">
	<img src="assets/logo.png" alt="Bina Antarbudaya" style="font-size: 18pt">
	<h1>Transkrip Formulir Pendaftaran Seleksi</h1>
	<?php if ($picture): ?>
	<img src="http://seleksi.bina-antarbudaya.info/uploads/<?php echo $picture->cropped_filename; ?>" width="300" height="400" alt="Foto 4x6">
	<?php else: ?>
	<br><br><br><br>(Foto 4x6)<br><br><br><br>
	<?php endif; ?>
	<h1 class="name"><?php echo $applicant->sanitized_full_name ?></h1>
	<h2 class="test-id"><?php echo $applicant->test_id; ?></h2>
	<table class="cover-table">
		<tr>
			<th class="programs">Pilihan Program</th>
			<th class="school">Asal Sekolah</th>
			<th class="chapter">Chapter</th>
		</tr>
		<tr>
			<td class="programs"><?php
			$p = array();
			$v = array('program_afs' => 'AFS', 'program_yes' => 'YES', 'program_jenesys' => 'JENESYS');
			foreach ($v as $i => $j) {
				if ($form->values[$i])
					$p[] = $j;
			}
			echo implode(', ', $p);
			?></td>
			<td class="school"><?php echo $applicant->sanitized_high_school_name . ' ';
			if ($a->in_pesantren)
				echo 'AFS';
			if ($a->in_pesantren && $a->in_acceleration_class)
				echo ', ';
			if ($a->in_acceleration_class)
				echo 'YES';
			?></td>
			<td class="chapter"><?php echo $applicant->chapter->chapter_code ?></td>
	</table>
</section>

<!-- page break -->
<section class="pane" id="pribadi">
	<h1>Data Pribadi</h1>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('first_name', 'Nama Depan', 'required') ?></td>
			<td class="field"><?php $form->text('first_name', 'medium'); ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('middle_name', 'Nama Tengah') ?></td>
			<td class="field"><?php $form->text('middle_name', 'medium'); ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('last_name', 'Nama Belakang') ?></td>
			<td class="field"><?php $form->text('last_name', 'medium'); ?></td>
		</tr>
		<tr>
			<td class="label">Tempat dan Tanggal Lahir</td>
			<td class="field"><span class="value"><?php echo $a->place_of_birth . ', ' . $a->date_of_birth->format('j F Y') ?></td>
		</tr>
		<tr>
			<td class="label">Alamat Surel (E-mail)</td>
			<td class="field"><span class="value"><?php echo $a->applicant_email ?></span></td>
		</tr>
		<tr>
			<td class="label">Nomor Ponsel</td>
			<td class="field"><span class="value"><?php echo $a->applicant_mobilephone ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat Lengkap</td>
			<td class="field"><?php print_address($a, 'applicant', true, true, true, true, false, true, false); ?> </td>
		</tr>
		<tr>
			<td class="label">Jenis Kelamin</td>
			<td class="field">
				<span class="value"><?php $map = array('F' => 'Perempuan', 'M' => 'Laki-laki'); echo $map[$a->sex] ?></span>
			</td>
		</tr>
		<tr>
			<td class="label"></td>
			<td class="field">
				<label class="subpoint">Tinggi Badan</label> <span class="value"><?php echo $a->body_height ?></span> cm
				<br>
				<label class="subpoint">Berat Badan</label> <span class="value"><?php echo $a->body_weight ?></span> kg
				<br>
				<label class="subpoint">Gol. Darah</label>
				<span class="value"><?php echo $a->blood_type ?></span>
			</td>
		</tr>
		<tr>
			<td class="label">Kewarganegaraan</td>
			<td class="field"><span class="value"><?php echo $a->citizenship ?></span><br>
				
			</td>
		</tr>
		<tr>
			<td class="label">Agama</td>
			<td class="field"><span class="value"><?php echo $a->religion ?></span></td>
		</tr>
	</table>
</section>

<section class="pane" id="keluarga">
	<h1>Keluarga</h1>
	<!-- poin 9–11 -->
	<?php

	foreach(array('father' => 'Ayah', 'mother' => 'Ibu') as $n => $parent):
	?>
	<h2><?php echo $parent; ?></h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php echo "Nama Lengkap $parent" ?></td>
			<td class="field"><span class="value"><?php $v = $n . '_full_name'; echo $a->$v; ?></a></td>
		</tr>
		<tr>
			<td class="label">Pendidikan Terakhir</td>
			<td class="field"><?php $v = $n . '_education'; echo $a->$v; ?></td>
		</tr>
		<tr>
			<td class="label">Pekerjaan/Jabatan</td>
			<td class="field"><?php $v = $n . '_occupation'; echo $a->$v; ?></td>
		</tr>
		<tr>
			<td class="label">Alamat Surel (E-mail)</td>
			<td class="field"><?php $v = $n . '_office_email'; echo $a->$v; ?></td>
		</tr>
		<tr>
			<td class="label">Nomor Ponsel</td>
			<td class="field"><?php $v = $n . '_office_mobilephone'; echo $a->$v; ?></td>
		</tr>
		<tr>
			<td class="label">Nama dan Alamat Kantor</td>
			<td class="field">
				<span class="value"><?php $v = $n . '_office_name'; echo $a->$v ?></span>
				<br>
				<?php print_address($a, $n . '_office', true, true, false, true, false, true, false) ?>
			</td>
		</tr>
	</table>
	<?php endforeach; ?>
	
	<h2>Wali</h2>
	<?php if ($a->guardian_full_name): ?>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('guardian_full_name', "Nama Lengkap Wali") ?></td>
			<td class="field"><span class="value"><?php echo $a->guardian_full_name ?></span><br>
				
			</td>
		</tr>
		
		<tr>
			<td class="label"><?php $form->label('guardian_relationship_to_applicant', 'Hubungan dengan Adik') ?></td>
			<td class="field"><span class="value"><?php echo $a->guardian_relationship_to_applicant ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('guardian_email', 'Alamat Surel (E-mail)') ?></td>
			<td class="field"><span class="value"><?php echo $a->guardian_email ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('guardian_address_street', 'Alamat Wali') ?></td>
			<td class="field"><?php print_address($a, 'guardian', true, true, false, true, true, false, false) ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('guardian_occupation', 'Pekerjaan/Jabatan') ?></td>
			<td class="field"><span class="value"><?php echo $a->guardian_occupation ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('guardian_office_name', 'Nama dan Alamat Kantor') ?></td>
			<td class="field"><span class="value"><?php echo $a->guardian_office_name ?></span><br>
				<?php print_address($a, 'guardian_office', true, true, false, true, false, true, false) ?>
			</td>
		</tr>
	</table>
	<?php else: ?>
	<p><em>(Tidak ada wali)</em></p>
	<?php endif; ?>

	<table class="siblings-table subform">
		<caption>
			Anak ke-<span class="value"><?php echo $a->number_of_children_in_family ?></span> dari <span class="value"><?php echo $a->nth_child ?></span> bersaudara
		</caption>
		<thead>
			<tr>
				<th class="sibling-name">Nama Saudara Kandung</th>
				<th class="sibling-dob">Tanggal Lahir</th>
				<th class="sibling-job">Sekolah/Pekerjaan</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($siblings as $s):
			?>
			<tr class="notempty">
				<td class="sibling-name"><?php echo $s->full_name ?></td>
				<td class="sibling-dob"><?php echo $s->date_of_birth->format('j F Y') ?></td>
				<td class="sibling-job"><?php echo $s->occupation ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>

<section class="pane" id="pendidikan">
	<h1>Pendidikan</h1>
	<!-- <p>Seluruh kolom pada halaman ini <strong>wajib diisi</strong>.</p> -->
	<!-- poin 12–14 -->

	<h2>SMA/SMK/MA</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('high_school_name', 'Nama Sekolah', 'required') ?></td>
			<td class="field">
				<?php $form->text('high_school_name', 'long'); ?> <?php if ($a->in_pesantren): ?><span class="tag">Pesantren</span><?php endif; ?> <?php if ($a->in_acceleration_class): ?><span class="tag">Akselerasi</span><?php endif; ?>
			</td>
			<tr>
				<td class="label"><?php $form->label('high_school_address_street', 'Alamat Sekolah') ?></td>
				<td class="field"><?php $form->address('high_school', false, false, false, true, false, true, false); ?></td>
			</tr>
			<tr>
				<td class="label"><?php $form->label('high_school_admission_year', 'Tahun Masuk', 'required') ?></td>
				<td class="field"><?php $form->select_year('high_school_admission_year', date('Y') - 2, date('Y') - 1, false); ?></td>
			</tr>
			<tr>
				<td class="label"><?php $form->label('high_school_graduation_year', 'Tahun Keluar', 'required') ?></td>
				<td class="field"><?php $form->select_year('high_school_graduation_year', date('Y') + 1, date('Y') + 2); ?></td>
			</tr>

		</tr>
	</table>
	
	<table class="academics sma subform">
		<caption>
			<?php $form->label('grades_y10t1_average', 'Data prestasi (skala 0-100)', 'required') ?>
		</caption>
		<thead>
			<tr>
				<th rowspan="2" class="grade">Kelas</th>
				<th colspan="2" class="term term-initial">Semester I</th>
			</tr>
			<tr>
				<th class="term-initial average">Rata-Rata Nilai</th>
				<th class="term-initial subjects">Jumlah Mata Pelajaran</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="grade">X</td>
				<td class="term-initial average"><?php $form->text('grades_y10t1_average', 'very-short l') ?></td>
				<td class="term-initial subjects"><?php $form->text('grades_y10t1_subjects', 'very-short r') ?></td>
			</tr>
		</tbody>
	</table>
	
	<h2>SMP/MTs</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('junior_high_school_name', 'Nama Sekolah', 'required') ?></td>
			<td class="field">
				<?php $form->text('junior_high_school_name', 'long'); ?><br>
			</td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('junior_high_school_graduation_year', 'Tahun Ijazah', 'required') ?></td>
			<td class="field"><?php $form->select_year('junior_high_school_graduation_year', date('Y') - 2, date('Y') - 1); ?></td>
		</tr>
	</table>

	<table class="academics smp subform">
		<caption>
			<?php $form->label('grades_y7t1_average', 'Data prestasi (skala 0-100)', 'required') ?>
			
		</caption>
		<thead>
			<tr>
				<th rowspan="2" width="60" class="grade">Kelas</th>
				<th colspan="2" class="term term-initial">Semester I</th>
				<th colspan="2" class="term term-final">Semester II</th>
			</tr>
			<tr>
				<th class="term-initial average">Rata-Rata Nilai</th>
				<th class="term-initial subjects">Jumlah Mata Pelajaran</th>
				<th class="term-final average">Rata-Rata Nilai</th>
				<th class="term-final subjects">Jumlah Mata Pelajaran</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$grades = array(7 => 'VII', 8 => 'VIII', 9 => 'IX');
			foreach($grades as $i => $g): ?>
			<tr>
				<td class="grade"><?php echo $g; ?></td>
				<td class="term-initial average"><?php $form->text('grades_y' . $i . 't1_average', 'very-short l') ?></td>
				<td class="term-initial subjects"><?php $form->text('grades_y' . $i . 't1_subjects', 'very-short r') ?></td>
				<td class="term-final average"><?php $form->text('grades_y' . $i . 't2_average', 'very-short l') ?></td>
				<td class="term-final subjects"><?php $form->text('grades_y' . $i . 't2_subjects', 'very-short r') ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<h2>SD/MI</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('elementary_school_name', 'Nama Sekolah', 'required') ?></td>
			<td class="field">
				<?php $form->text('elementary_school_name', 'long'); ?><br>
			</td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('elementary_school_graduation_year', 'Tahun Ijazah', 'required') ?></td>
			<td class="field"><?php $form->select_year('elementary_school_graduation_year', date('Y') - 5, date('Y') - 3); ?></td>
		</tr>
	</table>

	<table class="academics sd subform">
		<caption>
			<?php $form->label('grades_y1t1_average', 'Data prestasi (skala 0-100)', 'required') ?>
			
		</caption>
		<thead>
			<tr>
				<th rowspan="2" width="60" class="grade">Kelas</th>
				<th colspan="2" class="term term-initial">Semester I</th>
				<th colspan="2" class="term term-final">Semester II</th>
			</tr>
			<tr>
				<th class="term-initial average">Rata-Rata Nilai</th>
				<th class="term-initial subjects">Jumlah Mata Pelajaran</th>
				<th class="term-final average">Rata-Rata Nilai</th>
				<th class="term-final subjects">Jumlah Mata Pelajaran</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$grades = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI');
			foreach($grades as $i => $g): ?>
			<tr>
				<td class="grade"><?php echo $g; ?></td>
				<td class="term-initial average"><?php $form->text('grades_y' . $i . 't1_average', 'very-short l') ?></td>
				<td class="term-initial subjects"><?php $form->text('grades_y' . $i . 't1_subjects', 'very-short r') ?></td>
				<td class="term-final average"><?php $form->text('grades_y' . $i . 't2_average', 'very-short l') ?></td>
				<td class="term-final subjects"><?php $form->text('grades_y' . $i . 't2_subjects', 'very-short r') ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h2>Pengetahuan Bahasa</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('years_speaking_english', 'Bahasa Inggris', 'required') ?></td>
			<td class="field"><?php $form->text('years_speaking_english', 'long') ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('other_languages', 'Bahasa lain') ?></td>
			<td class="field"><?php $form->text('other_languages', 'long') ?> &nbsp;&nbsp;&nbsp; <?php echo $a->years_speaking_other_languages ? '(' . $a->years_speaking_other_languages . ')' : ''; ?></td>
		</tr>
	</table>
	<h2>Pelajaran Favorit dan Cita-Cita</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('favorite_subject', 'Mata pelajaran favorit', 'required') ?></td>
			<td class="field"><?php $form->text('favorite_subject', 'long') ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('dream', 'Cita-cita', 'required') ?></td>
			<td class="field"><?php $form->text('dream', 'long') ?></td>
		</tr>
	</table>
</section>

<section class="pane" id="kegiatan">
	<h1>Kegiatan</h1>
	<!-- poin 15-19 -->
	<h2>Organisasi</h2>
	<table class="achievements subform">
		<caption>Organisasi yang pernah diikuti, baik di lingkungan sekolah maupun di luar lingkungan sekolah</caption>
		<thead>
			<tr>
				<th class="name">Nama Organisasi</th>
				<th class="kind">Jenis Kegiatan</th>
				<th class="achv">Jabatan</th>
				<th class="year">Tahun</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($subforms['applicant_organizations'] as $s):
			?>
			<tr class="notempty">
				<td class="name"><?php $s->text('name', 'short') ?></td>
				<td class="kind"><?php $s->text('kind', 'short') ?></td>
				<td class="achv"><?php $s->text('position', 'short') ?></td>
				<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<h2>Kesenian</h2>
	<table class="form-table">
		<tr>
			<td class="label">Sekedar hobi</td>
			<td class="field"><span class="value"><?php echo $a->arts_hobby ?></span></td>
		</tr>		
		<tr>
			<td class="label"><?php $form->label('arts_organized', 'Ikut perkumpulan') ?></td>
			<td class="field"><span class="value"><?php echo $a->arts_organized ?></span></td>
		</tr>
	</table>

	<table class="achievements subform" width="620">
		<caption>Prestasi</caption>
		<thead>
			<tr>
				<th class="name">Jenis</th>
				<th class="kind">Kejuaraan</th>
				<th class="achv">Prestasi</th>
				<th class="year">Tahun</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($subforms['applicant_arts_achievements'] as $s):
			?>
			<tr class="notempty">
				<td class="name"><?php $s->text('championship', 'short') ?></td>
				<td class="kind"><?php $s->text('kind', 'short') ?></td>
				<td class="achv"><?php $s->text('achievement', 'short') ?></td>
				<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h2>Olahraga</h2>
	<?php $phase = 'olahraga'; ?>
	<table class="form-table">
		<tr>
			<td class="label">Sekedar hobi</td>
			<td class="field"><span class="value"><?php echo $a->sports_hobby ?></span></td>
		</tr>		
		<tr>
			<td class="label"><?php $form->label('sports_organized', 'Ikut perkumpulan') ?></td>
			<td class="field"><span class="value"><?php echo $a->sports_organized ?></span></td>
		</tr>
	</table>
	<table class="achievements subform" width="620">
		<caption>Prestasi</caption>
		<thead>
			<tr>
				<th class="chmp">Kejuaraan</th>
				<th class="achv">Pencapaian</th>
				<th class="year">Tahun</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($subforms['applicant_sports_achievements'] as $s):
			?>
			<tr class="notempty">
				<td class="chmp"><?php $s->text('championship', 'short') ?></td>
				<td class="achv"><?php $s->text('achievement', 'short') ?></td>
				<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h2>Lain-lain</h2>
	<?php $phase = 'kegiatan_lain_lain'; ?>
	<table class="achievements subform">
		<caption>Kegiatan lain di luar olahraga dan kesenian</caption>
		<thead>
			<tr>
				<th class="chmp">Kegiatan</th>
				<th class="achv">Prestasi</th>
				<th class="year">Tahun</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($subforms['applicant_other_achievements'] as $s):
			?>
			<tr class="notempty">
				<td class="chmp"><?php $s->text('activity', 'short') ?></td>
				<td class="achv"><?php $s->text('achievement', 'short') ?></td>
				<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php $phase = 'pengalaman_kerja'; ?>
	<table class="achievements subform">
		<caption>Pengalaman kerja sosial/magang/bekerja</caption>
		<thead>
			<tr>
				<th class="ngo">Nama dan bidang tempat bekerja/magang</th>
				<th class="ngo">Tugas dan tanggung jawab yang dijalankan</th>
				<th class="period">Tahun dan lama&nbsp;bekerja</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($subforms['applicant_work_experiences'] as $s):
			?>
			<tr class="notempty">
				<td class="ngo"><?php $s->text('organization', 'short') ?></td>
				<td class="ngo"><?php $s->text('position', 'short') ?></td>
				<td class="period"><?php $form->text('period') ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>

<section class="pane" id="travel">
	<h1>Riwayat Perjalanan</h1>
	
	<h2>Perjalanan luar negeri &ndash; jangka pendek</h2>
	<?php if ($a->short_term_travel_has): ?>
	<table class="form-table">
		<tr>
			<td class="label"><em>Destination</em></td>
			<td class="field"><span class="value"><?php echo $a->short_term_travel_destination ?></span></td>
		</tr>
		<tr>
			<td class="label"><em>Date</em></td>
			<td class="field"><span class="value"><?php echo $a->short_term_travel_when ?></span></td>
		</tr>
		<tr>
			<td class="label"><em>Purpose</em></td>
			<td class="field"><span class="value"><?php echo $a->short_term_travel_purpose ?></span></td>
		</tr>
	</table>
	<?php else: ?>
	<p><em>Belum pernah</em></p>
	<?php endif; ?>
	
	<h2>Perjalanan luar negeri &ndash; jangka panjang</h2>
	<?php if ($a->long_term_travel_has): ?>
	<table class="form-table">
		<tr>
			<td class="label"><em>Destination</em></td>
			<td class="field"><span class="value"><?php echo $a->long_term_travel_destination ?></span></td>
		</tr>
		<tr>
			<td class="label"><em>Date</em></td>
			<td class="field"><span class="value"><?php echo $a->long_term_travel_when ?></span></td>
		</tr>
		<tr>
			<td class="label"><em>Purpose</em></td>
			<td class="field"><span class="value"><?php echo $a->long_term_travel_purpose ?></span></td>
		</tr>
		<tr>
			<td class="label"><em>Activities</em></td>
			<td class="field"><span class="value"><?php echo $a->long_term_travel_activities ?></span></td>
		</tr>
	</table>
	<?php else: ?>
	<p><em>Belum pernah</em></p>
	<?php endif; ?>
</section>

<section class="pane" id="reference">
	<h1>Referensi</h1>
	<h2>Saudara yang pernah mengikuti program pertukaran Bina Antarbudaya/AFS</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('relative_returnee_name', 'Nama') ?></td>
			<td class="field"><span class="value"><?php echo $a->relative_returnee_name ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('relative_returnee_relationship', 'Hubungan dengan peserta') ?></td>
			<td class="field"><span class="value"><?php echo $a->relative_returnee_relationship ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('relative_returnee_program', 'Program') ?></td>
			<td class="field"><span class="value"><?php echo $a->relative_returnee_program . ($a->relative_returnee_program_type ? ' (' . ucfirst($a->relative_returnee_program_type) . ')' : ''); ?></span></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('relative_returnee_destination', 'Tujuan (sending)/Asal (hosting)') ?></td>
			<td class="field"><?php $form->text('relative_returnee_destination', 'long')  ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('relative_returnee_address_street', 'Alamat sekarang') ?></td>
			<td class="field"><?php print_address($a, 'relative_returnee', true, false, false, false, false, false, true) ?></td>
		</tr>
	</table>
	<h2>Partisipasi keluarga dalam kegiatan Bina Antarbudaya/AFS sebelumnya</h2>
	<table class="form-table">
		<tr>
			<td class="label"><?php $form->label('past_binabud_activities', 'Kegiatan') ?></td>
			<td class="field"><?php $form->text('past_binabud_activities', 'long')  ?></td>
		</tr>
		<tr>
			<td class="label"><?php $form->label('past_binabud_activities_year', 'Tahun') ?></td>
			<td class="field"><?php echo $a->past_binabud_activities_year; ?></td>
		</tr>
	</table>

	<h2>Referensi</h2>

	<p class="label">
		<?php $form->label('referrer', 'Dari mana Adik mengetahui program kami?') ?>
	</p>
		<?php $form->textarea('referrer');  ?>
	<p class="label">
		Apa motivasi Adik mengikuti seleksi dan program Bina Antarbudaya?
	</p>
		<?php $form->textarea('motivation', 'extra-large');  ?>
	<p class="label">
		Apa yang diharapkan Adik dengan keikutsertaan Adik dalam seleksi dan program Bina Antarbudaya?
	</p>
		<?php $form->textarea('hopes', 'extra-large');  ?>
</section>

<section class="pane pagebreak" id="rekomendasi">
	<h1>Rekomendasi</h1>
	<h2>Lingkungan sekolah</h2>
	<table class="form-table">
		<tr>
			<td class="label">Nama</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_school_name ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat/Telepon</td>
			<td class="field"><?php $form->textarea('recommendations_school_address') ?></td>
		</tr>
		<tr>
			<td class="label">Pekerjaan</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_school_occupation ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat pekerjaan</td>
			<td class="field"><?php $form->textarea('recommendations_school_work_address') ?></td>
		</tr>
		<tr>
			<td class="label">Hubungan</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_school_relationship ?></span></td>
		</tr>
	</table>
	<h2>Lingkungan rumah/organisasi di luar sekolah</h2>
	<table class="form-table">
		<tr>
			<td class="label">Nama</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_nonschool_name ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat/Telepon</td>
			<td class="field"><?php $form->textarea('recommendations_nonschool_address') ?></td>
		</tr>
		<tr>
			<td class="label">Pekerjaan</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_nonschool_occupation ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat pekerjaan</td>
			<td class="field"><?php $form->textarea('recommendations_nonschool_work_address') ?></td>
		</tr>
		<tr>
			<td class="label">Hubungan</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_nonschool_relationship ?></span><br>
				</td>
		</tr>
	</table>
	<h2>Teman dekat</h2>
	<table class="form-table">
		<tr>
			<td class="label">Nama</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_close_friend_name ?></span></td>
		</tr>
		<tr>
			<td class="label">Alamat/Telepon</td>
			<td class="field"><?php $form->textarea('recommendations_close_friend_address') ?></td>
		</tr>
		<tr>
			<td class="label">Hubungan</td>
			<td class="field"><span class="value"><?php echo $a->recommendations_close_friend_relationship ?></span></td>
		</tr>
	</table>
</section>

<section class="pane" id="persona">
	<h1>Kepribadian</h1>
	<p class="label">
		Menurut Adik, seperti apakah sifat dan kepribadian adik?
	</p>
		<?php $form->textarea('personality', 'extra-large') ?>
	<p class="label">
		Apakah kelebihan/kekurangan Adik?
	</p>
		<?php $form->textarea('strengths_and_weaknesses', 'extra-large') ?>
	<p class="label">
		Hal-hal apakah yang sering membuat Adik merasa tertekan?
	</p>
		<?php $form->textarea('stressful_conditions', 'extra-large') ?>
	<p class="label">
		Masalah terberat apakah yang pernah Adik hadapi? Bagaimana Adik menyelesaikannya?
	</p>
		<?php $form->textarea('biggest_life_problem', 'extra-large') ?>
	<p class="label">
		Apakah rencana Adik berkaitan dengan pendidikan dan karir di masa depan?
	</p>
	<?php $form->textarea('plans', 'extra-large') ?>
</section>

<section class="footer">
	<p class="statement">Saya yang bertanda tangan di bawah ini menyatakan bahwa informasi yang terdapat pada dokumen ini adalah adalah benar dan apa adanya, serta dibuat tanpa paksaan dari pihak&nbsp;manapun.</p>
	<p class="date"><?php $now = new HeliumDateTime('now'); echo $now->format('j F Y'); ?></p>
	<p class="signature"><?php echo $applicant->sanitized_full_name; ?></p>
</section>

<!-- end form -->
</body>
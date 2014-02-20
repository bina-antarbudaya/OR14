<?php
	
/*
TODO
* Admin mode
* JS cleanup
*/

$dob_upper_limit = new HeliumDateTime;
$dob_upper_limit->setDate($program_year - 17, 8, 1);
$dob_lower_limit = new HeliumDateTime;
$dob_lower_limit->setDate($program_year - 19, 8, 1);
$dob_lower_limit_yes = new HeliumDateTime;
$dob_lower_limit_yes->setDate($program_year - 18, 1, 1);

?>

<?php $this->print_header('Formulir Pendaftaran'); ?>

<?php if ($admin): ?>
<header class="page-header">
	<h1>Pengelolaan Peserta</h1>
</header>
<?php else: ?>
<header class="page-header">
	<h2>Tahap 3/4</h2>
	<h1>Formulir Pendaftaran</h1>
</header>
<?php endif; ?>
<!-- <div class="container"> -->

<form action="<?php L($this->params) ?>" enctype="multipart/form-data" method="POST" novalidate>
	<div id="appform">
		<?php if ($new && !$admin): ?>
		<div class="alert alert-block alert-success">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<h4>Selamat datang</h4>
			<p>
				Formulir sepanjang sepuluh halaman ini berisi informasi tentang Adik yang dibutuhkan 
				untuk mengikuti rangkaian seleksi pertukaran pelajar Bina Antarbudaya.
				Gunakan menu di sebelah kiri untuk berpindah halaman.
			</p>
			<p>
				Formulir ini dapat diisi secara bertahap. Gunakan tombol <b>Simpan&nbsp;Sementara</b> di sebelah kanan untuk menyimpan isian formulir Adik agar dapat dilanjutkan.
				Setelah seluruh formulir selesai, lakukan <b>Finalisasi</b> melalui menu di sebelah kiri.
			</p>
			<p>
				Kolom yang bertanda <span class="required-sample">*</span> wajib diisi.
			</p>
		</div>

		<?php elseif ($admin && $error): ?>
		<div class="alert alert-error alert-block">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<strong>Pengubahan peserta gagal.</strong>
			<?php $messages = array('not_found' => 'Peserta tidak ditemukan.', 'applicant_finalized' => 'Peserta sudah melakukan finalisasi.', 'forbidden' => 'Anda tidak diizinkan mengakses laman ini.'); echo $messages[$error]; ?>
		</div>

		<?php elseif ($upload_error): ?>
		<div class="alert alert-error">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<strong>Pengunggahan foto gagal.</strong>
			<?php
				switch ($upload_error) {
					case 'invalid_format':
						echo 'Format foto salah &ndash; gunakan foto berbentuk JPG, PNG, atau GIF.';
						break;
					default:
						echo 'Cobalah sekali lagi.';
				}
			?>
		</div>

		<?php elseif ($errors): ?>
		<div class="alert alert-error">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<h4>Finalisasi Gagal</h4>
			<?php
			$errors = array_map(function($e) {
				switch ($e) {
					case 'incomplete':
						return 'Formulir belum lengkap. Pastikan seluruh bagian formulir ini telah terisi.';
					case 'picture':
						return 'Adik belum mengunggah (upload) foto.';
					case 'birth_date':
						$lower = $dob_lower_limit->format('j F Y');
						$upper = $dob_upper_limit->format('j F Y');
						return "Tanggal lahir Adik harus di antara <strong>$lower</strong> dan <strong>$upper</strong>";
					case 'birth_date_yes':
						$lower_yes = $dob_lower_limit_yes->format('j F Y');
						$upper = $dob_upper_limit->format('j F Y');
						return "Untuk mendaftar program YES, tanggal lahir Adik harus di antara <strong>$lower_yes</strong> dan <strong>$upper</strong>";
					default:
						return $e;
				}
			}, $errors);
			
			foreach ($errors as $error): ?>
			<p><?php echo $error; ?></p>
			<?php endforeach; ?>
			<script>console.log('Skynet: Finalization failed because the following fields were not filled in:', <?php echo json_encode ($incomplete) ?>)</script>
		</div>

		<?php elseif ($crop_success): ?>
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<strong>Foto Adik berhasil disimpan.</strong> Silakan lanjut mengisi formulir.
		</div>

		<?php elseif ($success): ?>
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<strong>Data Adik berhasil disimpan.</strong> Silakan lanjut mengisi formulir.
		</div>

		<?php elseif ($message = $notice): ?>
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			<?php echo $message; ?>
		</div>
		<?php endif; ?>

		<div class="form-tools">
			<div class="form-tools-container">
				<div class="row">
					<div class="span9">
						<p>Batas waktu pendaftaran: <strong><?php echo $expires_on->format('l, j F Y') ?></strong></p>
					</div>
					<div class="span3">
						<input type="hidden" name="applicant_id" value="<?php echo $applicant->id ?>">
						<button type="submit" class="btn btn-block btn-success" id="save-button"><i class="icon-ok icon-white"></i> Simpan<?php if (!$admin): ?> Sementara<?php endif; ?></button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<nav class="form-nav span2">
				<header>
					<h1>Pilih Halaman</h1>
				</header>
				<ol>
					<li><a href="#pribadi">Data Pribadi</a></li>
					<li><a href="#foto">Pas Foto</a></li>
					<li><a href="#keluarga">Keluarga</a></li>
					<li><a href="#pendidikan">Riwayat Pendidikan</a></li>
					<li><a href="#kegiatan">Riwayat Kegiatan</a></li>
					<li><a href="#selfawareness">Kepribadian</a></li>
					<li><a href="#program">Pilihan Program</a></li>
					<li><a href="#countryprefs">Pilihan Negara</a></li>
					<li><a href="#perjalanan">Riwayat Perjalanan</a></li>
					<li><a href="#referensi">Referensi</a></li>
					<li><a href="#rekomendasi">Rekomendasi</a></li>
					<li><a href="#volunteering">Volunteering &amp; Hosting</a></li>
					<?php if (!$readonly && !$admin): ?>
					<li class="finalize"><a href="#finalisasi">Finalisasi</a></li>
					<?php endif; ?>
				</ol>
			</nav>

			<div class="span10">
				<div class="form-fields">

					<!-- <ul class="pager above">
						<li class="previous">
							<a href="#_prev">&larr; Halaman Sebelumnya</a>
						</li>
						<li class="next">
							<a href="#_next">Halaman Selanjutnya &rarr;</a>
						</li>
					</ul> -->

					<!-- begin form -->

					<fieldset class="pane" id="pribadi">
						<legend>Data Pribadi</legend>
						
						<table class="form-table">
							<?php /* <tr>
								<td class="label"><?php $form->label('full_name', 'Nama Lengkap', 'required') ?></td>
								<td class="field"><?php $form->text('full_name', 'long'); ?> <span class="help-block">Isi sesuai dengan Akte Kelahiran.</span></td>
							</tr> */ ?>
							<tr>
								<td class="label"><?php $form->label('first_name', 'Nama Depan', 'required') ?></td>
								<td class="field"><?php $form->text('first_name', 'input-block-level'); ?>
								<span class="help-block">Isilah sesuai akta kelahiran dan tanpa menggunakan singkatan.</span></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('middle_name', 'Nama Tengah') ?></td>
								<td class="field"><?php $form->text('middle_name', 'input-block-level'); ?>
								<span class="help-block">Isilah sesuai akta kelahiran dan tanpa menggunakan singkatan.</span></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('last_name', 'Nama Belakang') ?></td>
								<td class="field"><?php $form->text('last_name', 'input-block-level'); ?>
								<span class="help-block">Isilah sesuai akta kelahiran dan tanpa menggunakan singkatan.</span></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('place_of_birth', 'Tempat Kelahiran', 'required') ?></td>
								<td class="field"><?php $form->text('place_of_birth', 'input-block-level') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('date_of_birth', 'Tanggal Lahir', 'required') ?></td>
								<td class="field">
									<span class="applicant-dob"><?php $form->date('date_of_birth', 17, 15); ?></span>
									<br>
									<span class="help-block">
										Untuk mengikuti program AFS Year Program dan Green Academy Short Programme periode <?php echo $program_year - 1 ?>&ndash;<?php echo $program_year ?>,
										Adik harus lahir antara tanggal <?php echo str_replace(' ', '&nbsp;', $dob_lower_limit->format('j F Y')) ?> dan <?php echo str_replace(' ', '&nbsp;', $dob_upper_limit->format('j F Y')) ?>.
									</span>
									<span class="help-block">
										Untuk mengikuti program Kennedy-Lugar YES periode <?php echo $program_year - 1 ?>&ndash;<?php echo $program_year ?>,
										Adik harus lahir antara tanggal <?php echo str_replace(' ', '&nbsp;', $dob_lower_limit_yes->format('j F Y')) ?> dan <?php echo str_replace(' ', '&nbsp;', $dob_upper_limit->format('j F Y')) ?>.
									</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('applicant_email', 'Alamat E-mail', 'required') ?></td>
								<td class="field"><?php $form->email('applicant_email', 'long'); ?> <span class="help-block">Seluruh pengumuman mengenai seleksi akan dikirim ke alamat ini.</span></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('applicant_address_street', 'Alamat Lengkap', 'required') ?></td>
								<td class="field"><?php $form->address('applicant', true, true, true, true, true, true, false); ?> <span class="help-block">Isilah dengan lengkap agar tidak terjadi salah pengiriman surat.</span></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('sex', 'Jenis Kelamin', 'required') ?></td>
								<td class="field">
									<?php $form->select('sex', array('' => '', 'F' => 'Perempuan', 'M' => 'Laki-laki'), 'medium-short') ?>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('body_height', 'Tinggi Badan', 'required') ?></td>
								<td class="field">
									<?php $form->number('body_height', 'short') ?>
									cm
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('body_weight', 'Berat Badan', 'required') ?></td>
								<td class="field">
									<?php $form->number('body_weight', 'short') ?>
									kg
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('blood_type', 'Golongan Darah', 'required') ?></td>
								<td class="field">
									<?php $form->select('blood_type', array('' => '',
										'O+' => 'O+', 'A+' => 'A+', 'B+' => 'B+', 'AB+' => 'AB+',
										'O-' => 'O-', 'A-' => 'A-', 'B-' => 'B-', 'AB-' => 'AB-'), 'short')?>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('citizenship', 'Kewarganegaraan', 'required') ?></td>
								<td class="field">
									<?php $form->text('citizenship', 'long') ?>
									<br>
									<span class="help-block">Contoh: Indonesia</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('religion', 'Agama', 'required') ?></td>
								<td class="field">
									<?php $form->text('religion', 'long') ?>
								</td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="pane" id="program">
						<legend>Pilihan Program</legend>
						<table class="programs-table">
							<tr class="program-name">
								<th class="label">Nama Program</th>
								<td class="afs"><?php $form->checkbox('program_afs') ?> <?php $form->label('program_afs', 'AFS Year Program') ?></td>
								<td class="yes"><?php $form->checkbox('program_yes') ?> <?php $form->label('program_yes', 'Kennedy-Lugar YES') ?></td>
								<td class="green-academy"><?php $form->checkbox('program_green_academy') ?> <?php $form->label('program_green_academy', 'Green Academy Short Programme') ?></td>
							</tr>
							<tr class="program-age-limit">
								<th class="label">Batas Umur</th>
								<td class="afs">
									Siswa kelahiran antara <?php echo str_replace(' ', '&nbsp;', $dob_lower_limit->format('j F Y')) ?>&nbsp;dan&nbsp;<?php echo str_replace(' ', '&nbsp;', $dob_upper_limit->format('j F Y')) ?>
								</td>
								<td class="yes">
									Siswa kelahiran antara <?php echo str_replace(' ', '&nbsp;', $dob_lower_limit_yes->format('j F Y')) ?>&nbsp;dan&nbsp;<?php echo str_replace(' ', '&nbsp;', $dob_upper_limit->format('j F Y')) ?>
								</td>
								<td class="green-academy">
									Siswa kelahiran antara <?php echo str_replace(' ', '&nbsp;', $dob_lower_limit->format('j F Y')) ?>&nbsp;dan&nbsp;<?php echo str_replace(' ', '&nbsp;', $dob_upper_limit->format('j F Y')) ?>
								</td>
							</tr>
							<tr class="program-length">
								<th class="label">Lama Program</th>
								<td class="afs">
									<span class="length">11 bulan</span>
									<br>
									Agustus <?php echo $program_year - 1?> &ndash; Juni <?php echo $program_year ?> (kecuali Jepang)
									<br>
									Maret <?php echo $program_year - 1?> &ndash; Februari <?php echo $program_year ?> (Jepang)
								</td>
								<td class="yes">
									<span class="length">11 bulan</span>
									<br>
									Agustus <?php echo $program_year - 1?> &ndash; Juni <?php echo $program_year ?>
								</td>
								<td class="green-academy">
									<span class="length">1 bulan</span>
									<br>
									Desember <?php echo $program_year - 1?> &ndash; Januari <?php echo $program_year ?>
								</td>
							</tr>
							<tr class="program-destination">
								<th class="label">Negara Tujuan</th>
								<td class="afs">
									<ul>
										<?php
										$pp = array(
											'ARG' => 'Argentina',
											'BRA' => 'Brazil',
											'CAN' => 'Kanada',
											'MEX' => 'Meksiko',
											'USA' => 'Amerika Serikat',
											'NED' => 'Belanda',
											'BFL' => 'Belgia',
											'CZE' => 'Republik Ceko',
											'FIN' => 'Finlandia',
											'FRA' => 'Perancis',
											'GER' => 'Jerman',
											'ISL' => 'Islandia',
											'ITA' => 'Italia',
											'LAT' => 'Latvia',
											'NOR' => 'Norwegia',
											'SUI' => 'Swiss',
											'SWE' => 'Swedia',
											'TUR' => 'Turki',
											'CHN' => 'Cina',
											'JPN' => 'Jepang',
											'PHI' => 'Filipina',
											'RUS' => 'Rusia',
											'THA' => 'Thailand',
										);

										asort($pp);

										foreach ($pp as $p):
										?>

											<li><?php echo $p ?></li>

										<?php endforeach; ?>
									</ul>
								</td>
								<td class="yes">
									<ul>
										<li>Amerika Serikat</li>
									</ul>
								</td>
								<td class="green-academy">
									<ul>
										<li>Jerman</li>
									</ul>
								</td>
							</tr>
							<tr class="program-info">
								<th class="label"></th>
								<td class="afs">
									<p>
									    Kalau kamu ingin mendapatkan pengalaman yang akan mengubah hidupmu, maka program AFS adalah pilihan yang tepat. Kmu akan mendapat teman baru, belajar
									    bahasa asing, budaya baru, dan mengalami kehidupan dengan cara yang baru. Di akhir program nanti kalu juga akan memperoleh keterampilan baru yang tidak
									    semua orang bisa dapatkan, yaitu: Kompetensi Antarbudaya (<em>intercultural Competence</em>). Keterampilan ini akan membuatmu memiliki kemampuan
									    beradaptasi di kebudayaan manapun. Selain itu kamu akan memperoleh banyak kemampuan lain, di antaranya kamu akan lebih mandiri, bertanggung jawab, dan
									    lebih fleksibel. Semua itu akan sangat berguna di semua jenjang kehidupanmu nanti.
									</p>
									<p>
									    Sebagai siswa AFS, kamu akan tinggal dengan keluarga angkat yang akan menerima kamu selayaknya bagian dari keluarga mereka sendiri. Seperti sebuah
									    keluarga, keluarga angkatmu akan selalu memberikan dukungan yang kamu perlukan selama masa program, memastikan bahwa kamu sehat dan selamat, serat akan
									    menjadi sumber terbaikmu untuk mempelajari budaya setempat.
									</p>
									<p>
									    Kamu juga akan berekolah di SMA setempat, dan merasakan lingkungan belajar yang berbeda dengan sekolah di Indonesia. Kamu akan mendapat teman baru dan
									    mengalami secara langsung metode belajar di luar negeri. Selama program kamu dapat melibatkan diri dalam berbagai kegiatan baik di lingkungan AFS maupun di
									    masyarakat sekitar. Berbagai eksplorasi dapat dialkukan untuk memperkaya pengalaman dan pengembangan dirimu selama program.
									</p>
								</td>
								<td class="yes">
									<p>
									    Kennedy-Lugar Youth Exchange and Study Program adalah program beasiswa penuh yang diberikan oleh U.S. Department of State kepada siswa SMA atau sederajat,
									    yang bertujuan menjembatani pemahaman dan saling pengertian antara masyarakat negara-negara dengan populasi muslin yang signifikan dengan masyarakat
									    Amerika Serikat.
									</p>
									<p>
									    Program KL-YES yang telah dilaksanakan sejak tahun 2003 juga memberikan kesempatan kepada siswa difabel (tuna netra, tuna rungu, tuna wicara, tuna daksa)
									    untuk mengikuti program ini. Selama program, kamu akan tinggal dengan keluarga Amerika, dan bersekolah di SMA setempat. Kamu akan mengalami dan belajar
									    secara langsung mengenai kehidupan di Amerika Serikat. Kamu juga kan memperoleh kesempatan untuk berinteraksi secara langsung dengan masyarakat melalui
									    kegiatan-kegiatan yang dilaksanakan selama program.
									</p>
									<p>
									    Dalam program KL-YES ini kamu akan menjadi duta perdamaian dan persahabatan antara Indonesia sebagai Negara dengan jumlah penduduk muslim terbesar di dunia
									    dan Amerika Serikat.
									</p>
								</td>
								<td class="green-academy"></td>
							</tr>
						</table>	
					</fieldset>

					<fieldset class="pane" id="countryprefs">
						<legend>Pilihan Negara</legend>
						<p>Pilih urutan negara tujuan yang Adik kehendaki <em>jika</em> Adik diterima di AFS Year Program.</p>
						<p>Adik wajib menentukan <strong>seluruh</strong> urutan pilihan negara.</p>
						<div class="country-prefs-container">

							<div class="row-fluid">
						<?php

						// $partners is loaded from controller
							
						$continents = array(
							'americas' => 'Benua Amerika',
							'europe' => 'Benua Eropa',
							'asia' => 'Benua Asia'
						);
							
						foreach ($partners as $continent => $countries):
							$select = array_merge(array('' => '(Pilih Negara)'), $countries);
							$basename = 'pref_' . $continent . '_';
							ksort($select);
										?>

									<div class="span4 country-pref-options" data-continent="<?php echo $continent ?>">
										<h4><?php echo $continents[$continent] ?></h4>
										<ol>
											<?php for ($i = 1; $i <= count($countries); $i++) {
												echo '<li>';
												$form->select($basename . $i, $select, 'input-block-level country-pref');
												echo "</li>\n";
											} ?>
										</ol>
									</div>
											
						<?php endforeach;?>
							</div>
						</div>
						<table class="form-table country-preference">
							<tr>
								<td class="label"><?php $form->label('country_preference_other', 'Pilihan Negara Lainnya') ?></td>
								<td class="field">
								<?php $form->text('country_preference_other', 'input-block-level') ?>
								<br>
								<span class="help-block">Isilah dengan negara lain yang ingin Adik kunjungi untuk pertukaran pelajar di luar pilihan negara di atas, bila ada.</span>
								</td>
						</table>
					</fieldset>

					<fieldset class="pane" id="keluarga">
						<!-- poin 9–11 -->
						<legend>Keluarga</legend>
						<?php

						foreach(array('father' => 'Ayah', 'mother' => 'Ibu') as $n => $parent):
						?>
						<h4><?php echo $parent; ?></h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label($n . '_full_name', "Nama Lengkap $parent", 'required') ?></td>
								<td class="field">
									<?php $form->text($n . '_full_name', 'long'); ?>
									<br>
									<span class="help-block">Isilah dengan nama lengkap. Apabila telah wafat, cantumkan (Alm).</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_office_email', 'Alamat E-mail ' . $parent) ?></td>
								<td class="field"><?php $form->text($n . '_office_email', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_office_mobilephone', 'Nomor Ponsel ' . $parent) ?></td>
								<td class="field"><?php $form->tel($n . '_office_mobilephone', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_education', 'Pendidikan Terakhir ' . $parent) ?></td>
								<td class="field"><?php $form->text($n . '_education', 'long'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_occupation', 'Pekerjaan/Jabatan ' . $parent) ?></td>
								<td class="field">
									<?php $form->text($n . '_occupation', 'long'); ?>
									<br>
									<span class="help-block long">Isilah dengan rinci &ndash; bila wiraswasta, cantumkan bidangnya; bila swasta, cantumkan nama perusahaannya.</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_job_title', 'Pangkat/Golongan ' . $parent) ?></td>
								<td class="field">
									<?php $form->text($n . '_job_title', 'long'); ?>
									<br>
									<span class="help-block long">Isilah dengan rinci &ndash; bila TNI, cantumkan pangkatnya; bila PNS, cantumkan golongannya; bila swasta, cantumkan jabatannya.</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_office_name', 'Instansi/Perusahaan ' . $parent) ?></td>
								<td class="field"><?php $form->text($n . '_office_name', 'long'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label($n . '_office_address_street', 'Alamat Kantor ' . $parent) ?></td>
								<td class="field">
									<?php $form->address($n . '_office', true, true, false, true, false, true, false) ?>
								</td>
							</tr>
						</table>
						<?php endforeach; ?>

						<h4>Wali <span>(apabila orang tua telah wafat atau Adik tinggal terpisah dengan orang tua)</span></h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('guardian_full_name', "Nama Lengkap Wali") ?></td>
								<td class="field">
									<?php $form->text('guardian_full_name', 'long'); ?>
									<br>
									<span class="help-block">Isilah dengan nama lengkap.</span>
								</td>
							</tr>
							
							<tr>
								<td class="label"><?php $form->label('guardian_relationship_to_applicant', 'Hubungan dengan Adik') ?></td>
								<td class="field"><?php $form->text('guardian_relationship_to_applicant', 'long'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('guardian_email', 'Alamat E-mail Wali') ?></td>
								<td class="field"><?php $form->text('guardian_email', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('guardian_address_street', 'Alamat Wali') ?></td>
								<td class="field"><?php $form->address('guardian', true, true, false, true, true, false, false) ?></td>
							</tr>
							<?php /* The field below is in the DB schema but not the original form */ ?>
							<tr>
								<td class="label"><?php $form->label('guardian_education', 'Pendidikan Terakhir') ?></td>
								<td class="field"><?php $form->text('guardian_education', 'long'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('guardian_occupation', 'Pekerjaan/Jabatan Wali') ?></td>
								<td class="field">
									<?php $form->text('guardian_occupation', 'long'); ?>
									<br>
									<span class="help-block long">Isilah dengan rinci &ndash; bila wiraswasta, cantumkan bidangnya; bila swasta, cantumkan jabatan dan nama perusahaannya.</span>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('guardian_job_title', 'Pangkat/Golongan Wali') ?></td>
								<td class="field"><?php $form->text('guardian_job_title', 'long'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('guardian_office_name', 'Nama dan Alamat Kantor') ?></td>
								<td class="field">
									<?php $form->text('guardian_office_name', 'long'); ?>
									<br>
									<?php $form->address('guardian_office', true, true, false, true, false, true, false) ?>
								</td>
							</tr>
						</table>

						<h4>Saudara Kandung</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('number_of_children_in_family', 'Jumlah Anak dalam Keluarga', 'required') ?></td>
								<td class="field"><?php $form->number('number_of_children_in_family', 'short', 'required'); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('nth_child', 'Adik adalah anak ke-...') ?></td>
								<td class="field"><?php $form->number('nth_child', 'short'); ?></td>
							</tr>
						</table>
						<table class="siblings-table subform">
							<caption>
								<span>Nama, umur, dan sekolah/pekerjaan saudara kandung: (selain Adik sendiri)</span>
							</caption>
							<thead>
								<tr>
									<th class="sibling-name">Nama Lengkap</th>
									<th class="sibling-dob">Tanggal Lahir</th>
									<th class="sibling-job">Sekolah/Pekerjaan</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($sibling_forms as $s):
								?>
								<tr class="notempty">
									<td class="sibling-name"><?php $s->text('full_name', 'short') ?></td>
									<td class="sibling-dob"><?php $s->date('date_of_birth', 50) ?></td>
									<td class="sibling-job"><?php $s->text('occupation', 'short') ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for ($i = 0; $i < ($applicant->number_of_children_in_family - count($sibling_forms) - 1); $i++): $s = new FormDisplay; $s->make_subform('siblings[' . ($i + 1024) . ']') ?>
								<tr class="phpengineered">
									<td class="sibling-name"><?php $s->text('full_name', 'short') ?></td>
									<td class="sibling-dob"><?php $s->date('date_of_birth', 50) ?></td>
									<td class="sibling-job"><?php $s->text('occupation', 'short') ?></td>
								</tr>
								<?php endfor; $s = new FormDisplay; $s->make_subform('siblings[#]'); ?>
								<tr class="prototype">
									<td class="sibling-name"><?php $s->text('full_name', 'short') ?></td>
									<td class="sibling-dob"><?php $s->date('date_of_birth', 50) ?></td>
									<td class="sibling-job"><?php $s->text('occupation', 'short') ?></td>
								</tr>
							</tbody>
						</table>
					</fieldset>

					<fieldset class="pane" id="pendidikan">
						<legend>Riwayat Pendidikan</legend>
						<h4>SMA, SMK, MA, atau sederajat</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('high_school_name', 'Nama Sekolah', 'required') ?></td>
								<td class="field">
									<?php $form->text('high_school_name', 'long'); ?><br>
									<span class="help-block">Cantumkan kota. Misal: SMA <u>Negeri</u> 70 <u>Bandung</u></span>
									<span class="help-block">Jika Adik pernah berpindah sekolah (mutasi), tuliskan secara berurutan nama masing-masing sekolah yang pernah Adik masuki dengan memisahkannya dengan garis miring (/).</span>
									<datalist id="high-schools" data-for="high_school_name">
										<?php foreach ($schools as $school): ?>
										<option value="<?php echo $school ?>">

										<?php endforeach; ?>
									</datalist>
									<script>
									var high_schools = <?php echo json_encode($schools) ?>
									</script>
								</td>
							</tr>
							<tr>
								<td class="label"></td>
								<td class="field">
									<label class="checkbox"><?php $form->checkbox('in_pesantren') ?> Sekolah saya adalah Pesantren/Madrasah</label>
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('high_school_address_street', 'Alamat Sekolah') ?></td>
								<td class="field"><?php $form->address('high_school', false, false, false, true, false, true, false); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('high_school_admission_year', 'Tahun Masuk', 'required') ?></td>
								<td class="field"><?php $form->select_year('high_school_admission_year', $program_year - 5, $program_year - 3, false); ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('high_school_graduation_year', 'Tahun Keluar', 'required') ?></td>
								<td class="field"><?php $form->select_year('high_school_graduation_year', $program_year - 1 /* Acceleration */, $program_year /* Regular */); ?></td>
							</tr>
							<tr>
								<td class="label"></td>
								<td class="field">
									<label class="checkbox"><?php $form->checkbox('in_acceleration_class') ?> Saya adalah siswa kelas Akselerasi</label>
									<span class="help-block">Program YES tidak tersedia bagi siswa kelas akselerasi.</span>
								</td>
							</tr>
						</table>
						
						<table class="academics sma subform">
							<caption>
								<?php $form->label('grades_y10t1_average', 'Data prestasi', 'required') ?>
								<span class="help-block">Gunakan skala 0&ndash;100 untuk rata-rata nilai, <em>atau</em> indeks abjad sesuai rapor asli bila tidak ada nilai numerik.</span>
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
						
						<h4>SMP, MTs, atau sederajat</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('junior_high_school_name', 'Nama Sekolah', 'required') ?></td>
								<td class="field">
									<?php $form->text('junior_high_school_name', 'long'); ?><br>
									<span class="help-block">Cantumkan kota. Misal: SMP <u>Negeri</u> 70 <u>Bandung</u></span>
									<span class="help-block">Jika Adik pernah berpindah sekolah (mutasi), tuliskan secara berurutan nama masing-masing sekolah yang pernah Adik masuki dengan memisahkannya dengan garis miring (/).</span>	
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('junior_high_school_graduation_year', 'Tahun Ijazah', 'required') ?></td>
								<td class="field"><?php $form->select_year('junior_high_school_graduation_year', $program_year - 4, $program_year - 3); ?></td>
							</tr>
						</table>

						<table class="academics smp subform">
							<caption>
								<?php $form->label('grades_y7t1_average', 'Data prestasi', 'required') ?>
								<span class="help-block">Gunakan skala 0&ndash;100 untuk rata-rata nilai, <em>atau</em> indeks abjad sesuai rapor asli bila tidak ada nilai numerik.</span>
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
						
						<h4>SD, MI, atau sederajat</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('elementary_school_name', 'Nama Sekolah', 'required') ?></td>
								<td class="field">
									<?php $form->text('elementary_school_name', 'long'); ?><br>
									<span class="help-block">Cantumkan kota. Misal: SD <u>Negeri</u> 70 <u>Bandung</u></span>
									<span class="help-block">Jika Adik pernah berpindah sekolah (mutasi), tuliskan secara berurutan nama masing-masing sekolah yang pernah Adik masuki dengan memisahkannya dengan garis miring (/).</span>	
								</td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('elementary_school_graduation_year', 'Tahun Ijazah', 'required') ?></td>
								<td class="field"><?php $form->select_year('elementary_school_graduation_year', date('Y') - 5, date('Y') - 3); ?></td>
							</tr>
						</table>

						<table class="academics sd subform">
							<caption>
								<?php $form->label('grades_y1t1_average', 'Data prestasi', 'required') ?>
								<span class="help-block">Gunakan skala 0&ndash;100 untuk rata-rata nilai, <em>atau</em> indeks abjad sesuai rapor asli bila tidak ada nilai numerik.</span>
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

						<h4>Pengetahuan Bahasa</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('years_speaking_english', 'Sudah berapa lama Adik belajar Bahasa Inggris?', 'required') ?></td>
								<td class="field"><?php $form->text('years_speaking_english', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('other_languages', 'Bahasa lain yang Adik kuasai/pelajari') ?></td>
								<td class="field"><?php $form->text('other_languages', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('years_speaking_other_languages', 'Berapa lama?') ?></td>
								<td class="field"><?php $form->text('years_speaking_other_languages', 'long') ?></td>
							</tr>
						</table>
						
						<h4>Pelajaran Favorit dan Cita-Cita</h4>
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
					</fieldset>

					<fieldset class="pane" id="kegiatan">
						<?php $levels = array(
							'' => '(Tingkat)',
							'school' => 'Sekolah',
							'neighborhood' => 'RT/RW',
							'district' => 'Kecamatan',
							'city' => 'Kabupaten/Kota',
							'province' => 'Provinsi',
							'national' => 'Nasional',
							'international' => 'Internasional'
						); ?>
						<legend>Riwayat Kegiatan</legend>
						<!-- poin 15-19 -->
						<h4>Organisasi</h4>
						<table class="achievements subform">
							<caption>Organisasi yang pernah diikuti, baik di lingkungan sekolah maupun di luar lingkungan sekolah</caption>
							<thead>
								<tr>
									<th class="name">Nama Organisasi</th>
									<th class="kind">Jenis Kegiatan</th>
									<th class="level">Tingkat</th>
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
									<td class="level"><?php $s->select('level', array(
										'' => '(Tingkat)',
										'school' => 'Sekolah',
										'neighborhood' => 'RT/RW',
										'district' => 'Kecamatan',
										'city' => 'Kabupaten/Kota',
										'province' => 'Provinsi',
										'national' => 'Nasional',
										'international' => 'Internasional'
									), 'short')?></td>
									<td class="achv"><?php $s->text('position', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for ($i=count($subforms['applicant_organizations']) + 1; $i<=5; $i++):
								$s = new FormDisplay;
								$s->make_subform("applicant_organizations[$i]"); ?>
								<tr>
									<td class="name"><?php $s->text('name', 'short') ?></td>
									<td class="kind"><?php $s->text('kind', 'short') ?></td>
									<td class="level"><?php $s->select('level', array(
										'' => '(Tingkat)',
										'school' => 'Sekolah',
										'neighborhood' => 'RT/RW',
										'district' => 'Kecamatan',
										'city' => 'Kabupaten/Kota',
										'province' => 'Provinsi',
										'national' => 'Nasional',
										'international' => 'Internasional'
									), 'short')?></td>
									<td class="achv"><?php $s->text('position', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>
						<h4>Kesenian <span>(seni suara, seni musik, tari, teater, dll.)</span></h4>
						<?php $phase = 'kesenian'; ?>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('arts_hobby', 'Sekedar hobi', 'required') ?></td>
								<td class="field"><?php $form->text('arts_hobby', 'long') ?></td>
							</tr>		
							<tr>
								<td class="label"><?php $form->label('arts_organized', 'Ikut perkumpulan') ?></td>
								<td class="field"><?php $form->text('arts_organized', 'long') ?></td>
							</tr>
						</table>

						<table class="achievements subform" width="620">
							<caption>Prestasi</caption>
							<thead>
								<tr>
									<th class="name">Jenis</th>
									<th class="kind">Kejuaraan</th>
									<th class="level">Tingkat</th>
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
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for ($i=count($subforms['applicant_arts_achievements']) + 1; $i<=5; $i++):
								$s = new FormDisplay;
								$s->make_subform("applicant_arts_achievements[$i]"); ?>
								<tr>
									<td class="name"><?php $s->text('championship', 'short') ?></td>
									<td class="kind"><?php $s->text('kind', 'short') ?></td>
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>

						<h4>Olahraga</h4>
						<?php $phase = 'olahraga'; ?>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('sports_hobby', 'Sekedar hobi', 'required') ?></td>
								<td class="field"><?php $form->text('sports_hobby', 'long') ?></td>
							</tr>		
							<tr>
								<td class="label"><?php $form->label('sports_organized', 'Ikut perkumpulan') ?></td>
								<td class="field"><?php $form->text('sports_organized', 'long') ?></td>
							</tr>
						</table>
						<table class="achievements subform" width="620">
							<caption>Prestasi</caption>
							<thead>
								<tr>
									<th class="chmp">Kejuaraan</th>
									<th class="level">Tingkat</th>
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
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for($i=count($subforms['applicant_sports_achievements']) + 1; $i<=5; $i++):
								$s = new FormDisplay;
								$s->make_subform("applicant_sports_achievements[$i]"); ?>
								<tr>
									<td class="chmp"><?php $s->text('championship', 'short') ?></td>
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>

						<h4>Lain-lain</h4>
						<?php $phase = 'kegiatan_lain_lain'; ?>
						<table class="achievements subform">
							<caption>Kegiatan lain di luar olahraga dan kesenian</caption>
							<thead>
								<tr>
									<th class="chmp">Kegiatan</th>
									<th class="level">Tingkat</th>
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
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for($i=count($subforms['applicant_other_achievements']) + 1; $i<=5; $i++):
								$s = new FormDisplay;
								$s->make_subform("applicant_other_achievements[$i]"); ?>
								<tr>
									<td class="chmp"><?php $s->text('activity', 'short') ?></td>
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="achv"><?php $s->text('achievement', 'short') ?></td>
									<td class="year"><?php $s->select_year('year', date('Y') - 12, date('Y')) ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>

						<?php $phase = 'pengalaman_kerja'; ?>
						<table class="achievements subform">
							<caption>Pengalaman kerja sosial/magang/bekerja (di LSM, Yayasan, kantor, sekolah, koperasi, usaha, dll)</caption>
							<thead>
								<tr>
									<th class="ngo">Nama dan bidang tempat bekerja/magang</th>
									<th class="level">Tingkat</th>
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
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="ngo"><?php $s->text('position', 'short') ?></td>
									<td class="period"><?php $s->text('period') ?></td>
								</tr>
								<?php endforeach; ?>
								<?php for($i=count($subforms['applicant_work_experiences']) + 1; $i<=5; $i++):
								$s = new FormDisplay;
								$s->make_subform("applicant_work_experiences[$i]"); ?>
								<tr>
									<td class="ngo"><?php $s->text('organization', 'short') ?></td>
									<td class="level"><?php $s->select('level', $levels, 'short')?></td>
									<td class="ngo"><?php $s->text('position', 'short') ?></td>
									<td class="period"><?php $s->text('period') ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>
					</fieldset>

					<fieldset class="pane" id="perjalanan">
						<legend>Riwayat Perjalanan</legend>
						<h5>Pernahkah Adik melawat/berpergian dalam jangka pendek ke luar negeri?</h5>
						<label class="checkbox"><?php $form->checkbox('short_term_travel_has') ?> <?php $form->label('short_term_travel_has', 'Pernah') ?></label>
						<table class="form-table" data-toggle="short_term_travel_has">
							<tr>
								<td class="label"><?php $form->label('short_term_travel_destination', 'Ke mana?') ?></td>
								<td class="field"><?php $form->text('short_term_travel_destination', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('short_term_travel_when', 'Kapan?') ?></td>
								<td class="field"><?php $form->text('short_term_travel_when', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('short_term_travel_purpose', 'Dalam rangka apa?') ?></td>
								<td class="field"><?php $form->text('short_term_travel_purpose', 'long') ?></td>
							</tr>
						</table>
						
						<h5>Pernahkah Adik tinggal di luar negeri?</h5>
						<label class="checkbox"><?php $form->checkbox('long_term_travel_has') ?> <?php $form->label('long_term_travel_has', 'Pernah') ?></label>
						
						<table class="form-table" data-toggle="long_term_travel_has">
							<tr>
								<td class="label"><?php $form->label('long_term_travel_destination', 'Ke mana?') ?></td>
								<td class="field"><?php $form->text('long_term_travel_destination', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('long_term_travel_when', 'Kapan dan berapa lama?') ?></td>
								<td class="field"><?php $form->text('long_term_travel_when', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('long_term_travel_purpose', 'Dalam rangka apa?') ?></td>
								<td class="field"><?php $form->text('long_term_travel_purpose', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('long_term_travel_activities', 'Kegiatan Adik selama di sana?') ?></td>
								<td class="field"><?php $form->text('long_term_travel_activities', 'long') ?></td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="pane" id="referensi">
						<legend>Referensi</legend>
						
						<h5>Adakah di antara keluarga besar Adik yang pernah mengikuti program pertukaran yang diselenggarakan oleh Bina Antarbudaya/AFS?</h5>
						<label class="checkbox"><?php $form->checkbox('relative_returnee_exists') ?> <?php $form->label('relative_returnee_exists', 'Pernah') ?></label>
						
						<table class="form-table" data-toggle="relative_returnee_exists">
							<tr>
								<td class="label"><?php $form->label('relative_returnee_name', 'Nama') ?></td>
								<td class="field"><?php $form->text('relative_returnee_name', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('relative_returnee_relationship', 'Hubungan dengan Adik') ?></td>
								<td class="field"><?php $form->text('relative_returnee_relationship', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('relative_returnee_program', 'Program') ?></td>
								<td class="field">
								<?php $form->text('relative_returnee_program', 'medium');
								$form->select('relative_returnee_program_type', array(' ' => '', 'sending' => 'Sending', 'hosting' => 'Hosting'), 'short') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('relative_returnee_destination', 'Tujuan (sending)/Asal (hosting)') ?></td>
								<td class="field"><?php $form->text('relative_returnee_destination', 'long')  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('relative_returnee_address_street', 'Alamat sekarang') ?></td>
								<td class="field"><?php $form->address('relative_returnee', true, false, false, false, false, false, true) ?></td>
							</tr>
						</table>
						
						<h5>Pernahkah Adik atau keluarga Adik berpartisipasi dalam kegiatan Bina Antarbudaya/AFS sebelumnya?</h5>
						<label class="checkbox"><?php $form->checkbox('past_binabud_has') ?> <?php $form->label('past_binabud_has', 'Pernah') ?></label>
						
						<table class="form-table" data-toggle="past_binabud_has">
							<tr>
								<td class="label"><?php $form->label('past_binabud_activities_who', 'Nama') ?></td>
								<td class="field"><?php $form->text('past_binabud_activities_who', 'long')  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('past_binabud_activities_relationship', 'Hubungan dengan Adik') ?></td>
								<td class="field"><?php $form->text('past_binabud_activities_relationship', 'long')  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('past_binabud_activities', 'Kegiatan') ?></td>
								<td class="field"><?php $form->text('past_binabud_activities', 'long')  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('past_binabud_activities_year', 'Tahun') ?></td>
								<td class="field"><?php $form->select_year('past_binabud_activities_year', date('Y') - 50, date('Y'))  ?></td>
							</tr>
						</table>
						
						<h4>Referensi</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('referrer', 'Dari mana Adik mengetahui program kami?', 'required') ?></td>
								<td class="field"><?php $form->textarea('referrer', 'large');  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('motivation', 'Apa motivasi Adik mengikuti seleksi dan program Bina Antarbudaya?', 'required') ?></td>
								<td class="field"><?php $form->textarea('motivation', 'extra-large');  ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('hopes', 'Apa yang diharapkan Adik dengan keikutsertaan Adik dalam seleksi dan program Bina Antarbudaya?', 'required') ?></td>
								<td class="field"><?php $form->textarea('hopes', 'extra-large');  ?></td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="pane" id="rekomendasi">
						<legend>Rekomendasi</legend>
						<p>Sebutkan nama 3 (tiga) orang <u>di luar keluarga</u> Adik yang mengenal diri Adik secara pribadi untuk menuliskan surat rekomendasi bagi Adik. Diharapkan nama orang-orang tersebut tidak akan berganti pada saat Adik harus memintakan rekomendasi dari mereka. <i>Surat rekomendasi tidak perlu dikumpulkan pada saat pendaftaran seleksi.</i></p>
						<h4>Lingkungan sekolah (Guru atau Kepala Sekolah) <span>(berusia sekurang-kurangnya 21 tahun)</span></h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('recommendations_school_name', 'Nama', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_school_name', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_school_address', 'Alamat/Telepon', 'required') ?></td>
								<td class="field"><?php $form->textarea('recommendations_school_address') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_school_occupation', 'Pekerjaan', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_school_occupation', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_school_work_address', 'Alamat pekerjaan', 'required') ?></td>
								<td class="field"><?php $form->textarea('recommendations_school_work_address') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_school_relationship', 'Hubungan', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_school_relationship', 'long') ?></td>
							</tr>
						</table>
						<h4>Lingkungan rumah/organisasi di luar sekolah <span>(<strong>bukan keluarga,</strong> berusia sekurang-kurangnya 21 tahun)</span></h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('recommendations_nonschool_name', 'Nama', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_nonschool_name', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_nonschool_address', 'Alamat/Telepon', 'required') ?></td>
								<td class="field"><?php $form->textarea('recommendations_nonschool_address') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_nonschool_occupation', 'Pekerjaan', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_nonschool_occupation', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_nonschool_work_address', 'Alamat pekerjaan'/* , 'required' */) ?></td>
								<td class="field"><?php $form->textarea('recommendations_nonschool_work_address') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_nonschool_relationship', 'Hubungan', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_nonschool_relationship', 'long') ?><br>
									<span class="help-block">Pastikan yang bersangkutan tidak memiliki hubungan keluarga dengan Adik.</span></td>
							</tr>
						</table>
						<h4>Teman dekat</h4>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('recommendations_close_friend_name', 'Nama', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_close_friend_name', 'long') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_close_friend_address', 'Alamat/Telepon', 'required') ?></td>
								<td class="field"><?php $form->textarea('recommendations_close_friend_address') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('recommendations_close_friend_relationship', 'Hubungan', 'required') ?></td>
								<td class="field"><?php $form->text('recommendations_close_friend_relationship', 'long') ?></td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="pane" id="selfawareness">
						<legend>Kepribadian</legend>
						<table class="form-table">
							<tr>
								<td class="label"><?php $form->label('personality', 'Menurut Adik, seperti apakah sifat dan kepribadian adik?', 'required') ?></td>
								<td class="field"><?php $form->textarea('personality', 'extra-large') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('strengths_and_weaknesses', 'Apakah kelebihan/kekurangan Adik?', 'required') ?></td>
								<td class="field"><?php $form->textarea('strengths_and_weaknesses', 'extra-large') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('stressful_conditions', 'Hal-hal apakah yang sering membuat Adik merasa tertekan?', 'required') ?></td>
								<td class="field"><?php $form->textarea('stressful_conditions', 'extra-large') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('biggest_life_problem', 'Masalah terberat apakah yang pernah Adik hadapi? Bagaimana Adik menyelesaikannya?', 'required') ?></td>
								<td class="field"><?php $form->textarea('biggest_life_problem', 'extra-large') ?></td>
							</tr>
							<tr>
								<td class="label"><?php $form->label('plans', 'Apakah rencana Adik berkaitan dengan pendidikan dan karir di masa depan?', 'required') ?></td>
								<td class="field"><?php $form->textarea('plans', 'extra-large') ?></td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="pane" id="volunteering">
						<legend>Volunteering &amp; Hosting</legend>

						<div class="promotional">
							<p><span>Bina Antarbudaya adalah organisasi nirlaba berbasis relawan.</span> Seluruh panitia yang terlibat dalam proses
							pendaftaran dan seleksi Bina Antarbudaya adalah relawan dari berbagai kalangan yang bekerja secara sukarela.
							Kami mengundang Adik untuk terlibat dalam kegiatan kerelawanan di Bina Antarbudaya setelah rangkaian seleksi selesai.</p>
							<p class="checkbox"><label class="checkbox"><?php $form->checkbox('interested_volunteering') ?> <?php $form->label('interested_volunteering', 'Saya tertarik untuk menjadi relawan Bina Antarbudaya') ?></label></p>
						</div>
						
						<h4>Program Hosting</h4>
						<div class="promotional">
							<div class="row-fluid">
								<div class="span6">
									<h5>Apa itu program hosting?</h5>
									<p>Setiap tahunnya Bina Antarbudaya menerima siswa dan relawan asing untuk tinggal dan bersekolah di Indonesia
									selama kurang lebih 10 bulan. Selama di Indonesia, setiap siswa/relawan asing akan tinggal
									bersama satu keluarga angkat (host family) dan menjalani kehidupan layaknya seorang pelajar/relawan lokal.</p>
									
									<h5>Siapa saja yang bisa kami host?</h5>
									<ul>
										<li>Siswa/siswi program pertukaran pelajar AFS dan YES, berusia 15-18 tahun</li>
										<li>Relawan peserta program Community Service, berusia diatas 18 tahun</li>
									</ul>

									<h5>Apa keuntungan menjadi keluarga angkat?</h5>
									<ul>
										<li>Memperkenalkan Indonesia kepada dunia</li>
										<li>Mengetahui dan memahami budaya, agama dan kebiasaan serta cara hidup di Negara lain melalui siswa/relawan asing untuk membantu mengembangkan pengetahuan dan wawasan seluruh anggota keluarga, tanpa harus bepergian ke luar negeri</li>
										<li>Menciptakan hubungan kekeluargaan antar negara</li>
										<li>Menjadi lebih sensitif terhadap permasalahan global</li>
										<li>Mendapatkan pendidikan informal untuk seluruh anggota keluarga karena dengan menjadi keluarga angkat, keluarga anda akan terlibat dalam suatu proses komunikasi antar budaya, juga membangun pemahaman dan kedewasaan bagi putra/putri Anda juga bagi peserta program pertukaran pelajar/relawan.</li>
									</ul>
								</div>
								<div class="span6">
									<h5>Siapa saja yang bisa menjadi Host Family?</h5>
									<p>Pada dasarnya setiap keluarga yang tertarik, bersedia dan memiliki waktu untuk memperkenalkan kehidupan keluarga Anda kepada peserta program dapat menjadi Host Family. Akan tetapi, berdasarkan pengalaman Bina Antarbudaya ada beberapa kriteria ideal yang sebaiknya dimiliki calon Host Family, yaitu:</p>
									<ul>
										<li>Seluruh anggota keluarga menyatakan ketertarikan dan kesediaannya untuk menerima siswa/relawan asing di rumah dan menjadi bagian dari anggota keluarga.</li>
										<li>Salah satu atau kedua orang tua berusia diatas 30 tahun.</li>
										<li>Terdapat putra/putri berusia 15 – 18 tahun atau berusia kira-kira sama dengan siswa/relawan asing di keluarga.</li>
										<li>Salah satu anggota keluarga mampu berkomunikasi dalam bahasa Inggris.</li>
										<li>Keluarga masih memegang teguh nilai dan budaya Indonesia, tetapi juga terbuka untuk mengenal dan belajar budaya lain.</li>
										<li>Keluarga mampu dan bersedia meluangkan waktu, kasih sayang dan kesempatan untuk meluangkan waktu bersama siswa/relawan asing.</li>
										<li>Keluarga memiliki ikatan kekeluargaan yang kuat, hangat dan terbuka.</li>
									</ul>
								</div>
							</div>
							<p class="checkbox"><label class="checkbox"><?php $form->checkbox('interested_hosting') ?> <?php $form->label('interested_hosting', 'Keluarga saya tertarik untuk menjadi host family') ?></label></p>
						</div>
					</fieldset>

					<fieldset class="pane" id="foto">
						<legend>Pas Foto</legend>

						<table class="form-table">
							<?php if ($picture): ?>
							<tr>
								<td class="label">Foto Saat Ini</td>
								<td class="field">
									<img src="<?php echo $picture->get_cropped_url(); ?>" width="300" height="400">
								</td>
							</tr>
							<?php endif; ?>
							<tr>
								<td class="label"><?php $form->label('picture', 'Unggah Foto'  . ($picture ? ' Baru' : ''),  ($picture ? '' : ' required')) ?></td>
								<td class="field">
									<input type="hidden" name="MAX_FILE_SIZE" value="2048000">
									<input type="file" name="picture" id="picture" class="medium">
									<br>
									<span class="help-block">Gunakan <strong>pas foto berwarna</strong>. Ukuran berkas maksimal 2MB. </span>
								</td>
							</tr>
							<tr>
								<td class="label"></td>
								<td class="field">
									<button type="submit" class="btn">Unggah</button>
								</td>
							</tr>
						</table>
					</fieldset>
					<?php if (!$admin): ?>
					<fieldset class="pane" id="finalisasi">
						<legend>Finalisasi</legend>
						<p>
							Untuk melanjutkan pendaftaran Adik, Adik perlu melakukan finalisasi. Setelah finalisasi, informasi pada formulir ini dikunci dan Adik tidak dapat mengubahnya kembali. Oleh sebab itu, <em>pastikan seluruh kolom pada formulir ini telah terisi dengan lengkap dan benar sebelum melakukan finalisasi</em>. Kelalaian dalam mengisi formulir akan mengakibatkan penolakan pengumpulan berkas.
						</p>
						<p>
							Dengan finalisasi, Adik juga menyatakan bahwa seluruh informasi yang Adik isi dalam formulir ini adalah benar dan apa adanya, serta dibuat tanpa paksaan dari pihak manapun.
						</p>
						<p class="recheck">
							Adik belum dapat melakukan finalisasi karena Adik belum mengisi formulir dengan lengkap. Lengkapi bagian-bagian  formulir yang ditandai dengan warna merah sebelum kembali ke laman ini. Adik masih bisa menyimpan sementara formulir ini jika Adik perlu.
						</p>
						<p class="recheck">
							Tekan tombol 'Simpan Sementara' untuk menonaktifkan penandaan kolom-kolom.
						</p>
						<p class="finalize-checkbox">
							<input type="checkbox" name="finalize" value="true" id="finalize"> <label for="finalize"><strong>Saya mengerti.</strong></label>
						</p>
						<p data-toggle="finalize">
							<button type="submit" id="finalize-button" class="btn btn-large btn-primary">Finalisasi</button>
						</p>
					</fieldset>
					<?php endif; ?>

					<p class="required-fields-help help-block">
						Kolom bertanda <span class="required-sample">*</span> wajib diisi.
					</p>
					<ul class="pager">
						<li class="previous">
							<a href="#_prev">&larr; Halaman Sebelumnya</a>
						</li>
						<li class="next">
							<a href="#_next">Halaman Selanjutnya &rarr;</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
	var last_pane = '<?php echo $last_pane ? $last_pane : '' ?>';
	var firstTime = <?php echo $new ? 'true' : 'false' ?>;
	var incomplete = <?php echo json_encode($incomplete) ?>;
	var programYear = <?php echo $applicant->program_year ?>;
	var required_fields = <?php echo json_encode(Applicant::required_fields()) ?>;
	var dob_upper_limit = new Date('<?php echo $dob_upper_limit->format('r') ?>');
	var dob_lower_limit = new Date('<?php echo $dob_lower_limit->format('r') ?>');
	var dob_lower_limit_yes = new Date('<?php echo $dob_lower_limit_yes->format('r') ?>');
</script>
<?php
$this->require_js('form');
$this->print_footer();
?>
<?php $this->print_header() ?>

<header class="page-header">
	<h1><?php echo $chapter->get_title() ?> <span>Dashboard</span></h1>
</header>

<div class="chapter-dashboard <?php echo $current_phase ?>-phase">
	<!-- <h3 class="current-phase">
		<?php switch ($current_phase) {
			case 'registration':
				echo 'Saat ini: <span>Masa Pendaftaran</span> <small>1 Maret &ndash; 14 April 2013</small>';
				break;
		} ?>
	</h3> -->
	<div class="quick-links">
		<ul class="nav nav-pills">
		<?php if ($current_phase == 'registration'): ?>
		<?php if ($chapter->is_national_office()): ?>
			<li><a href="<?php L(array('controller' => 'registration_code', 'action' => 'issue')) ?>">Terbitkan PIN Pendaftaran</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Daftar Pendaftar Nasional</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants', 'view' => 'stats')) ?>">Statistik Pendaftar Nasional</a></li>
		<?php else: // Registration phase ?>
			<li><a href="<?php L(array('controller' => 'registration_code', 'action' => 'issue')) ?>">Terbitkan PIN Pendaftaran</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Daftar Pendaftar</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants', 'view' => 'stats', 'chapter_id' => $id)) ?>">Statistik Pendaftar</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit', 'id' => $id)) ?>">Edit Informasi Chapter</a></li>
		<?php endif; ?>
		<?php else: ?>
		<?php if ($chapter->is_national_office()): ?>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Daftar Peserta Nasional</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants', 'view' => 'stats')) ?>">Statistik Peserta Nasional</a></li>
			<li><a href="<?php L(array('controller' => 'registration_code', 'action' => 'issue')) ?>">Terbitkan PIN Pendaftaran Outreach</a></li>
			<li><a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list')) ?>">Unduh Daftar Peserta Keseluruhan</a></li>
		<?php else: // Registration phase ?>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Daftar Peserta</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants', 'view' => 'stats', 'chapter_id' => $id)) ?>">Statistik Peserta</a></li>
			<li><a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list')) ?>">Unduh Daftar Peserta Keseluruhan</a></li>
			<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit', 'id' => $id)) ?>">Edit Informasi Chapter</a></li>
		<?php endif; ?>
		<?php endif; ?>
		</ul>
	</div>
<?php if ($current_phase == 'registration'): ?>
	<div class="row">
		<div class="span4">
			<h4>PIN Pendaftaran</h4>
			<table class="table counts">
				<tr>
					<td rowspan="3">
						<span title="Jumlah seluruh PIN pendaftaran yang telah di-generate melalui sistem"><strong><?php echo $code_count ?></strong> <i>generated</i></span>
					</td>
					<td>
						<span title="Jumlah PIN pendaftaran yang telah digunakan untuk membuat akun baru atau memperpanjang akun yang telah kadaluarsa"><strong class="text-info"><?php echo $activated_code_count ?></strong> terpakai</span>
					</td>
				</tr>
				<tr>
					<td>
						<span title="Jumlah PIN pendaftaran yang masih dapat digunakan"><strong class="text-success"><?php echo $available_code_count ?></strong> tersedia</span>
					</td>
				</tr>
				<tr>
					<td>
						<span title="Jumlah PIN yang telah kadaluarsa"><strong class="muted"><?php echo $expired_code_count ?></strong> kadaluarsa</span>
					</td>
				</tr>
			</table>
			<p>
				<a class="btn btn-primary btn-block" href="<?php L(array('controller' => 'registration_code', 'action' => 'issue', 'chapter_id' => $id)) ?>">Terbitkan PIN baru</a>
			</p>
			<p>
				<a class="btn btn-block" href="<?php L(array('controller' => 'registration_code', 'action' => 'index', 'chapter_id' => $id)) ?>">Daftar PIN yang sudah diterbitkan</a>
			</p>
		</div>
		<div class="span4">

			<h4>Jumlah Pendaftar Saat Ini</h4>

			<table class="table counts four-col">
				<tr>
					<td rowspan="4">
						<span title="Siswa yang sudah pernah mengaktifkan PIN pendaftaran dan membuat akun"><strong><?php echo $total_applicant_count ?></strong> pendaftar total</span>
					</td>
					<td rowspan="3" data-stage="active">
						<span title="Siswa yang akun pendaftarannya belum kadaluarsa"><strong class="text-info"><?php echo $active_applicant_count ?></strong> pendaftar aktif</span>
					</td>
					<td rowspan="2" data-stage="finalized">
						<span title="Siswa yang sudah melakukan finalisasi pada formulirnya"><strong class="text-success"><?php echo $finalized_applicant_count ?></strong> sudah finalisasi</span>
					</td>
					<td data-stage="confirmed">
						<span title="Siswa yang sudah diverifikasi bahwa berkasnya sudah diterma"><strong class="text-success"><?php echo $confirmed_applicant_count ?></strong> sudah verifikasi berkas</span>
					</td>
				</tr>
				<tr>
					<td data-stage="not_yet_confirmed">
						<span title="Siswa yang sudah melakukan finalisasi, namun belum diverifikasi bahwa berkasnya sudah diterma"><strong class="text-warning"><?php echo $not_yet_confirmed_applicant_count ?></strong> belum verifikasi berkas</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" data-stage="incomplete">
						<span title="Siswa yang formulirnya belum lengkap dan belum melakukan finalisasi"><strong class="text-warning"><?php echo $incomplete_applicant_count ?></strong> masih mengisi formulir</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" data-stage="expired">
						<span title="Siswa yang akun pendaftarannya sudah kadaluarsa"><strong class="text-error"><?php echo $expired_applicant_count ?></strong> kadaluarsa</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="span4 applicant-search">
			<h4>Pencarian Pendaftar</h4>
			<h5>Berdasarkan nomor peserta</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'view_applicant')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="YBA/YP15-16/<?php if (!$chapter->is_national_office()) echo $chapter->chapter_code . '/' ?>" name="test_id" placeholder="Nomor peserta">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<h5>Berdasarkan nama dan/atau asal sekolah</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">
				<div>
					<input type="text" class="span2" value="" name="combo" placeholder="Nama peserta">
					<input type="text" class="span2" value="" name="school_name" placeholder="Nama sekolah">
				</div>
				<p>
					<button type="submit" class="btn">Cari</button> <span class="help-inline">(Isi salah satu atau keduanya.)</span>
				</p>
			</form>
			<h5>Berdasarkan username</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'view_applicant')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="" name="username" placeholder="Username peserta">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<h5>Berdasarkan PIN</h5>
			<form action="<?php L(array('controller' => 'registration_code', 'action' => 'recover')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="" name="pin" placeholder="PIN pendaftaran">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<p>
				<a class="btn btn-primary" href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Telusuri seluruh pendaftar</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="span4">
			<h4>Contact Person &amp; Pengumpulan Berkas</h4>
			<p>
				Kakak dari <?php echo $chapter->get_title() ?> yang akan dihubungi pendaftar mengenai masalah pendaftaran adalah:
				<br><strong><?php echo $contact_person_name ? $contact_person_name : '(Belum ada)'; ?></strong>
				<?php if ($contact_person_phone) echo "($contact_person_phone)"; ?>
			</p>
			<p>Setelah menyelesaikan pengisian formulir, pendaftar akan diinstruksikan untuk mengumpulkan berkas ke:<br>
				<strong><?php echo $chapter->get_title() ?></strong><br>
				<?php echo nl2br($chapter_address) ?>
			</p>
			<p><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit')) ?>" class="btn">Ubah informasi ini</a></p>
		</div>
		<div class="span4">
			<h4>Pendaftaran Offline</h4>
			<div class="alert alert-warning">Fitur ini masih dalam pengembangan</div>
			<p>
				Untuk melayani pendaftar yang kesulitan mengakses internet untuk mengisi formulir,
				disediakan formulir dalam bentuk berkas Excel yang dapat disimpan, diisi, kemudian diunggah oleh Kakak Chapter.
			</p>
			<p><a class="btn btn-block btn-primary disabled" href="#"><b>Unduh</b> kit pendaftaran offline</a></p>
			<p><a class="btn btn-block disabled" href="#"><b>Unggah</b> formulir Excel yang sudah diisi</a></p>
		</div>
		<div class="span4">
			<h4>Bantuan</h4>
			<p>Untuk permasalahan teknis, hubungi:<br>
			<b>Kak Rio (IT Helpdesk Kantor Nasional)</b>,<br>
			<b>Kak Pandu (Tim OR)</b>, atau <b>Kak Gici (Tim OR)</b> di <strong>seleksi@bina-antarbudaya.or.id</strong>.<p>
			<p>Untuk permasalahan nonteknis (syarat pendaftaran, kasus-kasus khusus), harap hubungi<br>
			<strong>Kak Sari (sari.tjakra@afs.org)</strong>.</p>
			<p><span class="btn btn-block btn-primary disabled">Alur Pendaftaran Seleksi YP 2015/2016</span></p>
		</div>
	</div>
<?php else: // Post-registration phase ?>
	<div class="row">
		<div class="span4">
			<?php
				$results_deadline_conf = 'results_deadline_selection_' . ($next_selection_stage - 1);
				$results_deadline = new HeliumDateTime(Helium::conf($results_deadline_conf));
			?>
			<h4>Persiapan Seleksi <?php
				if ($next_selection_stage > 3)
					echo 'Nasional';
				elseif ($next_selection_stage > 2)
					echo 'Tahap Ketiga';
				elseif ($next_selection_stage > 1)
					echo 'Tahap Kedua';
				else
					echo 'Tahap Pertama';
			?></h4>
			<dl class='selection-todos'>
				<dt>
					<a href="<?php L(array('controller' => 'chapter', 'action' => 'participant_tab')) ?>"
					   class="btn btn-block btn-primary">Unduh <strong>lembar tabulasi standar</strong> (Format MS Excel)</a>
				</dt>
				<dd>
					Lembar tabulasi tersebut harap diisi dan dikirim melalui email ke
					<strong><a href="mailto:sari.tjakra@afs.org">Kak Sari (sari.tjakra@afs.org)</a></strong>.
				</dd>
				<dd>
					Pengiriman tabulasi paling lambat
					<strong><?php echo $results_deadline->format('l, j F Y') ?></strong>.
				</dd>
		<?php if ($next_selection_stage > 3): ?>
		<?php elseif ($next_selection_stage > 2): ?>
		<?php elseif ($next_selection_stage > 1):
			$announcement_date = new HeliumDateTime(Helium::conf('selection_one_announcement_date'));
		?>
				<dt>
					<a href="<?php L(array('controller' => 'selection_two', 'action' => 'create_batch')) ?>"
					   class="btn btn-block btn-primary">Masukkan <strong>daftar kelulusan</strong> Seleksi Tahap Pertama</a>
				</dt>
				<dd>
					Daftar tersebut berbeda dengan lembar tabulasi dan digunakan untuk mengumumkan
					kelulusan peserta melalui web ini.
				</dd>
				<dd>
					Pengisian daftar paling lambat
					<strong><?php echo $results_deadline->format('l, j F Y') ?>.</strong>
				</dd>
				<dd>
				  Peserta dapat melihat pengumuman kelulusan mulai <strong><?php echo $announcement_date->format('l, j F Y') ?></strong>
				  melalui <a href="<?php L('/') ?>">web seleksi</a>.
				</dd>
		<?php else: ?>
		<?php endif; ?>
				<dt>
					<a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list')) ?>"
					   class="btn btn-block">Unduh daftar peserta keseluruhan</a>
				</dt>
			</ol>
		</div>
		<div class="span4">

			<h4>Jumlah Peserta Seleksi</h4>

			<table class="table counts four-col">
				<tr>
					<td rowspan="4">
						<span title="Siswa yang telah menyelesaikan pendaftaran">
							<strong><?php echo $finalized_applicant_count ?></strong>
							peserta total (sudah finalisasi)
						</span>
					</td>
					<td rowspan="3" data-stage="active">
						<span title="Siswa yang telah lulus Seleksi Tahap Pertama dan berhak mengikuti Seleksi Tahap Kedua">
							<strong class="text-success"><?php echo $participant_count_passed_1 ?></strong>
							lulus seleksi tahap pertama
						</span>
					</td>
					<td rowspan="2" data-stage="finalized">
						<span title="Siswa yang telah lulus Seleksi Tahap Kedua dan berhak mengikuti Seleksi Tahap Ketiga">
							<strong class="text-success"><?php echo $participant_count_passed_2 ?></strong>
							lulus seleksi tahap kedua
						</span>
					</td>
					<td data-stage="confirmed">
						<span title="Siswa yang telah lulus Seleksi Tahap Ketiga dan menjadi Kandidat Chapter untuk Seleksi Nasional">
							<strong class="text-success"><?php echo $participant_count_passed_3 ?></strong>
							lulus seleksi tahap ketiga
						</span>
					</td>
				</tr>
				<tr>
					<td data-stage="not_yet_confirmed">
						<span title="Siswa yang tidak lulus Seleksi Tahap Ketiga">
							<strong class="text-warning"><?php echo $participant_count_passed_2 - $participant_count_passed_3 ?></strong>
							gagal seleksi tahap ketiga
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" data-stage="incomplete">
						<span title="Siswa yang tidak lulus Seleksi Tahap Kedua">
							<strong class="text-warning"><?php echo $participant_count_passed_1 - $participant_count_passed_2 ?></strong>
							gagal seleksi tahap kedua
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" data-stage="expired">
						<span title="Siswa yang tidak lulus Seleksi Tahap Pertama">
							<strong class="text-warning"><?php echo $finalized_applicant_count - $participant_count_passed_1 ?></strong>
							gagal seleksi tahap pertama
						</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="span4 applicant-search">
			<h4>Pencarian Peserta</h4>
			<h5>Berdasarkan nomor peserta</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'view_applicant')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="YBA/YP15-16/<?php if (!$chapter->is_national_office()) echo $chapter->chapter_code . '/' ?>" name="test_id" placeholder="Nomor peserta">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<h5>Berdasarkan nama dan/atau asal sekolah</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">
				<div>
					<input type="text" class="span2" value="" name="combo" placeholder="Nama peserta">
					<input type="text" class="span2" value="" name="school_name" placeholder="Nama sekolah">
				</div>
				<p>
					<button type="submit" class="btn">Cari</button> <span class="help-inline">(Isi salah satu atau keduanya.)</span>
				</p>
			</form>
			<h5>Berdasarkan username</h5>
			<form action="<?php L(array('controller' => 'chapter', 'action' => 'view_applicant')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="" name="username" placeholder="Username peserta">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<h5>Berdasarkan PIN</h5>
			<form action="<?php L(array('controller' => 'registration_code', 'action' => 'recover')) ?>">
				<p class="input-append">
					<input type="text" class="input-block-level" value="" name="pin" placeholder="PIN pendaftaran">
					<button type="submit" class="btn">Lacak</button>
				</p>
			</form>
			<p>
				<a class="btn btn-primary" href="<?php L(array('controller' => 'chapter', 'action' => 'applicants')) ?>">Telusuri seluruh pendaftar</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="span4">
			<h4>PIN Pendaftaran</h4>
			<table class="table counts">
				<tr>
					<td rowspan="3">
						<span title="Jumlah seluruh PIN pendaftaran yang telah di-generate melalui sistem"><strong><?php echo $code_count ?></strong> <i>generated</i></span>
					</td>
					<td>
						<span title="Jumlah PIN pendaftaran yang telah digunakan untuk membuat akun baru atau memperpanjang akun yang telah kadaluarsa"><strong class="text-info"><?php echo $activated_code_count ?></strong> terpakai</span>
					</td>
				</tr>
				<tr>
					<td>
						<span title="Jumlah PIN pendaftaran yang masih dapat digunakan"><strong class="text-success"><?php echo $available_code_count ?></strong> tersedia</span>
					</td>
				</tr>
				<tr>
					<td>
						<span title="Jumlah PIN yang telah kadaluarsa"><strong class="muted"><?php echo $expired_code_count ?></strong> kadaluarsa</span>
					</td>
				</tr>
			</table>
			<?php if ($chapter->is_national_office()): ?>
			<p>
				<a class="btn btn-primary btn-block" href="<?php L(array('controller' => 'registration_code', 'action' => 'issue', 'chapter_id' => $id)) ?>">Terbitkan PIN baru untuk Chapter Outreach</a>
			</p>
			<?php endif; ?>
			<p>
				<a class="btn btn-block" href="<?php L(array('controller' => 'registration_code', 'action' => 'index', 'chapter_id' => $id)) ?>">Daftar PIN yang sudah diterbitkan</a>
			</p>
		</div>
		<div class="span4">

			<h4>Jumlah Pendaftar berdasarkan Status</h4>

			<table class="table counts four-col">
				<tr>
					<td rowspan="4">
						<span title="Siswa yang sudah pernah mengaktifkan PIN pendaftaran dan membuat akun"><strong><?php echo $total_applicant_count ?></strong> pendaftar total</span>
					</td>
					<td rowspan="3" data-stage="active">
						<span title="Siswa yang akun pendaftarannya belum kadaluarsa"><strong class="text-info"><?php echo $active_applicant_count ?></strong> pendaftar aktif</span>
					</td>
					<td rowspan="2" data-stage="finalized">
						<span title="Siswa yang sudah melakukan finalisasi pada formulirnya"><strong class="text-success"><?php echo $finalized_applicant_count ?></strong> sudah finalisasi</span>
					</td>
					<td data-stage="confirmed">
						<span title="Siswa yang sudah diverifikasi bahwa berkasnya sudah diterma"><strong class="text-success"><?php echo $confirmed_applicant_count ?></strong> sudah verifikasi berkas</span>
					</td>
				</tr>
				<tr>
					<td data-stage="not_yet_confirmed">
						<span title="Siswa yang sudah melakukan finalisasi, namun belum diverifikasi bahwa berkasnya sudah diterma"><strong class="text-warning"><?php echo $not_yet_confirmed_applicant_count ?></strong> belum verifikasi berkas</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" data-stage="incomplete">
						<span title="Siswa yang formulirnya belum lengkap dan belum melakukan finalisasi"><strong class="text-warning"><?php echo $incomplete_applicant_count ?></strong> masih mengisi formulir</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" data-stage="expired">
						<span title="Siswa yang akun pendaftarannya sudah kadaluarsa"><strong class="text-error"><?php echo $expired_applicant_count ?></strong> kadaluarsa</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="span4">
			<h4>Contact Person &amp; Pengumpulan Berkas</h4>
			<p>
				Kakak dari <?php echo $chapter->get_title() ?> yang akan dihubungi pendaftar mengenai masalah seleksi adalah:
				<br><strong><?php echo $contact_person_name ? $contact_person_name : '(Belum ada)'; ?></strong>
				<?php if ($contact_person_phone) echo "($contact_person_phone)"; ?>
			</p>
			<p>Peserta akan diinstruksikan untuk mengumpulkan berkas-berkas seleksi ke:<br>
				<strong><?php echo $chapter->get_title() ?></strong><br>
				<?php echo nl2br($chapter_address) ?>
			</p>
			<p><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit')) ?>" class="btn">Ubah informasi ini</a></p>
		</div>
		<div class="span4">
			<h4>Bantuan</h4>
			<p>Untuk permasalahan teknis, hubungi:<br>
			<b>Kak Rio (IT Helpdesk Kantor Nasional)</b>,<br>
			<b>Kak Pandu (Tim OR)</b>, atau <b>Kak Gici (Tim OR)</b> di <strong>seleksi@bina-antarbudaya.or.id</strong>.<p>
			<p>Untuk permasalahan nonteknis (syarat pendaftaran, kasus-kasus khusus), harap hubungi<br>
			<strong>Kak Sari (sari.tjakra@afs.org)</strong>.</p>
			<p><span class="btn btn-block btn-primary disabled">Alur Pendaftaran Seleksi YP 2015/2016</span></p>
		</div>
	</div>
<?php endif; ?>
</div>

<!-- <div class="alert">Kakak sedang berada pada tampilan baru dashboard chapter. <a href="/chapter/view">Gunakan tampilan lama.</a></div> -->
<?php
$this->require_js('dashboard');
$this->print_footer()
?>
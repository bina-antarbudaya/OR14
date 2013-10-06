<?php $this->header() ?>
<header class="page-title">
	<h1><?php echo $chapter->get_title() ?></h1>
</header>
<nav class="actions-nav">
	<ul>
	<?php if ($chapter->is_national_office()): ?>
		<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'index')) ?>">Daftar Chapter</a></li>
		<li><a href="<?php L(array('controller' => 'applicant', 'action' => 'index')) ?>">Daftar Pendaftar Nasional</a></li>
		<li><a href="<?php L(array('controller' => 'applicant', 'action' => 'stats', 'chapter_id' => $id)) ?>">Statistik Pendaftar Nasional</a></li>
	<?php elseif (true): // Registration phase ?>
		<li><a href="<?php L(array('controller' => 'registration_code', 'action' => 'issue')) ?>">Terbitkan PIN Pendaftaran</a></li>
		<li><a href="<?php L(array('controller' => 'applicant', 'action' => 'index', 'chapter_id' => $id)) ?>">Daftar Pendaftar</a></li>
		<li><a href="<?php L(array('controller' => 'applicant', 'action' => 'stats', 'chapter_id' => $id)) ?>">Statistik Pendaftar</a></li>
		<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit', 'id' => $id)) ?>">Edit Informasi Chapter</a></li>
	<?php endif; ?>
	</ul>
</nav>

<div class="container">
	<section class="primary">
		<?php if ($this->can_register()): ?>
		<article class="registration-codes">
			<header>
				<h1>PIN Pendaftaran</h1>
			</header>
			<ul class="counts codes">
				<li class="issued"><strong><?php echo $code_count ?></strong> tercetak</li>
				<li class="activated"><strong><?php echo $activated_code_count ?></strong> terpakai</li>
				<li class="activated"><strong><?php echo $available_code_count ?></strong> tersedia</li>
				<li class="activated"><strong><?php echo $expired_code_count ?></strong> kadaluarsa</li>
			</ul>
			<form class="quick-view" action="<?php L(array('controller' => 'registration_code', 'action' => 'recover')) ?>">
				<p>
					<input type="text" class="medium-short" value="" name="pin" placeholder="PIN Pendaftaran">
					<button type="submit">Lacak</button>
				</p>
			</form>
			<p class="more"><a href="<?php L(array('controller' => 'registration_code', 'action' => 'issue', 'chapter_id' => $id)) ?>">Terbitkan PIN pendaftaran</a></p>
			<p class="more"><a href="<?php L(array('controller' => 'registration_code', 'action' => 'index', 'chapter_id' => $id)) ?>">Lihat daftar selengkapnya</a></p>
		</article>
		
		<article class="applicants-summary">
			<header>
				<h1>Jumlah Pendaftar</h1>
			</header>
			<ul class="counts applicants">
				<li class="total"><strong><?php echo $total_applicant_count ?></strong> total</li>
				<li class="active"><strong><?php echo $active_applicant_count ?></strong> aktif
					<?php if (!$applicant_tipping_point): ?>
					<ul>
						<li class="total"><strong><?php echo $confirmed_applicant_count ?></strong> terkonfirmasi</li>
						<li class="active"><strong><?php echo $finalized_applicant_count ?></strong> terfinalisasi</li>
						<li class="expired"><strong><?php echo $incomplete_applicant_count ?></strong> masih mengisi</li>
					</ul>
					<?php endif; ?>
				</li>
				<li class="expired"><strong><?php echo $expired_applicant_count ?></strong> kadaluarsa</li>
				<li class="expired"><strong><?php echo $anomalous_applicant_count ?></strong> belum dikonfirmasi</li>
			</ul>
		</article>
		<?php else: ?>
		<article class="selection-mgt">
			<header>
				<h1>Pengelolaan Seleksi</h1>
			</header>
			<ul class="counts codes">
				<li class="all"><strong><?php echo $participant_count ?></strong> jumlah peserta</li>
				<li class="passed1"><strong><?php echo $participant_count_2 ? $participant_count_2 : 'N/A';  ?></strong> lolos seleksi 1</li>
				<li class="passed2"><strong><?php echo $participant_count_3 ? $participant_count_3 : 'N/A' ?></strong> lolos seleksi 2</li>
				<li class="passed3"><strong><?php echo $participant_count_4 ? $participant_count_4 : 'N/A' ?></strong> lolos seleksi 3</li>
			</ul>
			<p class="more"><a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list', 'chapter_id' => $id)) ?>">Unduh daftar seluruh peserta (XLSX)</a></p>
			<p class="more"><a href="<?php L(array('controller' => 'chapter', 'action' => 'participant_tab', 'chapter_id' => $id)) ?>">Unduh lembar tabulasi standar (XLSX)</a></p>
			<?php if ($chapter->is_national_office()): ?>
			<p class="more">Unduh Master XLSX: <a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list_new')) ?>">Semua</a> / <a href="<?php L(array('controller' => 'participant', 'action' => 'participant_list_new', 'only_candidates' => 'true')) ?>">Hanya Kandidat Chapter</a></p>
			<?php endif; ?>
		</article>
		<article class="selection-todos">
		<?php if ($next_selection_stage): ?>
			<header>
				<h1>To-Do List Seleksi Tahap <?php $w = array(1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga', 4 => 'Nasional'); echo $w[$next_selection_stage] ?></h1>
			</header>
		<?php switch ($next_selection_stage):
			case 1:
		?>
		<?php
			break;
			case 2:
		?>
			<ol>
				<li>Unduh <a href="<?php L(array('controller' => 'chapter', 'action' => 'participant_tab', 'chapter_id' => $id)) ?>">lembar tabulasi standar</a>, isi dengan hasil tabulasi chapter dan kirimkan ke <a href="mailto:sari.tjakra@afs.org">sari.tjakra@afs.org</a>.</li>
				<li>Unggah <a href="<?php L(array('controller' => 'selection_two', 'action' => 'create_batch')) ?>">daftar kelulusan Seleksi Tahap Pertama</a></li>
				<li>Atur <a href="<?php L(array('controller' => 'selection_two', 'action' => 'index')) ?>">pembagian ruangan</a></li>
			</ol>
		<?php
			break;
			case 3:
		?>
			<ol>
				<li>Unduh <a href="<?php L(array('controller' => 'chapter', 'action' => 'participant_tab', 'chapter_id' => $id)) ?>">lembar tabulasi standar</a>, isi dengan hasil tabulasi chapter dan kirimkan ke <a href="mailto:sari.tjakra@afs.org">sari.tjakra@afs.org</a>.</li>
				<li>Unggah <a href="<?php L(array('controller' => 'selection_three', 'action' => 'create_batch')) ?>">daftar kelulusan Seleksi Tahap Kedua</a></li>
			</ol>
			<hr>
			<header>
				<h1>To-Do List Seleksi Tahap Kedua</h1>
			</header>
			<ol>
				<li>Unggah <a href="<?php L(array('controller' => 'selection_two', 'action' => 'create_batch')) ?>">daftar kelulusan Seleksi Tahap Pertama</a></li>
				<li>Atur <a href="<?php L(array('controller' => 'selection_two', 'action' => 'index')) ?>">pembagian ruangan</a></li>
			</ol>
		<?php break; ?>
		<?php endswitch; ?>
		<?php else: ?>
			<header>
				<h1>To-Do List Seleksi Nasional</h1>
			</header>
			<ol>
				<li>Unduh <a href="<?php L(array('controller' => 'chapter', 'action' => 'participant_tab', 'chapter_id' => $id)) ?>">lembar tabulasi standar</a>, isi dengan hasil tabulasi chapter dan kirimkan ke <a href="mailto:sari.tjakra@afs.org">sari.tjakra@afs.org</a>.</li>
				<li>Unggah <a href="<?php L(array('controller' => 'selection_three_announcement', 'action' => 'announce')) ?>">daftar kelulusan Seleksi Tingkat Chapter</a></li>
			</ol>
		<?php endif; ?>
		</article>
		<?php endif; ?>
		<article>
			<header>
				<h1>Pengelolaan <?php echo $this->can_register() ? 'Pendaftar' : 'Peserta' ?></h1>
			</header>
			<form class="quick-view" action="<?php L(array('controller' => 'applicant', 'action' => 'view')) ?>">
				<p>
					<input type="text" class="medium-short" value="YBA/YP13-14/<?php if (!$chapter->is_national_office()) echo $chapter->chapter_code . '/' ?>" name="test_id" placeholder="Nomor Peserta">
					<button type="submit">Buka</button>
				</p>
			</form>
			<form class="quick-view" action="<?php L(array('controller' => 'applicant', 'action' => 'index')) ?>">
				<p>
					<input type="text" class="medium-short" value="" name="name" placeholder="Nama Peserta">
					<input type="text" class="medium-short" value="" name="school_name" placeholder="Asal Sekolah">
					<button type="submit">Cari</button>
				</p>
			</form>
			<form class="quick-view" action="<?php L(array('controller' => 'applicant', 'action' => 'view')) ?>">
				<p>
					<input type="text" class="medium-short" value="" name="username" placeholder="Username">
					<button type="submit">Lacak</button>
				</p>
			</form>
			<table class="summary applicants">
				<?php
				// foreach ($na as $a):
				foreach (array() as $a):
				?>
				<tr>
					<td class="field"><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $a->id)) ?>"><?php echo $a->sanitized_full_name ?></a></td>
					<th class="label"><?php echo $a->get_short_test_id() ?></th>
				</tr>
				<?php endforeach; ?>
			</table>
			<p class="more"><a href="<?php L(array('controller' => 'applicant', 'action' => 'index', 'chapter_id' => $id)) ?>">Lihat daftar selengkapnya</a></p>	
		</article>
	</section>
	<section class="secondary">
		<?php if ($this->user->capable_of('national_admin')): ?>
		<article class="chapter-info">
			<header>
				<h1>Kantor Nasional</h1>
			</header>
			<?php if ($this->can_register()): ?>
			<form action="<?php L(array('action' => 'migrate_applicants')) ?>" method="POST">
				<p>
					<button type="submit">Migrasi data pendaftar</button><br>
					<span class="instruction">Dengan melakukan migrasi pendaftar, pendaftaran ditutup dan seluruh pendaftar yang telah melakukan finalisasi akan dipindahkan ke daftar peserta.</span>
				</p>
			</form>
			<?php else: ?>
			<dl>
				<?php
					$dates = array(
						'Seleksi Tahap Pertama' => 'selection_one_date',
						'Seleksi Tahap Kedua' => 'selection_two_date',
						'Seleksi Tahap Ketiga' => 'selection_three_date',
					);
					foreach ($dates as $event => $pref) {
						$date = date_create(Helium::conf($pref))->format('j F Y');
						echo "<dt>$event</dt><dd>$date</dd>\n";
					}
				?>
			</dl>
			<?php endif; ?>
		</article>
		<?php else: ?>
		<article class="chapter-info">
			<header>
				<h1>Informasi Chapter</h1>
			</header>
			<table class="summary">
				<tr>
					<th class="label">Alamat</th>
					<td class="field"><?php echo nl2br($chapter_address) ?></td>
				</tr>
				<tr>
					<th class="label">Jangkauan</th>
					<td class="field"><?php echo nl2br($chapter_area) ?></td>
				</tr>
				<?php if ($contact_person_name): ?>
				<tr>
					<th class="label">Narahubung</th>
					<td class="field"><?php
						echo nl2br($contact_person_name);
						if ($contact_person_phone)
							echo '<br>' . $contact_person_phone	?></td>
				</tr>
				<?php endif; ?>
				<?php if ($facebook_url || $twitter_username): ?>
				<tr>
					<th class="label">Jejaring Sosial</th>
					<td class="field"><?php
						if ($facebook_url)
							printf('<a href="%s">Facebook</a>', $facebook_url);
						if ($facebook_url && $twitter_username)
							echo '<br>';
						if ($twitter_username)
							printf('<a href="http://twitter.com/%s">@%1$s</a>', $twitter_username);
					?>
				</tr>
				<?php endif; ?>
				<?php if ($site_url): ?>
				<tr>
					<th class="label">Situs web</th>
					<td class="field"><?php
						if ($site_url)
							printf('<a href="%s">%1$s</a>', $site_url);
					?>
				</tr>
				<?php endif; ?>
				<?php if ($chapter_email): ?>
				<tr>
					<th class="label">Alamat Surel</th>
					<td class="field"><?php
						if ($chapter_email)
							printf('<a href="mailto:%s">%1$s</a>', $chapter_email);
					?>
				</tr>
				<?php endif; ?>
			</table>
			<p class="edit"><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit', 'id' => $id)) ?>">Edit informasi chapter</a></p>
		</article>
		<?php endif;?>
		<!-- article class="users">
			<header>
				<h1>Akun Pengguna</h1>
			</header>
			<p class="summary">
				Akun volunteer <?php if (!$national) echo 'Chapter '; echo $chapter_name ?> yang terdaftar:<br>
				<?php foreach ($volunteers as $v): ?>
				<a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $v->id)) ?>"><?php echo $v->username ?></a>
				
				<?php endforeach; ?>
			</p>
			<p class="more"><a href="<?php L(array('controller' => 'user', 'action' => 'index')) ?>">Lihat daftar selengkapnya</a></p>
			<p class="more"><a href="<?php L(array('controller' => 'auth', 'action' => 'login')) ?>">Masuk sebagai pengguna lain</a></p>
			<p class="more"><a href="<?php L(array('controller' => 'user', 'action' => 'create')) ?>">Tambahkan akun pengguna baru</a></p>
		</article -->
	</section>
	<!--
	<section class="tertiary">
		<article class="event-calendar">
			<header>
				<h1>Jadwal Kegiatan</h1>
			</header>
			<dl>
				<dt class="current">Pendaftaran</dt>
				<dd class="current">21 Jan - 22 Mar</dd>
				<dt>Seleksi Tahap Pertama</dt>
				<dd>27 Mar</dd>
				<dt>Seleksi Tahap Kedua</dt>
				<dd>19 Apr</dd>
			</dl>
		</article>
	</section>
	-->
</div>
<?php $this->footer() ?>
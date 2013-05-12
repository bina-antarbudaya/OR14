<?php $this->print_header('Pengumuman Hasil Seleksi'); ?>
<?php $m = array(1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga') ?>

<div class="results">

	<header class="page-header">
		<h1>Pengumuman Hasil Seleksi</h1>
	</header>

	<?php if (!$applicant): ?>
	<div class="alert alert-error">
		Peserta tidak ditemukan.
	</div>

	<?php else: ?>
	<div class="row results-row">
		<div class="span3">
			<img src="<?php echo $applicant->picture->get_cropped_url() ?>">
		</div>
		<div class="span9">
			<header class="applicant-header">
				<p class="test-id"><?php echo $applicant->test_id ?></h1>
				<p class="name"><?php echo $applicant->sanitized_full_name ?></p>
			</header>
			<?php if ($selection_to_announce == 0): ?>
			<section class="result waiting">
				<header>
					<h1>Sabar...</h1>
				</header>
				<?php if ($next_announcement_wave_date): ?>
				<p>Pengumuman hasil Seleksi Tahap <?php echo $m[$next_selection] ?> berikutnya akan diumumkan pada tanggal <?php echo $next_announcement_wave_date->format('j F Y') ?>.</p>
				<?php else: ?>
				<p>Hasil Seleksi Tahap <?php echo $m[$next_selection] ?> belum diumumkan.</p>
				<?php endif; ?>
			</section>
			<?php elseif ($selection_result): ?>
			<section class="result yes">	
				<header>
					<h1>Selamat :)</h1>
				</header>
				<?php if ($selection_to_announce < 3): ?>
				<p>Adik lulus Seleksi Tahap <?php echo $m[$selection_to_announce] ?> Bina Antarbudaya.</p>
				<?php else: ?>
				<p>Adik telah lulus seluruh rangkaian seleksi tingkat chapter! Selanjutnya, Adik akan dijadikan finalis dari <?php echo $applicant->chapter->get_title(); ?> untuk melanjutkan seleksi ke Tingkat Nasional. Tunggu pengumuman selanjutnya.</p>
				<?php endif; ?>
			</section>
			<?php
			switch ($selection_to_announce):
			case 1:
			?>
			<div class="rereg-notice">
				<div class="row">
					<div class="span5">
						<p><span class="label label-important">Penting!</span> Sebelum melanjutkan ke seleksi tahap selanjutnya, Adik harus melakukan pendaftaran ulang di <strong><?php echo $applicant->chapter->get_title() ?></strong>.
						<?php if ($applicant->participant->selection_two_batch->reregistration_start_date != $applicant->participant->selection_two_batch->reregistration_finish_date): ?>
						Pendaftaran ulang dilakukan antara tanggal <strong><?php echo $applicant->participant->selection_two_batch->reregistration_start_date->format('j F Y') ?></strong> dan <strong><?php echo $applicant->participant->selection_two_batch->reregistration_finish_date->format('j F Y') ?></strong>.
						<?php elseif ($applicant->participant->selection_two_batch->reregistration_start_date == $applicant->participant->selection_two_batch->reregistration_finish_date && $applicant->participant->selection_two_batch->reregistration_start_date != '0000-00-00 00:00:00'): ?>
							Pendaftaran ulang dilakukan pada tanggal <strong><?php echo $applicant->participant->selection_two_batch->reregistration_start_date->format('j F Y') ?></strong>.
						<?php endif; ?>
						</p>

						<?php
						$c = $applicant->chapter;
						if ($c->contact_person_phone): ?>
						<p>Untuk informasi selengkapnya, silakan <?php if ($cs = $c->site_url) : ?>buka <a href="<?php echo $cs ?>">situs <?php echo $c->get_title(); ?></a> atau<?php endif; ?> hubungi<?php if ($cp = $c->contact_person_name) echo '<strong> Kak ' . $cp . '</strong> di '; echo "<strong>{$c->contact_person_phone}</strong>" ?>.</p>
						<?php endif; ?>
					</div>
					<div class="span4">
						<p>
							<a class="btn btn-block" href="<?php L(array('action' => 'transcript')) ?>" class="download-link"><i class="icon-download-alt"></i> Unduh Transkrip Formulir Pendaftaran</a>
							<a class="btn btn-block" href="<?php L(array('action' => 'file', 'file' => 'recommendation_letters')) ?>" class="download-link"><i class="icon-download-alt"></i> Unduh Template Surat Rekomendasi</a>
						</p>

						<?php if (!$this->auth->is_logged_in()): ?><p class="text-error login-notice">Adik perlu melakukan login untuk mengunduh transkrip formulir pendaftaran. Username Adik adalah <strong><?php echo $applicant->user->username ?></strong>.</p><?php endif; ?>
					</div>
				
			</div>
			<?php
			default:
			?>
			<?php endswitch; ?>
			<?php else: ?>
			<section class="result no">
				<header>
					<h1>Maaf :(</h1>
				</header>
				<p>Adik tidak lulus Seleksi Tahap <?php echo $m[$selection_to_announce] ?> Bina Antarbudaya.</p>
			</section>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php $this->print_footer() ?>

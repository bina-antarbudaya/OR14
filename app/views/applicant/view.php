<?php $this->header(); ?>
<header class="page-title">
	<hgroup>
		<?php $ch = ($applicant->chapter) ? $applicant->chapter : $this->session->user->chapter ?>
		<h1><a href="<?php L(array('controller' => 'chapter', 'action' => 'view', 'chapter_code' => $ch->chapter_code)) ?>"><?php echo $ch->get_title() ?></a></h1>
		<h2>Pengelolaan <?php echo $applicant->confirmed ? 'Peserta' : 'Pendaftar' ?></h2>
	</hgroup>
</header>
<nav class="actions-nav">
	<ul>
		<li><a href="<?php L($back_to) ?>">Kembali ke daftar</a></li>
	</ul>
</nav>

<div class="container">
	<?php if ($error): ?>
	<div class="message error">
		<header>Pendaftar tidak ditemukan</header>
		<p>Pendaftar yang dimaksud tidak ditemukan.</p>
	</div>
	<?php else: ?>
	
	<header class="applicant-header">
		<p class="applicant-test-id"><?php echo $applicant->finalized ? $applicant->test_id : 'Chapter ' . $applicant->chapter->chapter_name ?></h1>
		<h1 class="applicant-name"><?php echo $applicant->sanitized_full_name ?>&nbsp;</h1>
	</header>

	<div class="picture-container">
		<?php if ($picture): ?>
		<img src="<?php echo $picture->get_cropped_url(); ?>" width="300" height="400">
		<?php endif; ?>
	</div>
	
	<section class="form-preview">
		<h2>Tentang <?php echo $applicant->sanitized_full_name ?></h2>
		<table>
			<?php if ($applicant->finalized): ?>
			<tr>
				<td class="label">Chapter</td>
				<td class="field"><?php echo $applicant->chapter->chapter_name ?></td>
			</tr>
			
			<?php endif; ?>
			<tr>
				<td class="label">Asal Sekolah</td>
				<td class="field"><?php echo $applicant->sanitized_high_school_name ?></td>
			</tr>
			<tr>
				<td class="label">Tempat, Tgl Lahir</td>
				<td class="field"><?php echo $applicant->place_of_birth ? $applicant->place_of_birth . ', ' : ''; echo $applicant->date_of_birth->format('j F Y') ?></td>
			</tr>
			<tr>
				<td class="label">Alamat Surel</td>
				<td class="field"><?php echo $applicant->applicant_email ?></td>
			</tr>
			<tr>
				<td class="label">No. Ponsel</td>
				<td class="field"><?php echo $applicant->applicant_mobilephone ?></td>
			</tr>
			<tr>
				<td class="label">Alamat Rumah</td>
				<td class="field">
					<?php echo nl2br($applicant->applicant_address_street) ?>
					<br>
					<?php
					echo $applicant->applicant_address_city;
					echo $applicant->applicant_address_postcode ? ' ' . $applicant->applicant_address_postcode : '';
					echo $applicant->applicant_address_city ? ', ' . $applicant->applicant_address_province : $applicant->applicant_address_province; ?>
				</td>
			</tr>
		</table>
		<p class="more"><a href="<?php L(array('controller' => 'applicant', 'action' => 'details', 'id' => $applicant->id)) ?>">Lihat formulir selengkapnya</a></p>
		<?php if ($can_edit): ?><p class="edit"><a href="<?php L(array('controller' => 'applicant', 'action' => 'form', 'id' => $applicant->id)) ?>">Edit formulir</a></p><?php endif; ?>
	</section>

	<?php if ($this->can_register()): ?>
	<section class="application-status">
		<?php
		$f = $applicant->finalized;
		$c = $applicant->confirmed;
		?>
		<table>
			<tr>
				<td class="label">Status Pendaftaran</td>
				<td class="field"><strong><?php
				if ($f && $c)
					echo 'Sudah konfirmasi';
				elseif ($f && !$c)
					echo $applicant->is_expired() ? 'Harap cek keberadaan berkas' : 'Belum konfirmasi';
				elseif (!$f)
					echo $applicant->is_expired() ? 'Kadaluarsa' : 'Belum finalisasi';
				?></strong></td>
			</tr>
			<?php if (!$c): ?>
			<tr>
				<td class="label">Batas Pendaftaran</td>
				<td class="field"><?php echo $applicant->expires_on->format('j F Y') ?></td>
			</tr>
			<?php endif; ?>
			<?php if ($f || ($this->user->capable_of('national_admin') && $applicant->local_id)): ?>
			<tr>
				<td class="label">Tanda Peserta</td>
				<td class="field"><a href="<?php L(array('controller' => 'applicant', 'action' => 'card', 'id' => $applicant->id)) ?>">Cetak</a></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td class="label">Nama Pengguna</td>
				<td class="field"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>"><?php echo $applicant->user->username ?></a></td>
			</tr>
		</table>
		<?php
		if ($f && $c):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="unfinalize-button">Batalkan konfirmasi</button>
			</p>
		</form>
		<?php
		elseif ($f && !$c):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="1">
				<button type="submit" class="confirm-button">Konfirmasi pendaftaran</button>
				<br>
				<span class="instruction">Lakukan konfirmasi hanya jika <?php echo $applicant->sanitized_full_name ?> telah melengkapi seluruh persyaratan pendaftaran.</span>
			</p>
		</form>
		<?php if ($this->user->capable_of('chapter_admin')): ?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="0">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="unfinalize-button">Batalkan finalisasi</button>
				<br>
				<span class="instruction">Pembatalan finalisasi dilakukan hanya jika <?php echo $applicant->sanitized_full_name ?> salah mengisi formulir pendaftarannya.</span>
			</p>
		</form>
		<?php endif; ?>

		<?php
		elseif (!$f && $c):
			// Anomaly. Let's fix it while we can.
			$applicant->confirmed = false;
			$applicant->save();
		elseif ($this->user->capable_of('chapter_admin')):
			if ($applicant->validate() && !$applicant->is_expired()):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="confirm-button">Finalisasi</button>
				<br>
				<span class="instruction">Formulir pendaftaran <?php echo $applicant->sanitized_full_name ?> telah lengkap.</span>
			</p>
		</form>
		<?php
			elseif ($applicant->validate() && $applicant->is_expired()):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="confirm-button">Finalisasi</button>
				<br>
				<span class="instruction"><?php echo $applicant->sanitized_full_name ?> telah melewati masa pendaftaran.</span>
			</p>
		</form>
		<?php
			else:
				if ($applicant->is_expired() && !$applicant->local_id && $this->user->capable_of('national_admin')): ?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="0">
				<input type="hidden" name="confirmed" value="0">
				<input type="hidden" name="force_finalize" value="1">
				<button type="submit" class="confirm-button">Finalisasi Paksa</button>
				<br>
				<span class="instruction"><strong>
					Tindakan ini dapat mengganggu integritas data
					dan hanya dapat dilakukan oleh administrator nasional.
				</strong></span>
			</p>
		</form>
			<?php
				endif;
		?>
		<p>Siswa belum dapat melakukan finalisasi karena:</p>
		<ul class="validation-errors">
			<?php
			foreach ($applicant->validation_errors as $error) {
				switch ($error) {
				case 'incomplete':
			?>
			<li>Formulir tidak lengkap.</li>
			<ul class="incomplete-fields">
				<?php foreach ($applicant->incomplete_fields as $fd) echo '<li>' . $fd . '</li>'; ?>
			</ul>
				<?php
					break;
				case 'picture':
					echo '<li>Belum mengunggah foto.</li>';
				case 'program':
					echo '<li>Belum memilih program.</li>';
				default:		?>
			<li><?php echo $error; ?></li>
			<?php } } ?>
		</ul>
		<?php
		endif; endif;
		?>
	</section>
	<?php else: ?>
	<section class="application-status">
		<table>
			<?php if ($applicant->participant instanceof HeliumRecord): ?>
			<tr>
				<td class="label">Nama Pengguna</td>
				<td class="field"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>"><?php echo $applicant->user->username ?></a></td>
			</tr>
			<tr>
				<td class="label">Transkrip</td>
				<td class="field"><a href="<?php L(array('controller' => 'applicant', 'action' => 'transcript', 'id' => $applicant->id)) ?>">Unduh</a></td>
			</tr>
			<tr>
				<td class="label">Hasil Seleksi 1</td>
				<td class="field"><strong><?php
					if ($applicant->participant->selection_results(1) === null)
						echo 'Belum diumumkan';
					elseif ($applicant->participant->selection_results(1) === false)
						echo 'Tidak lulus';
					else
						echo 'Lulus';
				?></strong></td>
			</tr>
			<?php if ($applicant->participant->passed_selection_one): ?>
			<tr>
				<td class="label">Hasil Seleksi 2</td>
				<td class="field"><strong><?php
					if ($applicant->participant->selection_results(2) === null)
						echo 'Belum diumumkan';
					elseif ($applicant->participant->selection_results(2) === false)
						echo 'Tidak lulus';
					else
						echo 'Lulus';
				?></strong></td>
			</tr>
			<?php if ($applicant->participant->passed_selection_two): ?>
			<tr>
				<td class="label">Hasil Seleksi 3</td>
				<td class="field"><strong><?php
					if ($applicant->participant->selection_results(3) === null)
						echo 'Belum diumumkan';
					elseif ($applicant->participant->selection_results(3) === false)
						echo 'Tidak lulus';
					else
						echo 'Lulus';
				?></strong></td>
			</tr>
			<?php endif; ?>
			<?php endif;?>
			<?php else: ?>
			<tr>
				<td class="label">Status Pendaftaran</td>
				<td class="field"><strong>Gagal mendaftar</strong></td>
			</tr>
			<?php endif; ?>
		</table>
	</section>
	<?php endif; ?>

	<?php endif; ?>
</div>
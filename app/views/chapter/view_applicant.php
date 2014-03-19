<?php $this->print_header('Ringkasan Profil Peserta') ?>

<header class="page-header combo">
	<h1>Pengelolaan Peserta</h1>
</header>

<div class="quick-links">
	<ul class="nav nav-tabs">
		<li><a href="<?php L($back_to) ?>"><i class="icon-chevron-left"></i> Kembali ke daftar</a></li>
		<li class="active"><a><?php echo $applicant->sanitized_full_name ? $applicant->sanitized_full_name : 'Ringkasan Profil Peserta' ?></a></li>
	</ul>
</div>

<?php if ($error): ?>
<div class="alert alert-error">Peserta tidak ditemukan</div>
<?php else: ?>
<h3 class="applicant-header"><?php
		if ($applicant->local_id) {
			echo "<span class=\"test-id\">$applicant->test_id</span> ";
		}

		echo $applicant->sanitized_full_name;
	?></h3>
<div class="row">
	<div class="span3">
		<?php if ($picture): ?>
		<img src="<?php echo $picture->get_cropped_url(); ?>" class="img-polaroid">
		<?php else: ?>
		<div class="no-photo img-polaroid">
			Belum ada foto
		</div>
		<?php endif; ?>
	</div>
	<div class="span5">
		<table class="table">
			<tr>
				<th>Chapter</th>
				<td class="field"><?php echo $applicant->chapter->chapter_name ?></td>
			</tr>
			<tr>
				<th>Asal Sekolah</th>
				<td class="field"><a href="<?php L(array('controller' => 'chapter', 'action' => 'applicants', 'school_name' => $applicant->sanitized_high_school_name)) ?>"><?php echo $applicant->sanitized_high_school_name ?></a></td>
			</tr>
			<tr>
				<th>Tempat, Tanggal Lahir</th>
				<td class="field"><?php echo $applicant->place_of_birth ? $applicant->place_of_birth . ', ' : ''; echo $applicant->date_of_birth->format('j F Y') ?></td>
			</tr>
			<tr>
				<th>Alamat E-mail</th>
				<td class="field"><?php echo $applicant->applicant_email ?></td>
			</tr>
			<tr>
				<th>Nomor Ponsel</th>
				<td class="field"><?php echo $applicant->applicant_mobilephone ?></td>
			</tr>
			<tr>
				<th>Alamat Rumah</th>
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
		<a href="<?php L(array('controller' => 'applicant', 'action' => 'form', 'id' => $applicant->id)) ?>" class="btn btn-block">Edit formulir selengkapnya</a>
		<a href="<?php L(array('controller' => 'applicant', 'action' => 'transcript', 'id' => $applicant->id)) ?>" class="btn btn-block">Transkrip formulir</a>
	</div>
	<div class="span4">
		<?php if($this->can_register()): ?>
		<?php
		$f = $applicant->finalized;
		$c = $applicant->confirmed;
		$e = $applicant->is_expired();
		?>

		<?php
		if ($f && $c):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="btn btn-danger btn-block btn-large">Batalkan verifikasi berkas</button>
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
				<button type="submit" class="btn btn-success btn-block btn-large">Verifikasi berkas pendaftaran</button>
				<span class="help-block">Lakukan verifikasi berkas jika <?php echo $applicant->sanitized_full_name ?> telah melengkapi seluruh persyaratan pendaftaran.</span>
			</p>
		</form>
		<?php if ($this->user->capable_of('chapter_admin')): ?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="0">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="btn btn-danger btn-block">Batalkan finalisasi</button>
				<span class="help-block">Pembatalan finalisasi dilakukan hanya jika <?php echo $applicant->sanitized_full_name ?> salah mengisi formulir pendaftarannya.</span>
			</p>
		</form>
		<?php endif; ?>

		<?php
		elseif (!$f && $c):
			// Anomaly. Let's fix it while we can.
			$applicant->confirmed = false;
			$applicant->save();
		else:
			if ($applicant->validate() && !$applicant->is_expired()):
		?>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant->id)) ?>" method="POST" class="confirm-form">
			<p>
				<input type="hidden" name="id" value="<?php echo $applicant->id ?>">
				<input type="hidden" name="finalized" value="1">
				<input type="hidden" name="confirmed" value="0">
				<button type="submit" class="btn btn-success btn-block btn-large">Finalisasi atas nama peserta</button>				
				<span class="help-block">Formulir pendaftaran <?php echo $applicant->sanitized_full_name ?> telah lengkap.</span>
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
				<button type="submit" class="btn btn-warning btn-block btn-large">Finalisasi atas nama peserta</button>
				<span class="help-block"><?php echo $applicant->sanitized_full_name ?> telah melewati batas&nbsp;waktu&nbsp;pendaftarannya.</span>
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
				<button type="submit" class="btn btn-warning btn-block btn-large">Finalisasi Paksa</button>
				<span class="help-block"><strong>
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
			<li>Formulir tidak lengkap. Kolom-kolom yang kosong:</li>
			<ul class="incomplete-fields">
				<?php foreach ($applicant->incomplete_fields as $fd) echo '<li>' . $fd . '</li>'; ?>
			</ul>
				<?php
					break;
				case 'picture':
					echo '<li>Belum mengunggah foto.</li>';
					break;
				case 'program':
					echo '<li>Belum memilih program.</li>';
					break;
				case 'birth_date':
					echo '<li>Tanggal lahir belum diisi atau tidak sesuai dengan batasan umur AFS.</li>';
					break;
				default:		?>
				<li><?php echo $error; ?></li>
			<?php } } ?>
		</ul>
		<?php
		endif; endif;
		?>

		<table class="table">
			<tr>
				<th>Status Pendaftaran</th>
				<td class="field"><strong><?php
				if ($f && $c)
					echo '<span class="text-success">Sudah verifikasi berkas</span>';
				elseif ($f && !$c) {
					if ($e)
						echo 'Belum verifikasi berkas namun sudah kadaluarsa';
					else
						echo 'Belum verifikasi berkas';
				}
				elseif (!$f) {
					if ($e)
						echo '<span class="text-error">Kadaluarsa</span>';
					else
						echo '<span class="text-primary">Masih mengisi formulir</span>';
				}
				?></strong></td>
			</tr>
			<?php if (!$c): ?>
			<tr>
				<th>Batas Waktu Pendaftaran</th>
				<td class="field"><?php echo $applicant->expires_on->format('j F Y') ?></td>
			</tr>
			<?php endif; ?>
			<?php if ($f || ($this->user->capable_of('national_admin') && $applicant->local_id)): ?>
			<tr>
				<td colspan="2"><a href="<?php L(array('controller' => 'applicant', 'action' => 'card', 'id' => $applicant->id)) ?>" class="btn btn-block"><i class="icon-download-alt"></i> Unduh tanda peserta</a></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>Username</th>
				<td class="field"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>"><?php echo $applicant->user->username ?></a></td>
			</tr>
			<tr>
				<td colspan="2"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>" class="btn btn-block">Reset password peserta</a></td>
			</tr>
		</table>

		<?php else: ?>

		<table class="table">
			<?php if ($applicant->participant instanceof HeliumRecord): ?>
			<tr>
				<th>Username</th>
				<td class="field"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>"><?php echo $applicant->user->username ?></a></td>
			</tr>
			<tr>
				<td colspan="2"><a href="<?php L(array('controller' => 'user', 'action' => 'edit', 'id' => $applicant->user_id)) ?>" class="btn btn-block">Reset password peserta</a></td>
			</tr>
			<tr>
				<td colspan="2"><a href="<?php L(array('controller' => 'applicant', 'action' => 'card', 'id' => $applicant->id)) ?>" class="btn btn-block"><i class="icon-download-alt"></i> Unduh tanda peserta</a></td>
			</tr>
			<tr>
				<td colspan="2"><a href="<?php L(array('controller' => 'applicant', 'action' => 'transcript', 'id' => $applicant->id)) ?>" class="btn btn-block"><i class="icon-download-alt"></i> Unduh transkrip formulir</a></td>
			</tr>
			<tr>
				<th>Hasil Seleksi 1</th>
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
				<th>Hasil Seleksi 2</th>
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
				<th>Hasil Seleksi 3</th>
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
				<th>Status Pendaftaran</th>
				<td class="field"><strong>Gagal mendaftar</strong></td>
			</tr>
			<?php endif; ?>
		</table>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

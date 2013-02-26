<?php $this->print_header('Pengaturan Akun'); ?>
<header class="page-header">
	<h1>Pengaturan Akun</h1>
</header>
	<?php if ($error): ?>
	<div class="alert alert-error">
		<strong>Pengubahan password gagal</strong>
		<?php
		
		switch ($error) {
			case 'old_password_incorrect':
				echo 'Password lama yang Anda masukkan salah.';
				break;
			case 'password_mismatch':
				echo 'Password tidak cocok.';
				break;
			case 'password_too_short':
				echo 'Password yang Anda pilih terlalu pendek.';
				break;
		}
		
		?>
	</div>
	<?php elseif ($success): ?>
	<div class="message">
		<strong>Pengubahan password berhasil</strong>
		<p>Gunakan password yang baru untuk memasuki situs ini.</p>
	</div>
	<?php endif; ?>

	<!-- <h4>Ubah password</h4> -->

	<form action="<?php L(array('controller' => 'user', 'action' => 'prefs')) ?>" method="POST" class="form form-horizontal">
		<div class="control-group">
			<label for="old_password" class="control-label">Password lama</label>
			<div class="controls">
				<?php $form->password('old_password', 'medium', null, true) ?>
			</div>
		</div>
		<div class="control-group">
			<label for="password" class="control-label">Password baru</label>
			<div class="controls">
				<?php $form->password('password', 'medium', null, true) ?>
				<span class="help-inline">Password terdiri atas paling sedikit delapan karakter.</span>
			</div>
		</div>
		<div class="control-group">
			<label for="retype_password" class="control-label">Ulangi password baru</label>
			<div class="controls">
				<?php $form->password('retype_password', 'medium', null, true) ?>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn">Simpan</button>
		</div>
	</form>
</div>
<?php $this->print_footer(); ?>
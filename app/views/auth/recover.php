<?php $this->print_header('Pemulihan akun') ?>
<header class="page-header">
	<h1>Pemulihan Akun</h1>
</header>

<?php if ($error == 'not_found' || $error == 'expired'): ?>
<div class="alert alert-error">
	<strong>Pemulihan akun gagal.</strong>
	<?php echo $error == 'not_found' ? 'Permohonan pemulihan akun tidak ditemukan.' : 'Permohonan pemulihan akun telah kadaluarsa.' ?>
</div>
<?php else: ?>

<?php if ($error == 'incomplete' || $error == 'mismatch'): ?>
<div class="alert alert-error">
	<strong>Pemulihan akun gagal.</strong>
	<?php echo $error == 'incomplete' ? 'Isian tidak lengkap.' : 'Password tidak cocok.' ?>
</div>
<?php endif; ?>

<form action="<?php L(array('action' => 'recover')) ?>" method="POST" class="form form-horizontal">
	<div class="control-group">
		<label class="control-label" for="password">Password baru</div>
		<div class="controls"><input type="password" name="password" class="medium" id="password" required>
			<br><span class="instruction">Terdiri atas paling sedikit delapan karakter.</span></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="retype_password">Ulangi password baru</div>
		<div class="controls"><input type="password" name="retype_password" id="retype_password" class="medium" required></div>
	</div>	
	<div class="form-actions">
		<input name="token" type="hidden" value="<?php echo $key->token ?>"><button type="submit">Ubah Password</button>
	</div>
</form>
<?php endif; ?>

<?php $this->print_footer() ?>
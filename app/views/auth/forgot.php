<?php $this->print_header('Pemulihan akun'); ?>
<header class="page-header">
	<h1>Pemulihan akun</h1>
</header>
	<?php if ($success): ?>
	<div class="alert alert-success">
		Sebuah surel telah dikirimkan ke alamat yang tertera pada formulir pendaftaran Adik. Petunjuk selanjutnya terdapat pada alamat tersebut.
	</div>
	<?php else: ?>
	<?php if ($error): ?>
	<div class="alert alert-error">
		<strong>Pemulihan Akun gagal</strong>
		<?php switch ($error) {
			case 'not_found':
				echo 'Pengguna tidak ditemukan';
				break;
			case 'no_email':
				echo 'Alamat surel tidak ditemukan';
				break;
			case 'send_fail':
				echo 'Pengiriman surel gagal.';
				break;
			case 'recaptcha':
				echo 'Isian reCAPTCHA tidak sesuai.';
				break;
			default:
				echo $error;
		} ?>
	</div>
	<?php endif; ?>
	<p>Isilah formulir di bawah ini untuk memulihkan password Adik.</p> 
	<form action="<?php L(array('action' => 'forgot')) ?>" method="POST" class="form">
		<div class="control-group">
			<label for="identifier" class="control-label">Nomor peserta atau username</label>
			<div class="controls">
				<input type="text" class="medium" name="identifier" value="<?php echo $_POST['identifier'] ?>" autofocus placeholder="Nomor peserta atau username">
			</div>
		</div>
		<div class="control-group">
			<span class="control-label">reCAPTCHA</span>
		<script>var RecaptchaOptions = { theme : 'clean' };</script>
		<?php echo $recaptcha->get_html(); ?>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn">Lanjutkan</button>
		</div>
	</form>
	<?php endif; ?>
<?php $this->footer(); ?>
<?php $this->print_header('Perpanjangan Akun'); ?>

<header class="page-header">
		<h1>Perpanjangan Akun</h1>
</header>
<div class="alert alert-error">
<?php
if ($error):
$error_messages = array('token_nonexistent' => 'PIN pendaftaran yang Adik masukkan salah.',
						'token_unavailable' => 'PIN pendaftaran yang Adik masukkan sudah terpakai.',
						'token_expired' => 'PIN pendaftaran yang Adik masukkan telah habis masa pakainya.',
						'incomplete' => 'Adik belum memasukkan PIN pendaftaran.',
						'recaptcha' => 'Isian reCAPTCHA tidak cocok.',
						'chapter_mismatch' => 'PIN pendaftaran yang Adik masukkan berasal dari chapter yang berbeda dengan chapter tempat Adik terdaftar.')

?>
	<strong>Perpanjangan akun gagal.</strong> <?php echo $error_messages[$error] ?>
<?php else: ?>
	<strong>Akun Adik telah kadaluarsa.</strong> Isilah formulir ini untuk mengaktifkan kembali akun Adik.
<?php endif; ?>
</div>

<section class="token-redemption">
	<form action="<?php L($this->params) ?>" method="POST" class="token-redemption-form">
		<?php if ($enable_recaptcha): ?>
		<p>
			<script>var RecaptchaOptions = { theme : 'clean' };</script>
			<?php echo $recaptcha->get_html(); ?>
		</p>
		<?php endif; ?>
		<div class="control-group">
			<label for="token" class="control-label">Untuk melanjutkan pendaftaran, masukkan enam belas huruf PIN pendaftaran Adik.</label>
			<div class="input-append">
				<input type="text" name="token" id="token" width="16" maxlength="16" autofocus required placeholder="PIN Pendaftaran">
				<button type="submit" class="btn btn-large btn-primary">Lanjut</button>
			</div>
		</p>
	</form>
</section>
<?php $this->footer(); ?>
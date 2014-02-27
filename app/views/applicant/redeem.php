<?php $this->print_header('Pengaktifan PIN Pendaftaran'); ?>

<header class="page-header">
	<h2>Tahap 1 dari 4</h1>
	<h1>Pengaktifan PIN Pendaftaran</h1>
</header>

	<?php
	if ($error):
	$error_messages = array('token_nonexistent' => 'PIN pendaftaran yang Adik masukkan salah.',
							'token_unavailable' => 'PIN pendaftaran yang Adik masukkan sudah terpakai.',
							'token_expired' => 'PIN pendaftaran yang Adik masukkan telah habis masa pakainya.',
							'incomplete' => 'Adik belum memasukkan PIN pendaftaran.',
							'recaptcha' => 'Isian reCAPTCHA tidak cocok.')

	?>
	<div class="alert alert-error alert-block">
		<h4>Pengaktifan PIN pendaftaran gagal</h4>
		<p><?php echo $error_messages[$error] ?></p>
	</div>
	<?php endif; ?>

	<div class="row redeem-pin">
		<div class="span6 about-pins">
			<h4>Apa itu PIN Pendaftaran?</h4>
			<p>
				PIN pendaftaran adalah sebuah kode yang terdiri dari 16 huruf (tanpa angka, tanpa spasi)
				yang digunakan untuk mendaftar untuk seleksi pertukaran pelajar Bina Antarbudaya.
				Masing-masing PIN pendaftaran berlaku untuk sekali pakai dan memiliki batas waktu pendaftaran.
			</p>
			<p>
				PIN pendaftaran dapat Adik dapatkan di Chapter Bina Antarbudaya terdekat.
			</p>
		</div>
		<div class="span6 redeem-now">
			<h4>Saya sudah memiliki PIN Pendaftaran</h4>
			<form action="<?php L($this->params) ?>" method="POST" class="token-redemption-form">
				<?php if ($enable_recaptcha): ?>
				<p>
					<script>var RecaptchaOptions = { theme : 'clean' };</script>
					<?php echo $recaptcha->get_html(); ?>
				</p>
				<?php endif; ?>
				<div class="control-group">
					<label for="token" class="control-label">Untuk memulai pendaftaran, masukkan enam belas huruf PIN pendaftaran Adik.</label>
					<div class="input-append">
						<input type="text" name="token" id="token" width="16" maxlength="16" autofocus required placeholder="PIN Pendaftaran">
						<button type="submit" class="btn btn-large btn-success">Aktifkan</button>
					</div>
				</div>
			</form>
			<h4>Saya sudah pernah mengaktifkan PIN pendaftaran,<br>namun telah&nbsp;kadaluarsa</h4>
			<p>
				Untuk mengaktifkan kembali akun yang telah kadaluarsa, isilah formulir berikut:
			</p>
			<form action="<?php L(array('action' => 'reactivate')) ?>" class="form-inline" method="POST">
				<div class="control-group">
					<input type="text" name="username" required id="reactivate-username" class="span2" placeholder="Username">
					<input type="password" name="password" required id="reactivate-password" class="span2" placeholder="Password">
				</div>
				<div class="control-group">
					<input type="text" name="token" id="token" maxlength="16" class="span2" required placeholder="PIN pendaftaran baru">
					<button type="submit" class="btn">Aktifkan kembali</button>
				</div>
			</form>
		</div>
	</div>
<?php $this->print_footer(); ?>
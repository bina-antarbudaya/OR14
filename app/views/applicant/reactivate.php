<?php $this->header('Aktivasi PIN Pendaftaran'); $this->fx('staggered_load'); ?>

<header class="page-title">
		<h1>Perpanjangan Akun</h1>
</header>
<div class="container">
	<?php
	if ($error):
	$error_messages = array('token_nonexistent' => 'PIN pendaftaran yang Adik masukkan salah.',
							'token_unavailable' => 'PIN pendaftaran yang Adik masukkan sudah terpakai.',
							'token_expired' => 'PIN pendaftaran yang Adik masukkan telah habis masa pakainya.',
							'incomplete' => 'Adik belum memasukkan PIN pendaftaran.',
							'chapter_mismatch' => 'PIN pendaftaran yang Adik masukkan berbeda chapter dengan chapter mula-mula Adik.',
							'recaptcha' => 'Isian reCAPTCHA tidak cocok.')

	?>
	<div class="message error">
		<header>Pengaktifan PIN pendaftaran gagal</header>
		<p><?php echo $error_messages[$error] ?></p>
	</div>
	<?php endif; ?>

	<section class="token-entry">
		<form action="<?php L($this->params) ?>" method="POST" class="user-create-form">
			<?php if ($enable_recaptcha): ?>
			<p>
				<script>var RecaptchaOptions = { theme : 'clean' };</script>
				<?php echo $recaptcha->get_html(); ?>
			</p>
			<?php endif; ?>
			<p>
				<label for="token">Adik melewati tenggat waktu pendaftaran. Untuk melanjutkan pendaftaran, masukkan enam belas huruf PIN pendaftaran Adik</label>
				<span class="token-box">
					<input type="text" name="token" id="token" width="16" maxlength="16" autofocus required>
					<button type="submit">Lanjut</button>
				</span>
			</p>
		</form>
	</section>
</div>
<script>
	$('.token-box input').focus(function(){$(this).parent().addClass('focus')})
	$('.token-box input').blur(function(){$(this).parent().removeClass('focus')})
</script>
<?php $this->footer(); ?>
<?php $this->print_header('Pengaktifan PIN Pendaftaran'); ?>

<header class="page-header">
	<h2><span>Tahap </span>1</h1>
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

	<section class="token-redemption">
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
					<button type="submit" class="btn btn-large btn-primary">Lanjut</button>
				</div>
			</p>
		</form>
	</section>

	<div class="row">
		
		<section class="redemption-faqs span3">
			<header>
				<h3>FAQs</h3>
			</header>
			
			<h4>Apa itu PIN pendaftaran?</h4>
			<p>PIN pendaftaran adalah sebuah kode yang terdiri dari enam belas huruf (tanpa angka, tanpa spasi) yang digunakan untuk mendaftar untuk seleksi pertukaran pelajar Bina Antarbudaya.</p>
			
			<h4>Dari mana saya bisa mendapatkan PIN pendaftaran?</h4>
			<p>PIN pendaftaran dapat Adik dapatkan di Chapter-Chapter Bina Antarbudaya yang tersebar di seluruh Indonesia. Di bawah ini terdapat alamat-alamat Chapter tersebut.</p>
		</section>

		<section class="redemption-chapters span9">
			<header>
				<h3>Chapter-Chapter Bina Antarbudaya</h3>
			</header>
			<div class="row">
			<?php $i = 0; foreach ($chapters as $c): ?>
			<?php if (($i != 0) && ($i % 3 == 0)): ?>

			</div>
			
			<div class="row">
				
			<?php endif; ?>
				<div class="span3 chapter-item">
					<h4 class="chapter-name"><?php echo $c->chapter_name ?></h4>
					<p>
					<?php echo nl2br($c->chapter_address) ?><br>
						<a href="mailto:<?php echo $c->get_email() ?>?subject=Pendaftaran Seleksi"><?php echo $c->get_email() ?></a><br>
						<?php if ($u = $c->site_url) { ?><a href="<?php echo $u ?>"><?php echo $u ?></a><?php } ?>
					</p>
				</div>
			<?php $i++; endforeach; ?>
			</div>
		</section>
	</div>

<?php $this->print_footer(); ?>
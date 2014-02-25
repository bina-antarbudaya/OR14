<?php $this->print_header('Pemulihan Akun'); ?>
<header class="page-header">
	<h1>Pemulihan Akun</h1>
</header>
	<?php if ($success): ?>
	<div class="alert alert-success">
		Sebuah surel telah dikirimkan ke alamat yang tertera pada formulir pendaftaran Adik. Petunjuk selanjutnya terdapat pada alamat tersebut.
	</div>
	<?php else: ?>
	<?php if ($error): ?>
	<div class="alert alert-error">
		<strong>Pemulihan Akun gagal.</strong>
		<?php switch ($error) {
			case 'not_found':
				echo 'Pengguna tidak ditemukan.';
				break;
			case 'no_email':
				echo 'Alamat surel tidak ditemukan.';
				break;
			case 'send_fail':
				echo 'Pengiriman e-mail gagal.';
				break;
			case 'recaptcha':
				echo 'Isilah formulir sesuai petunjuk.';
				break;
			default:
				echo $error;
		} ?>
	</div>
	<?php endif; ?>
	<p class="lead">Formulir ini digunakan untuk memulihkan akun Adik jika Adik kehilangan password atau username Adik.</p>
	<form action="<?php L(array('action' => 'forgot')) ?>" method="POST" class="form form-horizontal">
		<div class="control-group">
			<label for="identifier" class="control-label">Nomor peserta atau username:</label>
			<div class="controls">
				<input type="text" class="medium" name="identifier" value="<?php echo $_POST['identifier'] ?>" autofocus placeholder="Nomor peserta atau username">
			</div>
		</div>
		<div class="control-group">
			<span class="control-label">Isilah dua kata berikut pada tempat yang disediakan:</span>
			<div class="controls">
				<script>var RecaptchaOptions = { theme : 'clean' };</script>
				<?php echo $recaptcha->get_html(); ?>
				<?php if ($error == 'recaptcha'): ?>
				<span class="text-error">Kata yang dimasukkan tidak sesuai dengan gambar. Cobalah sekali lagi.</span>
				<?php endif; ?>
				<span class="help-block">Isian ini guna memastikan bahwa Adik bukan robot yang dapat mengganggu sistem.</span>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Lanjutkan</button>
		</div>
	</form>
	<?php endif; ?>
<?php $this->footer(); ?>
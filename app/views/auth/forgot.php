<?php $this->header('Pemulihan akun'); ?>
<header class="page-title">
	<h1>Pemulihan akun</h1>
</header>
<div class="container">
	<?php if ($success): ?>
	<p>Sebuah surel telah dikirimkan ke alamat yang tertera pada formulir pendaftaran Adik. Petunjuk selanjutnya terdapat pada alamat tersebut.</p>
	<?php else: ?>
	<?php if ($error): ?>
	<div class="message error">
		<header>
			<h1>Pemulihan akun gagal</h1>
		</header>
		<p><?php switch ($error) {
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
		} ?></p>
	</div>
	<?php endif; ?>
	<p>Masukkan nomor peserta atau username Adik di bawah ini untuk memulihkan akun Adik.</p> 
	<form action="<?php L(array('action' => 'forgot')) ?>" method="POST">
		<table class="form-table">
			<tr>
				<td class="label"><label for="identifier">Nomor peserta atau username</label></td>
				<td class="field"><input type="text" class="medium" name="identifier" value="<?php echo $_POST['identifier'] ?>" autofocus></td>
			</tr>
			<tr>
				<td class="label">reCAPTCHA</td>
				<td class="field">
					<script>var RecaptchaOptions = { theme : 'clean' };</script>
					<?php echo $recaptcha->get_html(); ?>
				</td>
			</tr>
			<tr>
				<td class="label"></td>
				<td class="field"><button type="submit">Lanjutkan</button></td>
			</tr>
		</table>
	</form>
	<?php endif; ?>
</div>
<?php $this->footer(); ?>
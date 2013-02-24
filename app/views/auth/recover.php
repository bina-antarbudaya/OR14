<?php $this->header('Pemulihan akun') ?>
<header class="page-title">
	<h1>Pemulihan akun</h1>
</header>
<div class="container">
	<?php if ($error == 'not_found' || $error == 'expired'): ?>
	<div class="message error">
		<header>
			<h1>Pemulihan akun gagal</h1>
		</header>
		<p><?php echo $error == 'not_found' ? 'Permohonan pemulihan akun tidak ditemukan.' : 'Permohonan pemulihan akun telah kadaluarsa.' ?></p>
	</div>
	<?php else: ?>
	<?php if ($error == 'incomplete' || $error == 'mismatch'): ?>
	<div class="message error">
		<header>
			<h1>Pemulihan akun gagal</h1>
		</header>
		<p><?php echo $error == 'incomplete' ? 'Isian tidak lengkap.' : 'Password tidak cocok.' ?></p>
	</div>
	<?php endif; ?>
	<form action="<?php L(array('action' => 'recover')) ?>" method="POST">
		<table class="form-table">
			<tr>
				<td class="label">Password baru</td>
				<td class="field"><input type="password" name="password" class="medium" id="password" required>
					<br><span class="instruction">Terdiri atas paling sedikit delapan karakter.</span></td>
			</tr>
			<tr>
				<td class="label">Ulangi password baru</td>
				<td class="field"><input type="password" name="retype_password" id="retype_password" class="medium" required></td>
			</tr>	
			<tr>
				<td class="label"></td>
				<td class="field">
					<input name="token" type="hidden" value="<?php echo $key->token ?>"><button type="submit">Ubah Password</button>
				</td>
			</tr>
		</table>
	</form>
	<?php endif; ?>
</div>
<?php $this->footer() ?>
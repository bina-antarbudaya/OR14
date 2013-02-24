<?php $this->header() ?>
<div class="container">
	<?php if ($error): ?>
	<div class="message error">
		<header>
			<h1><?php switch ($error) { case 'not_found': echo 'Gelombang tidak ditemukan'; break; case 'unauthorized': echo 'Akses ditolak'; break; } ?></h1>
		</header>
		<p>Silakan mencoba kembali.</p>
	</div>
	<?php else: ?>
	<header>
		<h1>Pembatalan Gelombang Seleksi</h1>
	</header>
	<p>Dengan membatalkan gelombang seleksi ini, peserta yang dinyatakan lulus dalam gelombang seleksi tersebut akan dibatalkan pengumuman kelulusannya dan hanya dapat mengetahui kelulusannya setelah Kakak memasukkan kembali daftar kelulusan.</p>
	<form action="<?php L($this->params) ?>" method="POST">
		<p>
			<button type="submit">Saya mengerti. Lanjutkan.</button>
		</p>
	</form>
	<?php endif; ?>
</div>
<?php $this->footer() ?>
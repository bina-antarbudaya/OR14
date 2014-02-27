<?php

$error_messages = array('username_availability' => 'Nama pengguna yang Adik pilih tidak tersedia.',
						'username_format' => 'Pada nama pengguna yang Adik pilih terdapat karakter selain huruf, angka, dan garis bawah, atau panjangnya kurang dari tiga karakter.',
						'password' => 'Panjang sandi yang Adik pilih kurang dari delapan karakter.',
						'retype_password' => 'Kedua isian sandi Adik tidak saling cocok.',
						'email' => 'Alamat surel yang Adik masukkan tidak sah.',
						'incomplete' => 'Isian pada formulir tidak lengkap.',
						'recaptcha' => 'Isian reCAPTCHA tidak cocok.',
						'db_fail' => 'Cobalah sesaat lagi.')

?>
<?php $this->print_header('Buat Akun'); ?>

<header class="page-header">
	<h2>Tahap 2 dari 4</h2>
	<h1>Pembuatan Akun</h1>
</header>

<?php if ($error): ?>
<div class="alert alert-error alert-block">
	<h4>Pembuatan akun gagal</h4>
	<p><?php echo $error_messages[$error]; ?></p>
</div>
<?php else: ?>
<div class="alert alert-success">
	<strong>PIN Pendaftaran berhasil dimasukkan.</strong>
</div>
<?php endif; ?>

<div class="row">
	<section class="account-creation-intro span5">
		<header>
			<h3>Tentang Akun Bina Antarbudaya</h3>
		</header>

		<p>Untuk melanjutkan proses pendaftaran, Adik perlu membuat sebuah akun. Akun ini digunakan untuk mengakses formulir pendaftaran Adik dan menyimpannya. <strong>PIN Pendaftaran Adik tidak akan digunakan lagi.</strong></p>
		
		<p>
			Adik akan terdaftar pada Bina Antarbudaya Chapter <strong><?php echo $chapter_name ?></strong>, dan seluruh rangkaian seleksi yang Adik ikuti akan diselenggarakan oleh Chapter <?php echo $chapter_name ?>.
			<strong class="text-error">Apabila Adik memperoleh PIN pendaftaran selain dari Chapter <?php echo $chapter_name ?>, hubungi tempat Adik memperoleh PIN pendaftaran Adik.</strong>
		</p>
		
		<h5>Saya sudah pernah membuat akun, namun saya lupa password saya.</h5>
		<p>Jika Adik lupa password Adik, silakan kunjungi halaman <a href="<?php L(array('controller' => 'auth', 'action' => 'forgot')) ?>">pemulihan akun</a>.</p>

		<h5>Saya sudah pernah membuat akun, namun akun saya telah kadaluarsa.</h5>
		<p>Untuk mengaktifkan kembali akun Adik, isi formulir di bawah ini:</p>
		<form action="<?php L(array('action' => 'reactivate')) ?>" class="form-inline" method="POST">
			<input type="text" name="username" required id="reactivate-username" class="span2" placeholder="Username">
			<input type="password" name="password" required id="reactivate-password" class="span2" placeholder="Password">
			<input type="hidden" name="token" value="<?php echo $token ?>">
			<button type="submit" class="btn">Lanjut</button>
		</form>
	</section>
	<section class="account-creation-details span7">
		<header>
			<h3>Informasi Akun</h3>
		</header>
		
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'create')) ?>" method="POST" validate class="form form-horizontal">
			
			<div class="control-group">
				<label class="control-label" for="username">Username</label>
				<div class="controls">
					<input type="text" name="username" id="username" class="medium" value="<?php echo $this->session->flash('username'); ?>" autofocus required>
					<span class="help-block">Terdiri atas paling sedikit empat karakter, dan hanya boleh terdiri atas huruf, angka, garisbawah (_), tanda sambung (-). Tidak boleh mengandung spasi.</span>
				</div>
			</div>
			
			<?php /*
			<div class="control-group">
				<span class="control-label">PIN Pendaftaran</span>
				<div class="controls">
					<span class="input-large uneditable-input"><?php echo $token ?></span>
				</div>
			</div>
			*/ ?>
			
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="password" name="password" id="password" required>
					<span class="help-block">Terdiri atas paling sedikit delapan karakter. Demi keamanan Adik, gunakan password yang berbeda di setiap tempat.</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="retype_password">Ulangi Password</label>
				<div class="controls">
					<input type="password" name="retype_password" id="retype_password" required>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="email">Alamat E-mail</label>
				<div class="controls">
					<input type="email" name="email" id="email" required>
					<span class="help-block">
						Alamat e-mail ini akan digunakan untuk administrasi Adik sepanjang seleksi.<br>
						<strong>Gunakan alamat e-mail yang resmi dan tetap.</strong>
					</span>
				</div>
			</div>
			
			<?php /*
			<div class="control-group">
				<span class="control-label">Chapter</span>
				<div class="controls">
					<span class="input-large uneditable-input"><?php echo $chapter_name ?></span>
				</div>
			</div>
			*/ ?>
			
			<div class="form-actions">
				<button type="submit" class="btn btn-success">Buat Akun</button>
				<a href="<?php L(array('action' => 'redeem')) ?>" class="btn">Kembali</a>
			</div>
		</form>
	</section>
</div>
<?php $this->print_footer(); ?>
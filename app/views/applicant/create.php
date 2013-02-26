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
	<h2>Tahap 2</h2>
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

		<p>Untuk melanjutkan proses pendaftaran, Adik perlu membuat Akun Bina Antarbudaya. Akun ini digunakan untuk mengakses formulir pendaftaran Adik dan menyimpannya. <strong>PIN Pendaftaran Adik tidak akan digunakan lagi.</strong></p>
		
		<p>Adik akan terdaftar pada Bina Antarbudaya Chapter <strong><?php echo $chapter_name ?></strong>, dan seluruh rangkaian seleksi yang Adik ikuti akan diselenggarakan oleh Chapter <?php echo $chapter_name ?>. Apabila Adik memperoleh PIN pendaftaran selain dari Chapter <?php echo $chapter_name ?>, hubungi tempat Adik memperoleh PIN pendaftaran Adik.</p>
		
		<div class="accordion help-accordion" id="accordion2">
			
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" data-target="#collapseOne" href="#collapseOne">
						Saya sudah pernah membuat akun, namun saya lupa password saya.
					</a>
				</div>
				<div id="collapseOne" class="accordion-body collapse">
					<div class="accordion-inner">
						Jika Adik lupa password Adik, silakan kunjungi halaman <a href="<?php L(array('controller' => 'auth', 'action' => 'forgot')) ?>">pengembalian password</a>.
					</div>
				</div>
			</div>
			
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" data-target="#collapseTwo" href="#collapseTwo">
						Saya sudah pernah membuat akun, namun sudah kadaluarsa.
					</a>
				</div>
				<div id="collapseTwo" class="accordion-body collapse">
					<div class="accordion-inner">
						<p>Untuk mengaktivasi kembali akun Adik, isi formulir di bawah ini:</p>
						<form action="<?php L(array('action' => 'reactivate')) ?>" class="form-inline" method="POST">
							<input type="text" name="username" required id="reactivate-username" class="span2" placeholder="Username">
							<input type="password" name="password" required id="reactivate-password" class="span2" placeholder="Password">
							<input type="hidden" name="token" value="<?php echo $token ?>">
							<button type="submit" class="btn">Lanjut</button>
						</form>

					</div>
				</div>
			</div>
			
		</div>
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
			
			<div class="control-group">
				<span class="control-label">PIN Pendaftaran</span>
				<div class="controls">
					<span class="input-large uneditable-input"><?php echo $token ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="password" name="password" id="password" required>
					<span class="help-block">Terdiri atas paling sedikit delapan karakter.</span>
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
					<input type="email" name="email" id="email" required> <span class="help-block">Alamat e-mail ini akan digunakan untuk administrasi Adik sepanjang seleksi.<br> <strong>Gunakan alamat e-mail yang tetap.</strong></span>
				</div>
			</div>
			
			<div class="control-group">
				<span class="control-label">Chapter</span>
				<div class="controls">
					<span class="input-large uneditable-input"><?php echo $chapter_name ?></span>
				</div>
			</div>
			
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Buat Akun</button>
				<a href="<?php L(array('action' => 'redeem')) ?>" class="btn">Kembali</a>
			</div>
		</form>
	</section>
</div>

	<section class="intro" style="display: none">		
		<header>
			<h1>PIN Pendaftaran Berhasil Dimasukkan</h1>
		</header>
		<p class="hello">Untuk melanjutkan proses pendaftaran, Adik perlu membuat akun. Akun ini digunakan untuk mengisi formulir pendaftaran dan mengubah isian formulir tersebut sebelum finalisasi.</p>
		<p>Adik akan terdaftar pada Bina Antarbudaya Chapter <strong><?php echo $chapter_name ?></strong>. Jika Adik memperoleh PIN pendaftaran selain dari Chapter <?php echo $chapter_name ?>, hubungi panitia chapter tempat Adik memperoleh PIN pendaftaran Adik.</p>
	</section>

	<section class="user-form" style="display: none">		
		<header>
			<h1>Informasi Akun</h1>
		</header>
		<form action="<?php L(array('controller' => 'applicant', 'action' => 'create')) ?>" method="POST" validate>
			<table class="form-table">
				<tr class="noborder">
					<td class="label"><?php $form->label('username', 'Username', 'required') ?></td>
					<td class="field"><input type="text" name="username" id="username" class="medium" value="<?php echo $this->session->flash('username'); ?>" autofocus required> <span class="instruction">Terdiri atas paling sedikit empat karakter, dan hanya boleh terdiri atas huruf, angka, garisbawah (_), tanda sambung (-). Tidak boleh mengandung spasi.</span></td>
				</tr>
				<tr>
					<td class="label"><?php $form->label('password', 'Password', 'required')?></td>
					<td class="field"><input type="password" name="password" class="medium" id="password" required> <span class="instruction">Terdiri atas paling sedikit delapan karakter.</span></td>
				</tr>
				<tr>
					<td class="label"><?php $form->label('retype_password', 'Ulangi Password', 'required')?></td>
					<td class="field"><input type="password" name="retype_password" id="retype_password" class="medium" required></td>
				</tr>
				<tr>
					<td class="label">Chapter</td>
					<td class="field"><select class="medium" name="_" disabled><option><?php echo $chapter_name ?></option></select> <span class="instruction">Jika Adik membeli PIN pendaftaran selain dari Chapter <?php echo $chapter_name ?>, hubungi panitia chapter tempat Adik memperoleh PIN pendaftaran Adik.</span></td>
				</tr>
				<tr>
					<td class="label"><?php $form->label('email', 'Alamat E-mail', 'required') ?></td>
					<td class="field"><input type="email" name="email" id="email" class="medium" value="<?php echo $this->session->flash('email'); ?>" required>
						<br>
						<span class="instruction">Alamat e-mail ini akan digunakan untuk administrasi Adik sepanjang seleksi. <strong>Gunakan alamat e-mail yang tetap.</strong></span></td>
				</tr>
				<tr>
					<td class="label"></td>
					<td class="field">
						<button type="submit">Buat Akun</button>
						&nbsp;&nbsp;&nbsp;
						<?php $form->checkbox('remember') ?> <?php $form->label('remember', 'Ingat saya') ?>
					</td>
				</tr>
			</table>
		</form>
	</section>
</div>
<?php $this->print_footer(); ?>
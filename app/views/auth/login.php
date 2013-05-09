<?php $this->print_header('Login') ?>
<?php

$destination_titles = array(
	'selection::results' => 'Pengumuman Hasil Seleksi',
	'selection' => 'Informasi Seleksi',
	'admin::stats' => 'Registration Statistics',
	'admin' => 'Switchboard',
	'applicant::confirm' => 'Switchboard'
);
$destination_title = $destination_titles[$destination_name];
if (!$destination_title)
	$destination_title = $destination_titles[$destination_controller];

?>

<div class="row">
	<div class="span4 offset4">
		<header class="page-header">
			<h1>Login</h1>
		</header>
		<?php if ($mode == 'fail'): ?>
		<div class="alert alert-error">
			Perpaduan nama pengguna dan sandi yang Anda masukkan tidak cocok.
		</div>
		<?php elseif ($this->params['recovered']): ?>
		<div class="alert alert-success">
			Password berhasil diubah.
		</div>
		<?php elseif ($error = $this->params['error']): ?>
		<div class="alert alert-error">
			<?php echo $error ?>
		</div>
		<?php endif; ?>
 		<section class="login-form">
 			<form action="<?php L(array('controller' => 'auth', 'action' => 'login')) ?>" method="POST" class="form">
 				<p>
 					<label for="username">Username</label>
 					<input type="text" name="username" id="username" class="input-block-level" placeholder="Username" value="<?php echo $this->session->flash('username'); ?>" autofocus required>
 				</p>
 				<p>
 					<label for="password">Password</label>
 					<input type="password" name="password" id="password" class="input-block-level" placeholder="Password" required>
 				</p>
 				<p>
 					<label class="checkbox pull-left"><input type="checkbox" name="remember" id="remember"> Ingat saya</label>
 					<button class="btn pull-right" type="submit">Login</button>
 				</p>
 				<p class="aux">
					<a href="<?php L(array('controller' => 'auth', 'action' => 'forgot')) ?>">Saya lupa password saya</a>
				<?php if ($this->can_register()): ?>
					<a href="<?php L(array('controller' => 'applicant', 'action' => 'create')) ?>" class="register-link">Saya belum punya akun</a>
				<?php endif; ?>
				</p>
 			</form>
 		</section>
 	</div>
</div>
<?php $this->print_footer(); ?>
<?php $this->print_header() ?>
<header class="page-header welcome-header">
	<h1>Pendaftaran Seleksi Bina Antarbudaya <?php echo $program_year - 1 ?>&ndash;<?php echo $program_year ?></h1>
	<h2>Explore&nbsp;the&nbsp;World, Explore&nbsp;Yourself</h2>
</header>

<div class="welcome">
	<div class="row">
	 	<div class="span6">
	 		<section class="about-us">
		 		<h3>Tentang Bina Antarbudaya</h3>
		 		<p>
		 			<strong>Bina Antarbudaya</strong> adalah organisasi pendidikan
					antarbudaya berbasis relawan, yang memberikan
					kesempatan untuk memperoleh pendidikan
					antarbudaya secara global. Sejak didirikan pada
					tahun 1985, Bina Antarbudaya telah mengirim lebih
					dari 3000 siswa Indonesia dan menerima lebih
					dari 1500 siswa asing dari berbagai Negara. Saat
					ini Bina Antarbudaya telah memiliki <?php echo $chapter_count ?> chapter di
					seluruh Indonesia.
				</p>
				<p>
					Bina Antarbudaya merupakan mitra AFS
					Intercultural Programs, salah satu organisasi
					pertukaran antarbudaya terbesar di dunia yang
					beroperasi di lebih dari 60 negara di lima benua.
				</p>
		 	</section>
	 	</div>
	 	<div class="span6">
	 		<section class="registration">
		 		<h3>Tata Cara Pendaftaran</h3>
		 		<div class="row">
		 			<div class="span3">
		 				<ol class="registration-steps">
		 					<li>Mendapatkan <span>PIN pendaftaran</span> dari Chapter Bina Antarbudaya <a href="#chapters">terdekat</a></li>
		 					<li><span>Mengaktifkan</span> PIN pendaftaran dan <span>membuat akun</span></li>
		 					<li><span>Mengisi</span> formulir pendaftaran online</li>
		 					<li><span>Mengumpulkan</span> berkas-berkas pendaftaran ke Chapter</li>
		 				</ol>

		 				<p class="registration-dates">
		 					<span class="pre">Masa pendaftaran:</span>
		 					<span class="from"><?php echo $reg_start->format('j M') ?></span>
		 					&ndash;
		 					<span class="to"><?php echo $reg_end->format('j M') ?></span>
		 					<?php echo $program_year - 2 ?>
		 				</p>
		 			</div>
				 	<div class="span3">
				 		<?php if (!$can_register): ?>
				 		<section class="results-form">
				 			<p><a class="btn btn-primary btn-large btn-block" href="<?php L(array('controller' => 'home', 'action' => 'results')) ?>">Pengumuman Hasil Seleksi</a></p>
				 		</section>
				 		<?php elseif ($this->is_logged_in() && $this->user->applicant):
				 		?>
				 		<section class="big-redeem-button">
				 			<p><a class="btn btn-primary btn-large btn-block" href="<?php L($this->session->user->get_landing_page()) ?>">Lanjutkan Pendaftaran</a></p>
				 		</section>
	 					<?php else: ?>
				 		<section class="big-redeem-button">
				 			<p><a class="btn btn-success btn-large btn-block" href="<?php L(array('controller' => 'applicant', 'action' => 'redeem')) ?>">Aktifkan PIN Pendaftaran</a></p>
				 		</section>
				 		<?php endif; ?>
				 		<p class="or"><span>atau</span></p>
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
				 				<p class="or"><span>atau</span></p>
				 				<p>
									<a href="<?php L(array('controller' => 'auth', 'action' => 'forgot')) ?>" class="btn btn-danger btn-block">Saya lupa password saya</a>
				 				</p>
				 			</form>
				 		</section>
				 	</div>
				 </div>
		 	</section>
	 	</div>
	</div>
	<section class="chapters-list" id="chapters">
		<header>
			<h3>Chapter-Chapter Bina Antarbudaya</h3>
		</header>
		<div class="row">
		<?php $i = 0; foreach ($chapters as $c): ?>
		<?php if (($i != 0) && ($i % 3 == 0)): ?>

		</div>
		
		<div class="row">
			
		<?php endif; ?>
			<div class="span4 chapter-item">
				<h4 class="chapter-name"><?php echo $c->chapter_name ?></h4>
				<p class="address"><?php echo nl2br($c->chapter_address) ?></p>
				<p class="links">
					<a href="mailto:<?php echo $c->get_email() ?>?subject=Pendaftaran Seleksi"><?php echo $c->get_email() ?></a><br>
					<?php if ($u = $c->twitter_username) { ?><a href="http://twitter.com/<?php echo $u ?>">@<?php echo $u ?></a><?php } ?>
				</p>
			</div>
		<?php $i++; endforeach; ?>
		</div>
	</section>
</div>

<?php $this->print_footer() ?>

<?php /*
<?php $this->header(); ?>
<div class="container">
	<?php if ($this->can_register()): ?>
	<a href="<?php L('/daftar') ?>"><img src="<?php L('/assets/dengar.png'); ?>" alt="Dengar kata dunia. Didengar oleh dunia."></a>
	<?php elseif ($enable_announcement || true): $form = new FormDisplay; ?>
	<section class="announcement-form">
		<?php if ($this->params['not_found']): ?>
		<div class="message error">
			<p>Peserta tidak ditemukan.</p>
		</div>
		<?php endif; ?>
		<header>
			<h1>Pengumuman Hasil Seleksi</h1>
		</header>
		<form action="<?php L(array("controller" => "applicant", "action" => "results")) ?>" method="POST">
			<table class="form-table">
				<tr>
					<td class="label">Nomor Peserta</td>
					<td class="field"><input type="text" class="medium" value="YBA/YP/13-14/XXX/YYYY" placeholder="YBA/YP/13-14/XXX/YYYY" name="test_id"><?php // $form->text('test_id') ?></td>
				</tr>
				<tr>
					<td class="label">Tanggal Lahir</td>
					<td class="field"><?php $form->date('dob') ?></td>
				</tr>
				<tr>
					<td class="label"></td>
					<td class="field"><input type="hidden" name="on_fail_go_to" value="<?php L(array('controller' => 'home', 'action' => 'index', 'not_found' => 1)) ?>"><button type="submit">Buka</button></td>
				</tr>
			</table>
		</form>
	</section>
	<?php endif; ?>
</div>
<?php $this->footer(); ?>
*/ ?>
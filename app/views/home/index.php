<?php $this->print_header() ?>
<header class="page-header welcome-header">
	<h1>Explore the World, Explore&nbsp;Yourself</h1>
	<h2>Pendaftaran Seleksi Bina Antarbudaya <?php echo $this_year ?></h2>
</header>

<div class="welcome">
	<div class="row">
	 	<div class="span6">
	 		<section class="about-us">
		 		<h3>Tentang Bina Antarbudaya</h3>
		 		<p>Bina Antarbudaya adalah lembaga nirlaba berbasis relawan yang bergerak dalam bidang pendidikan dan pertukaran antarbudaya, bermitra dengan AFS Intercultural Programs yang berkantor pusat di New York, Amerika Serikat. Misi Bina Antarbudaya adalah menciptakan pemimpin masa depan yang berkualitas pribadi, berprestasi, memiliki visi, empati sosial dan nasionalisme serta berwawasan internasional.</p>
		 		<p>Bina Antarbudaya memiliki <?php echo $chapter_count ?> chapter (cabang) di seluruh Indonesia, berkomitmen untuk membantu mempersiapkan para pemuda membangun karakter, meningkatkan pengetahuan, sikap serta keahlian untuk menjadi para pemimpin masa depan dan membuat perubahan positif di masyarakat.</p>
		 	</section>
	 	</div>
	 	<div class="span6">
	 		<section class="registration">
		 		<h3>Tata Cara Pendaftaran</h3>
		 		<div class="row">
		 			<div class="span3">
		 				<ol class="registration-steps">
		 					<li><span>Menghubungi</span> Chapter Bina Antarbudaya <a href="#chapters">terdekat</a></li>
		 					<li>Mendapatkan <span>PIN pendaftaran</span></li>
		 					<li><span>Mengaktifkan</span> PIN pendaftaran</li>
		 					<li><span>Mengisi</span> formulir pendaftaran online</li>
		 					<li><span>Mengumpulkan</span> berkas fisik</li>
		 				</ol>

		 				<p class="registration-dates">
		 					<span class="pre">Masa pendaftaran:</span>
		 					<span class="from">1 Mar</span>
		 					&ndash;
		 					<span class="to">14 Apr</span>
		 					<?php echo $this_year ?>
		 				</p>
		 			</div>
				 	<div class="span3">
				 		<section class="big-redeem-button">
				 			<p><a class="btn btn-primary btn-large btn-block" href="<?php L(array('controller' => 'applicant', 'action' => 'redeem')) ?>">Aktifkan PIN Pendaftaran</a></p>
				 		</section>
				 		<p class="or"><span>atau</span></p>
				 		<section class="login-form">
				 			<form action="<?php L(array('controller' => 'auth', 'action' => 'login')) ?>" method="POST" class="form">
				 				<p>
				 					<label for="username">Username</label>
				 					<input type="text" name="username" id="username" class="input-block-level" placeholder="Username" value="<?php echo $this->session->flash('username'); ?>" required>
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
		<?php if (($i != 0) && ($i % 4 == 0)): ?>

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
<?php $this->print_header() ?>
<header class="page-header">
	<h1>Pendaftaran Seleksi Bina Antarbudaya 2013</h1>
</header>

<div class="welcome">
	<p class="intro">
		Selamat datang di trial sistem pendaftaran seleksi Bina Antarbudaya Year Program 2014-2015.
	</p>
	<p>Secara umum, sistem pendaftaran tahun ini tidak jauh berbeda dengan tahun sebelumnya. Perbedaan yang paling utama terletak di balik layar dan tidak dapat terlihat secara langsung oleh Kakak. Meski demikian, terdapat beberapa perbedaan pada tampilan antarmuka yang perlu Kakak perhatikan dan kritisi. Selain itu, masih terdapat beberapa laman yang tampilannya tercampur antara tampilan lama dan tampilan baru.</p>
	<p>Untuk informasi lebih lanjut harap hubungi Kak Rio di <a href="mailto:rio.apriyanto@afs.org">rio.apriyanto@afs.org</a>.</p>
	<p>Sincerely,</p>
	<p>Online Registration Team 2013</p>
	<hr>
	<p>
		<a class="btn btn-large btn-primary" href="<?php L(array('controller' => 'applicant', 'action' => 'redeem')) ?>">Aktifkan PIN pendaftaran</a>
	</p>
	<p>
		Untuk membangkitkan PIN pendaftaran, silakan <a class="btn" href="<?php L(array('controller' => 'auth', 'action' => 'login')) ?>">Login</a>
	</p>
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
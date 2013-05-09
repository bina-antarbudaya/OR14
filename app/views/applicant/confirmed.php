<?php $this->print_header('Pendaftaran Berhasil'); ?>
<header class="page-header">
	<h1>Pendaftaran Berhasil</h1>
</header>
<div class="alert alert-success">
	Berkas Adik telah kami terima. Tunggu pengumuman selanjutnya dari Kakak di Chapter untuk informasi selanjutnya.
</div>
<p>
	<a class="btn-large btn-primary" href="<?php L(array('controller' => 'applicant', 'action' => 'transcript')) ?>">Unduh Transkrip Formulir Pendaftaran</a>
</p>
<?php $this->print_footer() ?>
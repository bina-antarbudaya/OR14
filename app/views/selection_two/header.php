<header class="page-title">
	<hgroup>
		<?php $ch = $this->session->user->chapter ?>
		<h1><a href="<?php L(array('controller' => 'chapter', 'action' => 'view', 'chapter_code' => $ch->chapter_code)) ?>"><?php echo $ch->get_title() ?></a></h1>
		<h2>Persiapan Seleksi Tahap Kedua</h2>
	</hgroup>
</header>
<?php

$batch_count = $this->get_batch_count();
if ($batch_count == 0)
	$nav_array = array(	'create_batch' => 'Masukkan Daftar Kelulusan' );
elseif ($batch_count == 1)
	$nav_array = array(	'edit_batch' => 'Pengaturan Ruangan Seleksi Tahap Kedua',
						'create_batch' => 'Tambahkan Gelombang Pengumuman', );
else
	$nav_array = array(	'index' => 'Daftar Gelombang Pengumuman',
						'create_batch' => 'Tambahkan Gelombang Pengumuman' );
$this->actions_nav($nav_array);

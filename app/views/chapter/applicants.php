<?php $this->print_header('Pengelolaan Peserta') ?>
<header class="page-header combo">
	<h1>Pengelolaan Peserta</h1>
</header>

<?php 

global $p;
$p = $this->params;
function LL($target) {
	global $p;
	$pi = array_merge($p, $target);
	L($pi);
}
?>
<ul class="nav nav-tabs">
	<li<?php if ($current_stage == 'active') echo ' class="active"' ?> data-toggle="tooltip" title="Seluruh peserta yang masih mengisi formulir dan belum melewati batas pendaftaran, atau yang sudah melakukan finalisasi."><a href="<?php LL(array('stage' => 'active')) ?>">Seluruh Peserta Aktif</a></li>
	

	<?php
	$sel_dropdown = array(
		'selection_1' => 'Seleksi 1',
		'selection_2' => 'Seleksi 2',
		'selection_3' => 'Seleksi 3',
		'national_selection' => 'Kandidat Chapter',
		'national_candidate' => 'Kandidat Nasional'
	);
	$sel_active = $sel_dropdown[$current_stage];
	?>
	<li class="dropdown<?php if ($sel_active) echo ' active' ?>">
		<a class="dropdown-toggle" href="#" data-toggle="dropdown">
			<?php echo $sel_active ? $sel_active : 'Seleksi' ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		<?php foreach($sel_dropdown as $i => $j): ?>
			<li<?php if ($i == $current_stage) echo ' class="active"' ?>><a href="<?php LL(array('stage' => $i)) ?>"><?php echo $j ?></a></li>

		<?php endforeach; ?>
		</ul>
	</li>

	<?php
	$reg_dropdown = array(
		'confirmed' => 'Terkonfirmasi',
		'finalized' => 'Sudah Finalisasi',
		'not_yet_confirmed' => 'Belum Dikonfirmasi',
		'incomplete' => 'Masih Mengisi',
		'expired' => 'Kadaluarsa'
	);
	$reg_active = $reg_dropdown[$current_stage];
	?>
	<li class="dropdown<?php if ($reg_active) echo ' active' ?>">
		<a class="dropdown-toggle" href="#" data-toggle="dropdown">
			<?php echo $reg_active ? $reg_active : 'Status Pendaftaran' ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		<?php foreach($reg_dropdown as $i => $j): ?>
		<?php if ($i == 'expired') echo '<li class="divider"></li>'; ?>
			<li<?php if ($i == $current_stage) echo ' class="active"' ?>><a href="<?php LL(array('stage' => $i)) ?>"><?php echo $j ?></a></li>

		<?php endforeach; ?>
		</ul>
	</li>

	<li<?php if ($current_stage == 'search') echo ' class="active"' ?>><a href="#" id="searchLink">Pencarian</a></li>
</ul>

<!-- <?php var_dump($applicants) ?> -->

<ul class="nav nav-pills pull-left">

	<?php
	$views = array('list' => 'Daftar', 'stats' => 'Statistik')
	?>
	<?php foreach ($views as $i => $j): ?>
	<li<?php if ($i == $view) echo ' class="active"' ?>><a href="<?php LL(array('view' => $i)) ?>"><?php echo $j ?></a></li>

	<?php endforeach; ?>
</ul>

<?php $this->render('chapter/applicants/' . $view); ?>

<?php $this->require_js('dashboard'); $this->print_footer(); ?>
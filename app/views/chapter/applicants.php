<?php $this->print_header('Pengelolaan Peserta') ?>
<header class="page-header combo">
	<h1>Pengelolaan Peserta</h1>
</header>

<?php 

global $p;
$p = $this->params;
function LL($target) {
	global $p;
	$target['combo'] = $target['school_name'] = null;
	$pi = array_merge($p, $target);
	L($pi);
}
?>
<ul class="nav nav-tabs">
	<li class="disabled"><a>Filter:</a></li>
	<li<?php if ($current_stage == 'active') echo ' class="active"' ?> data-toggle="tooltip" title="Seluruh peserta yang masih mengisi formulir dan belum melewati batas pendaftaran, atau yang sudah melakukan finalisasi."><a href="<?php LL(array('stage' => 'active')) ?>">Seluruh Peserta Aktif</a></li>
	<li<?php if ($current_stage == 'expired') echo ' class="active"' ?> data-toggle="tooltip" title="Seluruh peserta yang telah melewati batas pendaftaran tanpa melakukan finalisasi"><a href="<?php LL(array('stage' => 'expired')) ?>">Kadaluarsa</a></li>

	<?php
	$reg_dropdown = array(
		'confirmed' => 'Sudah verifikasi berkas',
		'not_yet_confirmed' => 'Belum verifikasi berkas',
		'finalized' => 'Sudah finalisasi',
		'incomplete' => 'Masih mengisi'
	);
	$reg_active = $reg_dropdown[$current_stage];
	?>
	<li class="dropdown<?php if ($reg_active) echo ' active' ?>">
		<a class="dropdown-toggle" href="#" data-toggle="dropdown">
			Status Pendaftaran<?php if ($reg_active) echo ': <b>' . $reg_active . '</b>';;  ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		<?php foreach($reg_dropdown as $i => $j): ?>
			<li<?php if ($i == $current_stage) echo ' class="active"' ?>><a href="<?php LL(array('stage' => $i)) ?>"><?php echo $j ?></a></li>

		<?php endforeach; ?>
		</ul>
	</li>

	<?php
	$sel_dropdown = array(
		'selection_1' => 'Peserta Seleksi 1',
		'selection_2' => 'Peserta Seleksi 2 (Lulus Seleksi 1)',
		'selection_3' => 'Peserta Seleksi 3 (Lulus Seleksi 2)',
		'national_selection' => 'Peserta Seleksi Nasional (Kandidat Chapter)',
		'national_candidate' => 'Kandidat Nasional'
	);
	$sel_active = $sel_dropdown[$current_stage];
	?>
	<li class="dropdown<?php if ($sel_active) echo ' active' ?>">
		<a class="dropdown-toggle" href="#" data-toggle="dropdown">
			Status Seleksi<?php if ($sel_active) echo ': <b>' . $sel_active . '</b>';  ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
		<?php foreach($sel_dropdown as $i => $j): ?>
			<li<?php if ($i == $current_stage) echo ' class="active"' ?>><a href="<?php LL(array('stage' => $i)) ?>"><?php echo $j ?></a></li>

		<?php endforeach; ?>
		</ul>
	</li>

	<li<?php if ($current_stage == 'search') echo ' class="active"' ?>><a href="#" id="searchLink">Pencarian</a></li>
</ul>

<!-- <?php var_dump($applicants) ?> -->

<div id="searchPane" class="search-pane<?php if ($current_stage == 'search') echo ' active' ?>">
	<form class="form" action="<?php LL(array('filter' => 'search')) ?>" method="GET">
		<div class="row">
			<div class="span4">
				<label class="control-label" for="search-combo">Nama peserta, nomor peserta, atau username</label>
				<div class="controls">
					<input type="text" name="combo" class="input-block-level" id="search-combo" value="<?php echo $this->params['combo'] ?>">
				</div>
			</div>
			<div class="span4">
				<label class="control-label" for="search-name">Asal sekolah</label>
				<div class="controls">
					<input type="text" name="school_name" class="input-block-level" id="search-school-name" value="<?php echo $search['school_name'] ?>">
				</div>
			</div>
			<div class="span4">
				<label class="control-label" for="search-stage">Status pendaftaran atau seleksi</label>
				<div class="controls">
					<select name="stage" class="input-block-level" id="search-stage">
						<option value="" style="font-style: italic">(Status apa saja)</option>
					<?php foreach (($reg_dropdown + $sel_dropdown) as $stage => $label): ?>
						<option value="<?php echo $stage ?>"<?php if ($current_stage == $stage) echo ' selected' ?>><?php echo $label ?></option>

					<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="search-button">
			<input type="hidden" name="view" value="<?php echo $view ?>">
			<button type="submit" class="btn btn-primary">Cari</button>
		</div>
	</form>
</div>

<ul class="nav nav-pills pull-left">
	<li class="disabled"><a>Tampilan:</a></li>
	<?php
	$views = array(
		'list' => 'Daftar Peserta',
		'stats' => 'Statistik',
		// '' => 'Excel'
		);
	?>
	<?php foreach ($views as $i => $j): ?>
	<li<?php if ($i == $view) echo ' class="active"' ?>><a href="<?php LL(array('view' => $i)) ?>"><?php echo $j ?></a></li>

	<?php endforeach; ?>
</ul>

<?php
if ($count_all > 0) {
	$this->render('chapter/applicants/' . $view);
}
else {
	echo '<br clear="all"><p>Tidak ada peserta yang ditemukan.</p>';
}
?>

<?php $this->require_js('dashboard'); $this->print_footer(); ?>
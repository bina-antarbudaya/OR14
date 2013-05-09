<?php

global $p;
$p = $this->params;
function LL($target) {
	global $p;
	$pi = array_merge($p, $target);
	L($pi);
}

function print_pie_chart($source, $threshold = 0.01, Array $series_map = array()) {
	global $p;
	static $i;

	arsort($source);

	$total = array_sum($source);

	$labeled_data = array();
	foreach ($source as $key => $value) {
		$key = $series_map[$key] ? $series_map[$key] : $key;
		if (!$key)
			$key = 'Kosong';

		$entry = array(
			'label' => $key,
			'data' => (int) $value,
		);

		$labeled_data[] = $entry;
	}
	$data = json_encode($labeled_data);
	$id = 'graph' . ++$i;
	
	?>
	<div id="<?php echo $id ?>">
	</div>
	<script>
	$(function(){
		$.plot($("#<?php echo $id ?>"), <?php echo $data ?>, 
		{
			series: {
				pie: { 
					combine: {
						color: '#ccc',
						threshold: 0.01
					},
					show: true,
					radius: 1,
					innerRadius: 0.3,
					label: {
						show: true,
						radius: 2/3,
						formatter: function(label, series){
							return '<div style="font-size:8pt;text-align:center;padding:5px;color:white">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						threshold: 0.05,
						background: { 
	                        opacity: 0.4,
	                        color: '#000'
	                    }
					}
				}
			},
			legend: {
				show: false
			}
		});
	});
	</script>
	<?php
}

function print_leaderboard($source, $label = '', Array $series_map = array()) {
	// sort
	$source_copy = $source;
	if ($source['series'])
		$source = $source['series'];
	
	arsort($source);

	// labels for total
	$total_label = $custom_total_label ? $custom_total_label : 'total';
	if ($source_copy['total'])
		$total = $source_copy['total'];
	else
		$total = array_sum($source);

	$source_key_count = count(array_keys($source));

	?>
	<table>
		<tr>
			<td></td>
			<td><?php if ($label) { ?><strong><?php echo $source_key_count; ?></strong> <?php echo $label; } ?></td>
			<td colspan="2"></td>
		</tr>
	<?php $i = 0; foreach ($source as $n => $s):
		if ($series_map[$n])
			$n = $series_map[$n];
		$p = number_format($s / $total * 100, 2, ',', '.');
	?>
		<tr>
			<td class="rank"><?php
			if ($n) {
				if ($prev == $s) {
					++$i;
					echo "<span class=\"repeat\">$prev_rank</span>";
				}
				else
					echo ++$i; 
			} ?></td>
			<td><?php echo $n; if (!$n): ?><i>(unspecified)</i><?php endif; ?></td>
			<td class="count"><strong><?php echo $s; ?></strong></td>
			<td class="percentage"><?php echo $p ?>%</td>
		</tr>
	<?php
	if ($prev != $s)
		$prev_rank = $i;
	$prev = $s;
	endforeach;
	?>
	</table>
	<?php
}

?>
<?php $this->header('Statistics'); ?>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="../excanvas.min.js"></script><![endif]-->
<script src="<?php L('/assets/js/jquery-1.7.2.min.js') ?>"></script>
<script src="<?php L('/assets/js/flot/jquery.flot.js') ?>"></script>
<script src="<?php L('/assets/js/flot/jquery.flot.pie.js') ?>"></script>
<header class="page-title">
	<hgroup>
		<h1><a href="<?php L(array('controller' => 'chapter', 'action' => 'view', 'chapter_code' => $chapter ? $chapter->chapter_code : $this->user->chapter->chapter_code)) ?>"><?php echo $chapter ? $chapter->get_title() : $this->user->chapter->get_title() ?></a></h1>
		<h2>Statistik Pendaftar</h2>
	</hgroup>
</header>
<nav class="actions-nav">
	<ul>
		<?php foreach(array('' => $search_title ? $search_title : 'Seluruh Pendaftar', 'confirmed' => 'Terkonfirmasi', 'finalized' => 'Terfinalisasi', 'incomplete' => 'Sedang Mengisi', 'expired' => 'Kadaluarsa') as $i => $j): ?>
		<li><a href="<?php LL(array('stage' => $i)) ?>"<?php if ($i == $current_stage) echo 'class="active"' ?>><?php echo $j ?></a></li>

		<?php endforeach; ?>
	</ul>
</nav>

<div class="container">

<?php if ($this->user->chapter->is_national_office()):
	// $chapters = Chapter::find();
	// $labels = array();
	// foreach ($chapters as $c) {
	// 	$labels[$c->id] = $c->chapter_name;
	// }
	$labels = array();
?>
<article class="statbox">
	<header>Chapter</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['chapter']['data']['series'], 0.01, array(), 'chapter_name')?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['chapter']['data']['series'], 'chapter')?>
		</div>
	</div>
</article>
<?php endif; ?>

<article class="statbox gender_distribution">
	<header>Jenis Kelamin</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['sex']['data']['series'], 0.01, array('M' => 'Laki-laki', 'F' => 'Perempuan'), 'sex')?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['sex']['data']['series'], '', array('M' => 'Laki-laki', 'F' => 'Perempuan'))?>
		</div>
	</div>
</article>

<article class="statbox">
	<header>Asal Sekolah</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['school']['data']['series'], 0.01, array(), 'school_name')?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['school']['data']['series'], 'sekolah')?>
		</div>
	</div>
</article>

<article class="statbox">
	<header>Asal Provinsi</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['province']['data']['series'], 0.01, array())?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['province']['data']['series'], 'provinsi')?>
		</div>
	</div>
</article>

<article class="statbox">
	<header>Asal Kota</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['city']['data']['series'], 0.01)?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['city']['data']['series'], 'kota')?>
		</div>
	</div>
</article>
<?php /*
<article class="statbox countries">
	<header>Pilihan Negara</header>
		<table>
			<tr>
				<td><?php if ($label) { ?><strong><?php echo $source_key_count; ?></strong> <?php echo $label; } ?></td>
				<?php for ($i = 1; $i <= 10; $i++): ?>
				<td>#<?php echo $i ?></td>
				<?php endfor; ?>
			</tr>
		<?php $i = 0; foreach ($country_stats as $country_name => $preferences):
			// if ($series_map[$n])
			// 	$n = $series_map[$n];
			// $p = number_format($preferences / $total_afs * 100, 2, ',', '.');
		?>
			<tr class="prim">
				<td rowspan="2" class="country-name"><?php echo $country_name; ?></td>
				<?php for ($i = 1; $i <= 10; $i++):
				$n = $preferences[$i];
				?>
				<td class="count"><strong><?php echo $n; ?></strong></td>
				<?php endfor; ?>
			</tr>
			<tr>
				<?php for ($i = 1; $i <= 10; $i++):
				$n = $preferences[$i];
				$p = number_format($n / $total_afs * 100, 2, ',', '.');
				?>
				<td class="percentage"><?php echo $p ?>%</td>
				<?php endfor; ?>
			</tr>
		<?php
		if ($prev != $s)
			$prev_rank = $i;
		$prev = $s;
		endforeach;
		?>
		</table>
</article>

<article class="statbox">
	<header>Pilihan Negara Lainnya</header>
	<div class="stat-body">
		<div class="chart">
			<?php print_pie_chart($stats['other_countries']['data']['series'], 0.01)?>
		</div>
		<div class="leaderboard">
			<?php print_leaderboard($stats['other_countries']['data'], 'negara')?>
		</div>
	</div>
</article>
*/ ?>

<!-- 
<h1>Phone Numbers</h1>

<p>
<?php

// $addresses = $db->get_col("SELECT alamat_lengkap FROM applicant_details LEFT JOIN applicants ON applicants.id=applicant_details.applicant_id WHERE submitted=1");
// foreach ($addresses as $a) {
// 	$a = unserialize($a);
// 	$hp = $a['hp'];
// 	if ($hp) echo $hp . ', ';
// }

?>
</p>

<h1>Email Addresses</h1>

<p>

<?php

// foreach ($addresses as $a) {
// 	$a = unserialize($a);
// 	$email = $a['email'];
// 	if ($email) echo $email . ', ';
// }

?>
</p>
-->
</div>

<?php $this->footer(); ?>
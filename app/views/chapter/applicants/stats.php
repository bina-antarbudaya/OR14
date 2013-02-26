<?php

global $p;
$p = $this->params;

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
	charts.push(function(){
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
						show: false,
						radius: 2/3,
						//formatter: function(label, series){
						//	return '<div style="font-size:8pt;text-align:center;padding:5px;color:white">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						//},
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
	<table class="table table-striped">
		<thead>
		<tr>
			<th></td>
			<th><?php if ($label) { ?><strong><?php echo $source_key_count; ?></strong> <?php echo $label; } ?></td>
			<th colspan="2">Jumlah</td>
		</tr>
	</thead>
	<tbody>
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
</tbody>
	</table>
	<?php
}

function print_statbox($title, $source, $label = '', $threshold = 0.01, Array $series_map = array()) {
	?>
<section class="statbox">
	<header>
		<h3><?php echo $title ?></h3>
	</header>
	<div class="stat-body row">
		<div class="span6 leaderboard">
			<?php print_leaderboard($source, $label, $series_map)?>
		</div>
		<div class="span6 chart">
			<?php print_pie_chart($source, $threshold, $series_map)?>
		</div>
	</div>
</section>
	<?php
}

?>

<script>
charts = [];
</script>

<div class="stats">

<?php
if ($this->user->chapter->is_national_office()) {
	// $chapters = Chapter::find();
	// $labels = array();
	// foreach ($chapters as $c) {
	// 	$labels[$c->id] = $c->chapter_name;
	// }
	$labels = array();
	print_statbox('Chapter', $stats['chapter']['data']['series'], 'chapter');
}
?>

<?php print_statbox('Jenis Kelamin', $stats['sex']['data']['series'], '', 0.01, array('M' => 'Laki-laki', 'F' => 'Perempuan')) ?>

<?php print_statbox('Asal Sekolah', $stats['school']['data']['series'], 'sekolah') ?>

<?php print_statbox('Asal Provinsi', $stats['province']['data']['series'], 'provinsi') ?>

<?php print_statbox('Asal Kota', $stats['city']['data']['series'], 'kota') ?>

<!--
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

-->

</div>

<?php
$this->require_js('flot/jquery.flot');
$this->require_js('flot/jquery.flot.pie');
$this->require_js('stats');
?>
<?php
/*
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
*/ ?>
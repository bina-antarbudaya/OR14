<?php

global $p;
$p = $this->params;

function print_pie_chart($source, $threshold = 0.01, $series_map = array()) {
	global $p;
	static $i;

	arsort($source);

	$total = array_sum($source);

	$labeled_data = array();
	$blank_label = 'Tidak diisi';
	foreach ($source as $key => $value) {
		$key = $series_map[$key] ? $series_map[$key] : $key;
		if (!$key)
			$key = 'Tidak diisi';

		$entry = array(
			'label' => $key,
			'data' => (int) $value,
		);

		$labeled_data[] = $entry;
	}
	$data = json_encode($labeled_data);
	$id = 'graph' . ++$i;
	
	?>
	<div id="<?php echo $id ?>" class="pie-chart">
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
						show: true,
						radius: 2/3,
						formatter: function(label, series){
							if (label == "<?php echo $blank_label ?>")
								return '<div style="font-size:8pt;text-align:center;padding:6px 12px;color:white;line-height:1.2"><i>'+label+'</i><br/>'+Math.round(series.percent)+'%</div>';
							return '<div style="font-size:8pt;text-align:center;padding:6px 12px;color:white;line-height:1.2"><b>'+label+'</b><br/>'+Math.round(series.percent)+'%</div>';
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
			}
			elseif ($i == 0) {
				++$i;
			}
			?></td>
			<td><?php echo $n; if (!$n): ?><i>Tidak diisi</i><?php endif; ?></td>
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
		<div class="span6">
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

<h3 class="search-title"><?php echo $search_title; ?></h3>

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

<?php print_statbox('Pilihan Program', $stats['program_choices']['data']['series'], '') ?>

<?php print_statbox('Asal Sekolah', $stats['school']['data']['series'], 'sekolah') ?>

<?php print_statbox('Jenis Sekolah: Negeri/Swasta', $stats['school_funding_type']['data']['series'], '', 0.01, array('Swasta', 'Negeri')) ?>

<?php print_statbox('Jenis Sekolah: SMA/SMK/MA/Pesantren', $stats['school_education_type']['data']['series'], '', 0.01, array('Swasta', 'Negeri')) ?>

<?php print_statbox('Kelas Akselerasi', $stats['acceleration_class']['data']['series'], '', 0.01, array('Kelas reguler atau tidak diisi', 'Kelas akselerasi')) ?>

<?php print_statbox('Asal Provinsi', $stats['province']['data']['series'], 'provinsi') ?>

<?php print_statbox('Asal Kota', $stats['city']['data']['series'], 'kota') ?>

<?php

foreach (Helium::conf('partners') as $region => $countries) {
	$n_countries = count($countries);
	$key_base = 'pref_' . $region . '_';
	for ($i = 1; $i <= $n_countries; $i++) {
		$key = $key_base . $i;
		$region_names = array('asia' => 'Kawasan Asia', 'americas' => 'Kawasan Amerika', 'europe' => 'Kawasan Eropa');
		if (is_array($stats[$key]['data']['series'])) {
			print_statbox('Pilihan Negara ' . $region_names[$region] . ' Ke-' . $i, $stats[$key]['data']['series'], 'negara', 0.01, $countries);
		}
	}
}

if ($stats['country_preferences_other']['data']) {
	print_statbox('Pilihan Negara Lainnya', $stats['country_preferences_other']['data']['series'], 'negara');
}
?>

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

</div>

<?php
$this->require_js('flot/jquery.flot');
$this->require_js('flot/jquery.flot.pie');
$this->require_js('stats');
?>
<?php $this->header() ?>
<div class="container">
	<p>Berikut adalah daftar peserta yang lulus Seleksi Tahap Ketiga.</p>
	<table class="applicants">
		<thead>
			<tr>
				<th class="test-id">No. Peserta</th>
				<th class="name">Nama Lengkap</th>
				<th class="school">Asal Sekolah</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($applicants as $a): 
		$classes = '';
		if (!$a->finalized) $classes .= 'unfinalized '; 
		if ($a->confirmed) $classes .= 'submitted '; 
		elseif ($a->is_expired()) $classes .= 'expired ';
		$exp = $a->expires_on;
		?>
			<tr class="<?php echo $classes; ?>">
				<td class="test-id"><?php echo $a->test_id; ?></td>
				<td class="name"><b><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $a->id)) ?>"><?php echo $a->sanitized_full_name ? $a->sanitized_full_name : '<span class="empty">(Belum diisi)</span>'; ?></a></b></td>
				<td class="school"><?php echo $a->sanitized_high_school_name; ?></td>
			</tr>

		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php $this->footer() ?>
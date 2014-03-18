<?php
$sel_dropdown = array(
	'selection_1' => 'Peserta Seleksi 1',
	'selection_2' => 'Peserta Seleksi 2 (Lulus Seleksi 1)',
	'selection_3' => 'Peserta Seleksi 3 (Lulus Seleksi 2)',
	'national_selection' => 'Peserta Seleksi Nasional (Kandidat Chapter)',
	'national_candidate' => 'Kandidat Nasional'
);
$reg_dropdown = array(
	'confirmed' => 'Sudah verifikasi berkas',
	'not_yet_confirmed' => 'Belum verifikasi berkas',
	'finalized' => 'Sudah finalisasi',
	'incomplete' => 'Masih mengisi',
	'expired' => 'Kadaluarsa'
);

$sel_stages = array_keys($sel_dropdown);
$reg_stages = array_keys($reg_dropdown);

function LK($target) {
	global $p;
	$pi = array_merge($p, $target);
	L($pi);
}

?>
<div class="pagination pagination-right">
	<?php if ($total_pages <= 20): ?>
	<ul>
		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
		<li<?php if ($i == $current_page) echo ' class="active"' ?>><a href="<?php LL(array('page' => $i)) ?>"><?php echo $i ?></a></li>
		<?php endfor; ?>
	</ul>
	<?php else: ?>
	<select onchange="window.location.href=this.value">
	<?php for ($i = 1; $i <= $total_pages; $i++): ?>
		<option value="<?php LL(array('page' => $i)) ?>"<?php if ($i == $current_page) echo ' selected' ?>><?php echo $i ?></option>
	<?php endfor; ?>
	</select>
	<?php endif; ?>
</div>

<table class="table table-hover">
	<thead>
		<tr>
			<!-- <th class="checkbox"></th> -->
			<?php
			$cols = array(
					'test_id' => 'No. Peserta',
					'sanitized_full_name' => 'Nama Lengkap',
					'sanitized_high_school_name' => 'Asal Sekolah',
					'expires_on' => 'Batas Pendaftaran'
				);

			if (in_array($current_stage, $sel_stages)) {
				$cols += array(
						'selection_stage' => 'Status Seleksi',
					);
			}
			else {
				$cols = $cols + array(
						'finalized' => 'Status Finalisasi',
						'confirmed' => 'Status Verifikasi Berkas'
					);
			}
			?>
			<?php foreach ($cols as $key => $label):
				$order = $current_order;
				if ($current_order == 'desc' && $current_order_by == $key) {
					$order = '';
					$order_by = '';
				}
				elseif ($current_order == 'asc' && $current_order_by == $key) {
					$order = 'desc';
					$order_by = $key;
				}
				else {
					$order = 'asc';
					$order_by = $key;
				}
			?>
				<th class="">
					<a href="<?php LK(compact('order_by', 'order')) ?>"><?php echo $label ?></a>
					<?php if ($current_order_by == $key): ?>
					<i class="icon icon-chevron-<?php echo $current_order == 'desc' ? 'down' : 'up' ?>"></i>
					<?php endif; ?>
				</th>

			<?php endforeach; ?>
			<!--
			<?php if ($current_stage != 'expired'): ?>
			<th class="test-id">No. Peserta</th>
			<?php endif; ?>
			<th class="name">Nama lengkap</th>
			<th class="school">Asal Sekolah</th>
			<th class="expires-on">Batas Pendaftaran</th>
			<th class="finalized">Status Finalisasi</th>
			<th class="confirmed">Status Verifikasi Berkas</th>
			-->
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
			<!-- <td class="checkbox"></td> -->
			<td class="test-id"><?php echo ($a->finalized) ? $a->test_id : '&mdash;'; ?></td>
			<td class="name"><b><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $a->id)) ?>"><?php echo $a->sanitized_full_name ? $a->sanitized_full_name : '<span class="empty">(Belum diisi)</span>'; ?></a></b></td>
			<td class="school"><?php echo $a->sanitized_high_school_name; ?></td>
			<td class="expires-on <?php if ($exp->earlier_than('now')) echo 'text-error' ?>"><?php echo $exp->format('j F Y'); ?></td>
			<td class="finalized text-<?php echo $a->finalized ? 'success' : 'warning' ?>"><?php echo $a->finalized ? 'Sudah' : 'Belum' ?> finalisasi</td>
			<td class="confirmed text-<?php echo $a->confirmed ? 'success' : 'warning' ?>"><?php echo $a->confirmed ? 'Sudah' : 'Belum' ?> verifikasi berkas</td>
		</tr>

	<?php endforeach; ?>
	</tbody>
</table>

<div class="pagination pagination-centered">
	<?php if ($total_pages <= 20): ?>
	<ul>
		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
		<li<?php if ($i == $current_page) echo ' class="active"' ?>><a href="<?php LL(array('page' => $i)) ?>"><?php echo $i ?></a></li>
		<?php endfor; ?>
	</ul>
	<?php else: ?>
	<select onchange="window.location.href=this.value">
	<?php for ($i = 1; $i <= $total_pages; $i++): ?>
		<option value="<?php LL(array('page' => $i)) ?>"<?php if ($i == $current_page) echo ' selected' ?>><?php echo $i ?></option>
	<?php endfor; ?>
	</select>
	<?php endif; ?>
</div>
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
			<th class="checkbox"></th>
			<?php if ($current_stage != 'expired'): ?>
			<th class="test-id">No. Peserta</th>
			<?php endif; ?>
			<th class="name">Nama Lengkap</th>
			<th class="school">Asal Sekolah</th>
			<th class="expires-on">Batas Pendaftaran</th>
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
			<td class="checkbox"></td>
			<?php if ($current_stage != 'expired'): ?>
			<td class="test-id"><?php echo $a->test_id; ?></td>
			<?php endif; ?>
			<td class="name"><b><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $a->id)) ?>"><?php echo $a->sanitized_full_name ? $a->sanitized_full_name : '<span class="empty">(Belum diisi)</span>'; ?></a></b></td>
			<td class="school"><?php echo $a->sanitized_high_school_name; ?></td>
			<td class="expires-on"><?php echo $exp->format('j F Y'); ?></td>
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
<?php $this->header() ?>
<div class="container">
	<header>
		<h1>Daftar Gelombang Seleksi 2</h1>
	</header>
	<table class="batches">
		<tr>
			<th class="no"></th>
			<th class="participant-count">Jumlah Peserta</th>
			<th class="announcement-date">Tanggal Pengumuman</th>
			<th class="selection-date">Tanggal Seleksi</th>
			<th class="selection-date">Lokasi Seleksi</th>
			<th colspan="3"></th>
		</tr>
		<?php foreach ($batches as $b): ?>
		<tr>
			<td class="no">Gelombang <?php echo $b->local_batch_number ?></td>
			<td class="participant-count"><?php echo $b->get_participant_count() ?></td>
			<td class="announcement-date"><?php echo $b->get_announcement_date()->format('j F Y'); ?></td>
			<td class="selection-date"><?php echo $b->get_selection_date()->format('j F Y'); ?></td>
			<td class="selection-venue"><?php echo $b->selection_venue ?></td>
			<td class="view"><a href="<?php L(array('action' => 'view_batch', 'id' => $b->id)) ?>">Lihat Pembagian Ruangan</a></td>
			<td class="edit"><a href="<?php L(array('action' => 'edit_batch', 'id' => $b->id)) ?>">Edit</a></td>
			<td class="delete"><a href="<?php L(array('action' => 'delete_batch', 'id' => $b->id)) ?>">Batalkan</a></td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php $this->footer() ?>
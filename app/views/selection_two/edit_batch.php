<?php $this->header(''); ?>
<script src="<?php L('/assets/js/jquery-1.7.2.min.js') ?>"></script>
<script>
$(function() {
	$('#announcement_date_follows_national').change(function() {
		$('#announcement_date_follows_national').attr('checked') ?
			$('#announcement-date').slideUp('fast') :
			$('#announcement-date').slideDown('fast');
	});
	$('#announcement_date_follows_national').attr('checked') ?
		$('#announcement-date').hide() :
		$('#announcement-date').show();
	$('#selection_date_follows_national').change(function() {
		$('#selection_date_follows_national').attr('checked') ?
			$('#selection-date').slideUp('fast') :
			$('#selection-date').slideDown('fast');
	});
	$('#selection_date_follows_national').attr('checked') ?
		$('#selection-date').hide() :
		$('#selection-date').show();
});
</script>
<div class="container">
	<?php if ($this->params['new']): ?>
	<div class="message">
		<p>Pemasukan data berhasil. Silakan lengkapi keterangan pengumuman seleksi di bawah ini.</p>
	</div>
	<?php endif; ?>
	
	<form action="<?php L(array('action' => 'edit_batch', 'id' => $batch_id)) ?>" method="POST">
		<section class="details">
			<header>
				<h1>Pengaturan Seleksi</h1>
			</header>
			<table class="form-table">
				<tr>
					<td class="label">Jumlah ruangan wawancara</td>
					<td class="field">
						<input type="number" name="pcc" value="<?php echo $batch->get_personality_chamber_count() ?>" class="very-short"> Kepribadian<br>
						<input type="number" name="ecc" value="<?php echo $batch->get_english_chamber_count() ?>" class="very-short"> Bahasa Inggris<br>
						<span class="instruction">Sistem akan mengatur urutan wawancara secara otomatis berdasarkan jumlah peserta dan jumlah ruangan yang tersedia.</span>
					</td>
				</tr>
				<tr>
					<td class="label">Tempat seleksi</td>
					<td class="field"><?php $form->text('selection_venue') ?></td>
				</tr>
				<tr>
					<td class="label">Batas awal pendaftaran ulang</td>
					<td class="field"><?php $form->date('reregistration_start_date') ?></td>
				</tr>
				<tr>
					<td class="label">Batas akhir pendaftaran ulang</td>
					<td class="field"><?php $form->date('reregistration_finish_date') ?></td>
				</tr>
				<tr>
					<td class="label">Tanggal seleksi</td>
					<td class="field">
					<div id="selection-date"><?php $form->date('selection_date') ?></div>
					<?php $form->checkbox('selection_date_follows_national') ?> Ikuti Kantor Nasional
					</td>
				</tr>
				<tr>
					<td class="label"><label for="announcement_date">Tanggal pengumuman</label></td>
					<td class="field">
					<div id="announcement-date"><?php $form->date('announcement_date') ?></div>
					<?php $form->checkbox('announcement_date_follows_national') ?> Ikuti Kantor Nasional
					</td>
				</tr>
				<tr>
					<td class="label"></td>
					<td class="field"><button type="submit">Simpan</button></td>
				</tr>
			</table>
		</section>
		<section class="info">
			<p class="count"><strong><?php echo $participant_count ?></strong> peserta</p>
			<p><a href="<?php L(array('action' => 'view_batch', 'id' => $batch_id)) ?>">Lihat pembagian ruangan</a></p>
			<p><a href="<?php L(array('action' => 'delete_batch', 'id' => $batch_id)) ?>">Batalkan gelombang ini</a></p>
		</section>
	</form>
</div>
<?php $this->footer(); ?>
<?php $this->header(''); ?>
<script src="<?php L('/assets/js/jquery-1.7.2.min.js') ?>"></script>
<div class="container">
	<?php if ($error == 'db_fail'): ?>
	<div class="message error">
		<header><i>Database failure</i></header>
		<p>Entri data gagal. Silakan mencoba kembali</p>
	</div>
	<?php endif; ?>
	
	<?php if (!$stage): ?>
	<section class="intro">
		<h1>Pemasukan data kelulusan</h1>
		<p>Silakan masukkan nomor-nomor peserta yang lulus Seleksi Tahap Ketiga.</p>
		<p>Pada halaman berikutnya, Kakak akan diberi kesempatan untuk memastikan keabsahan dari data yang Kakak masukkan.</p>
		<?php if ($this->get_batch_count() == 1): ?>
		<p><?php echo $this->user->chapter->get_title(); ?> sudah memasukkan daftar kelulusan sebelumnya. Isi formulir ini untuk menambahkan gelombang pengumuman kelulusan baru, atau <a href="<?php L(array('action' => 'index')) ?>">edit yang sudah ada</a>.</p>
		<?php endif; ?>
	</section>
	
	<section class="form">
		<form action="<?php L(array('action' => 'announce')) ?>" method="POST">
			<table class="form-table flat">
				<?php if ($this->user->capable_of('national_admin')): ?>
				<tr class="chapter-row">
					<td class="label"><label for="chapter_id">Chapter</label></td>
					<td class="field"><?php $form->select('chapter_id', $chapters, 'medium-short'); ?></td>
				</tr>
				<?php endif; ?>
				<tr>
					<td class="label"><label for="test_ids">Nomor peserta yang lulus Seleksi Tahap Ketiga</label></td>
					<td class="field">
					<?php $form->textarea('test_ids', 'large') ?><br>
					<span class="instruction">Salin langsung satu kolom dari Excel, atau pisahkan dengan spasi, tanda koma, atau baris baru.</span>
					</td>
				</tr>
				<tr>
					<td class="label"></td>
					<td class="field"><button type="submit">Lanjutkan</button></td>
				</tr>
			</table>
		</form>
	</section>
	
	<?php elseif ($stage == 'confirm'): ?>
		<section class="intro">
			<h1><?php echo (int) count($participants); ?> peserta</h1>

			<p>Periksa kembali data peserta di sebelah kanan. Jika sesuai dengan keputusan Dewan Juri Chapter, tekan Simpan.</p>
			
			<form action="<?php L(array('action' => 'announce')) ?>" method="POST">
				<p>
				<input type="hidden" name="token" value="<?php echo $token ?>">
				<button type="submit">Simpan</button>
			</form>
		</section>

		<section class="form list">
			<?php if ($participants): ?>
			<table class="participants">
				<?php $i = 0; foreach ($participants as $participant): ?>
				<tr>
					<td class="no"><?php echo ++$i; ?></td>
					<td class="test-id"><?php echo $participant->test_id ?></td>
					<td class="full-name"><?php echo $participant->sanitized_full_name ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php else: ?>
			<p>Tidak ada peserta yang ditemukan.</p>
			<?php endif; ?>
		</section>
	<?php endif; ?>
</div>
<?php $this->footer(); ?>
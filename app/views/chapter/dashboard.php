<?php $this->print_header() ?>

<header class="page-header">
	<h2>Dashboard</h2>
	<h1><?php echo $chapter->get_title() ?></h1>
</header>

<div class="chapter-dashboard">
	<h3 class="current-phase">
		<?php switch ($current_phase) {
			case 'registration':
				echo 'Pendaftaran <small>1 Maret &ndash; 14 April 2013</small>';
				break;
		} ?>
	</h3>
	<div class="row">
		<div class="span3">
			<h4>PIN Pendaftaran</h4>
			<table class="table counts">
				<tr>
					<td rowspan="3">
						<strong><?php echo $code_count ?></strong> tercetak
					</td>
					<td>
						<strong><?php echo $activated_code_count ?></strong> terpakai
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo $available_code_count ?></strong> tersedia
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo $expired_code_count ?></strong> kadaluarsa
					</td>
				</tr>
			</table>
			<p class="btn-group">
				<a class="btn btn-primary" href="<?php L(array('controller' => 'registration_code', 'action' => 'issue', 'chapter_id' => $id)) ?>">Terbitkan PIN Baru</a>
				<a class="btn" href="<?php L(array('controller' => 'registration_code', 'action' => 'index', 'chapter_id' => $id)) ?>">Daftar PIN</a>
			</p>
			<form action="<?php L(array('controller' => 'registration_code', 'action' => 'recover')) ?>">
				<p class="input-append">
					<input type="text" class="medium-short" value="" name="pin" placeholder="PIN Pendaftaran">
					<button type="submit" class="btn">Lacak</button>
				</p>
			<p></p>
			</form>
		</div>
		<div class="span3">
			<h4>Contact Person Chapter</h4>
			<p>
				<strong><?php echo $contact_person_name ? $contact_person_name : '(Belum ada)'; ?></strong>
				<?php if ($contact_person_phone) echo ' &ndash; ' . $contact_person_phone; ?>
			</p>
			<p>Pendaftar akan diinstruksikan untuk mengumpulkan berkas ke:</p>
			<p>
				<strong><?php echo $chapter->get_title() ?></strong><br>
				<?php echo nl2br($chapter_address) ?>
			</p>
			<p><a href="#" class="btn btn-small">Ubah informasi ini</a></p>
		</div>
		<div class="span6">

			<?php if ($current_phase == 'registration'): ?>

			<h4>Jumlah Pendaftar</h4>

			<table class="table counts">
				<tr>
					<td rowspan="4">
						<span title="Jumlah orang yang sudah pernah mengaktifkan PIN pendaftaran dan membuat akun"><strong><?php echo $total_applicant_count ?></strong> mendaftar</span>
					</td>
					<td rowspan="3">
						<span title="Jumlah orang yang akun pendaftarannya tidak kadaluarsa"><strong><?php echo $active_applicant_count ?></strong> aktif</span>
					</td>
					<td rowspan="2">
						<span title="Jumlah orang yang akun pendaftarannya tidak kadaluarsa"><strong><?php echo $finalized_applicant_count ?></strong> sudah finalisasi</span>
					</td>
					<td>
						<span title="Jumlah orang yang akun pendaftarannya tidak kadaluarsa"><strong><?php echo $confirmed_applicant_count ?></strong> terkonfirmasi</span>
					</td>
				</tr>
				<tr>
					<td>
						<span title="Jumlah orang yang akun pendaftarannya tidak kadaluarsa"><strong><?php echo $not_yet_confirmed_applicant_count ?></strong> belum konfirmasi</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span title="Jumlah orang yang akun pendaftarannya tidak kadaluarsa"><strong><?php echo $incomplete_applicant_count ?></strong> masih mengisi</span>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<span title="Jumlah orang yang akun pendaftarannya sudah kadaluarsa"><strong><?php echo $expired_applicant_count ?></strong> kadaluarsa</span>
					</td>
				</tr>
			</table>

			<p>
				<a class="btn" href="<?php L(array('action' => 'applicants')) ?>">Pengelolaan Peserta</a>
			</p>

			<?php endif; ?>

		</div>
	</div>
</div>

<div class="alert">Kakak sedang berada pada tampilan baru dashboard chapter. <a href="/chapter/view">Gunakan tampilan lama.</a></div>
<?php
$this->require_js('dashboard');
$this->print_footer()
?>
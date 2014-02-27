<?php $this->print_header('Pengumpulan Berkas') ?>
<header class="page-header">
	<h2>Tahap 4 dari 4</h2>
	<h1>Pengumpulan Berkas</h1>
</header>
<!--
<nav class="actions-nav expleft">
	<ul>
		<?php if ($is_preview): ?>
		<li><a href="<?php L(array('controller' => 'chapter', 'action' => 'edit', 'id' => $back_to_chapter_id)) ?>">&laquo; Kembali mengedit informasi chapter</a></li>
		<?php else: ?>
		<li><a href="<?php L(array('action' => 'details')) ?>">Lihat Formulir Pendaftaran</a></li>
		<?php if (!$applicant->is_expired()): ?>
		<li class="expires-on">Batas waktu pendaftaran: <span><?php echo $applicant->expires_on->format('l, j F Y') ?></span></li>
		<?php endif; ?>
		<?php endif; ?>
	</ul>
</nav>
-->
<div class="finalized">
	<div class="row">
		<section class="span6 what">
			<p>Untuk menyelesaikan pendaftaran seleksi Bina Antarbudaya, Adik harus mengumpulkan berkas-berkas berikut ini:</p>
			<dl class="files">
				<dt class="card">Tanda Peserta Seleksi</dt>
					<dd class="download-link"><a class="btn" href="<?php L(array('controller' => 'applicant', 'action' => 'card')) ?>"><i class="icon-download-alt"></i> Unduh</a></dd>
					<dd class="details">
						<ul>
							<li>Dicetak di kertas HVS A4 dan digunting pada garis yang ditentukan</li>
							<li>Tidak boleh dilaminasi</li>
							<li>Ditandatangan dan distempel oleh panitia, kemudian dikembalikan kepada peserta</li>
							<li>Tidak berlaku jika tidak ditandatangan dan distempel oleh panitia</li>
						</ul>
					</dd>
				<dt class="parents-statement">Surat Pernyataan Orang Tua</dt>
					<dd class="download-link"><a class="btn" href="<?php L(array('controller' => 'applicant', 'action' => 'file', 'file' => 'parents_statement')) ?>"><i class="icon-download-alt"></i> Unduh</a></dd>
					<dd class="details">
						<ul>
							<li>Dicetak di kertas HVS A4</li>
							<li>Ditulis tangan serta ditanda tangan asli oleh orang tua siswa</li>
							<li>Dikumpulkan kepada panitia</li>
						</ul>
					</dd>
				<?php if ($applicant->in_acceleration_class): ?>
				<dt class="acceleration-statement">Surat Pernyataan Siswa Akselerasi</dt>
					<dd class="download-link">
						<a class="btn" href="<?php L(array('controller' => 'applicant', 'action' => 'file', 'file' => 'acceleration_statement')) ?>">
							<i class="icon-download-alt"></i> Unduh
						</a>
					</dd>
					<dd class="details">
						<ul>
							<li>Khusus untuk siswa kelas akselerasi</li>
							<li>Dicetak di kertas HVS A4</li>
							<li>Ditulis tangan serta ditanda tangan asli oleh siswa</li>
							<li>Dikumpulkan kepada panitia</li>
						</ul>
					</dd>
				<?php endif; ?>
			</dl>
		</section>

		<section class="span6 how">
			<p>Berkas-berkas tersebut dikumpulkan<?php if ($applicant->chapter->use_verification_deadline): ?> paling lambat <time datetime="<?php echo $applicant->expires_on->format(DateTime::W3C) ?>"><?php echo $applicant->expires_on->format('l, j F Y') ?></time><?php endif; ?> ke:</p>
			<dl class="depots">
				<?php
				$first = true;
				
				// dummy
				$dummy = new StdClass;
				$dummy->depot_address = $applicant->chapter->chapter_address;
				$dummy->mon_open = $dummy->mon_close = 
				$dummy->tue_open = $dummy->tue_close = 
				$dummy->wed_open = $dummy->wed_close = 
				$dummy->thu_open = $dummy->thu_close = 
					'';
				$dummy->fri_open = '14:00';
				$dummy->fri_close = '18:00';
				$dummy->sat_open = $dummy->sun_open = '13:00';
				$dummy->sat_close = $dummy->sun_close = '17:00';
				$dummy->is_default = true;
				
				$default_depot_name = $applicant->chapter->get_title();
				$default_depot_address = $applicant->chapter->chapter_address;
				
				$depots = array($dummy);
				$depots = $applicant->chapter->depots;
				$first = true;
				if (!$depots)
					$depots = array(array());
				foreach ($depots as $depot):
					if ($depot['disabled'])
						continue;
					$depot_name = $depot['name'] ? $depot['name'] : $default_depot_name;
					$depot_address = $depot['name'] ? $depot['address'] : $default_depot_address;
					$depot_mappable_address = str_replace("\n", ', ', $depot_address) . ', Indonesia';
				?>
				<dt class="<?php echo $first ? 'primary' : 'secondary'; $first = false; ?>"><?php echo $depot_name ?></dt>
					<dd class="map">
						<?php $params = array('markers' => $depot_mappable_address, 'size' => '458x120', 'sensor' => 'false', 'scale' => 2); $params_enc = http_build_query($params, '', '&amp;') ?>
						<a href="http://maps.google.com/maps?q=<?php echo urlencode($depot_mappable_address) ?>,%20Indonesia" title="Peta menuju <?php echo htmlspecialchars($depot_name) ?>"><img src="http://maps.googleapis.com/maps/api/staticmap?<?php echo $params_enc ?>" alt="Peta menuju <?php echo htmlspecialchars($depot_name) ?>" width="458" height="120"></a>
					</dd>
					<dd class="address">
						<h4>Alamat</h4>
						<address><?php echo nl2br($depot_address) ?></address>
					</dd>
					<dd class="schedule">
						<h4>Jadwal Pengumpulan</h4>
						<ul>
							<?php
							$days = array('monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu');
							foreach ($days as $d => $day):
								$times = str_replace('-', '&ndash;', $depot[$d]);
								if ($times):
							?>
							<li><strong><?php echo $day ?></strong> pukul <strong><?php echo $times ?></strong></li>
							
							<?php endif; endforeach; ?>
						</ul>
					</dd>
				<?php endforeach; ?>
			</dl>
		</section>
	</div>
</div>
<section class="then">
	<?php
	$c = $applicant->chapter;
	if ($c->contact_person_phone): ?>
	<p>Untuk informasi selengkapnya, silakan hubungi<?php if ($cp = $c->contact_person_name) echo '<strong> Kak ' . $cp . '</strong> di '; echo "<strong>{$c->contact_person_phone}</strong>" ?>.</p>
	<?php else: ?>
	<p>Kembali ke laman ini jika Adik sudah mengumpulkan berkas-berkas tersebut.</p>
	<?php endif; ?>
</section>
<?php if ($is_preview): ?>
	<style>.what { opacity: 0.2 }</style>
<?php endif; ?>
</div>
<?php $this->print_footer(); ?>
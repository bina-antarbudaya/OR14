<?php $this->header(); ?>
<div class="container">
	<section class="announcement-form">
		<?php if ($this->params['not_found']): ?>
		<div class="message error">
			<p>Peserta tidak ditemukan.</p>
		</div>
		<?php endif; ?>
		<header>
			<h1>Pengumuman Hasil Seleksi</h1>
		</header>
		<form action="<?php L(array("controller" => "applicant", "action" => "results")) ?>" method="POST">
			<table class="form-table">
				<tr>
					<td class="label">Nomor Peserta</td>
					<td class="field"><input type="text" class="medium" value="YBA/YP15-16/XXX/YYYY" placeholder="YBA/YP15-16/XXX/YYYY" name="test_id"><?php // $form->text('test_id') ?></td>
				</tr>
				<tr>
					<td class="label">Tanggal Lahir</td>
					<td class="field"><?php $form->date('dob') ?></td>
				</tr>
				<tr>
					<td class="label"></td>
					<td class="field"><input type="hidden" name="on_fail_go_to" value="<?php L(array('controller' => 'home', 'action' => 'results', 'not_found' => 1)) ?>"><button type="submit">Buka</button></td>
				</tr>
			</table>
		</form>
	</section>
</div>
<?php $this->footer(); ?>
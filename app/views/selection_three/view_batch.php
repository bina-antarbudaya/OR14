<?php $this->header() ?>
<div class="container">
	<section class="personality">
		<header><h1>Kepribadian</h1></header>
		
		<?php foreach ($p as $n => $v): ?>
		<article class="chamber">
			<header>
				<h1>P<?php echo str_pad($n, 2, '0', STR_PAD_LEFT) ?></h1>
			</header>
			<table class="participants">
				<?php foreach ($v['order'] as $o => $r): ?>
				<tr>
					<td class="no"><?php echo $o ?></td>
					<td class="test-id"><?php echo $r['test_id'] ?></td>
					<td class="full-name"><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $r['applicant_id'])) ?>"><?php echo $r['full_name'] ?></a></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</article>
		<?php endforeach; ?>
	</section>
	
	<section class="personality">
		<header><h1>Bahasa Inggris</h1></header>
		
		<?php foreach ($e as $n => $v): ?>
		<article class="chamber">
			<header>
				<h1>E<?php echo str_pad($n, 2, '0', STR_PAD_LEFT) ?></h1>
			</header>
			<table class="participants">
				<?php foreach ($v['order'] as $o => $r): ?>
				<tr>
					<td class="no"><?php echo $o ?></td>
					<td class="test-id"><?php echo $r['test_id'] ?></td>
					<td class="full-name"><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $r['applicant_id'])) ?>"><?php echo $r['full_name'] ?></a></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</article>
		<?php endforeach; ?>
	</section>
</div>
<?php $this->footer() ?>
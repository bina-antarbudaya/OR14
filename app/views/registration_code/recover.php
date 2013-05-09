<?php $this->header() ?>
<div class="container">
	<?php if ($pin): ?>
	<h1><?php echo $this->params['pin'] ?></h1>
	<?php if (!$token): ?>
	<p>PIN tersebut <strong>tidak ditemukan</strong>.</p>
	<?php elseif (!$applicant_id): ?>
	<p>PIN tersebut <strong>belum diaktifkan</strong>.</p>
	<p>Masa berlaku PIN: <strong><?php echo $expires_on->format('j F Y'); ?></strong></p>
	<?php else: ?>
	<p>Username: <a href="<?php L(array('controller' => 'user', 'action' => 'view', 'id' => $user_id))?>"><?php echo $username ?></a></p>
	<p><a href="<?php L(array('controller' => 'user', 'action' => 'view', 'id' => $user_id))?>">Edit akun atau ubah password</a></p>
	<p><a href="<?php L(array('controller' => 'applicant', 'action' => 'view', 'id' => $applicant_id))?>">Edit pendaftar</a></p>
	<p>Masa berlaku PIN: <strong><?php echo $expires_on->format('j F Y'); ?></strong></p>
	<?php endif; ?>
	<?php endif; ?>
	<form action="<?php L(array('action' => 'recover')) ?>" method="get"><input type="text" class="medium" name="pin" maxlength="16" placeholder="Masukkan PIN Pendaftaran"> <button type="submit">Lacak</button></form>
</div>
<?php $this->footer() ?>

<?php

$the_title = 'Pendaftaran Seleksi Bina Antarbudaya';
if ($page_title)
	$the_title = $page_title . ' | ' . $the_title;

$controller = $this->params['controller'];

$controller_css = $controller . '/style';
$action_css = $controller . '/' . $this->_action();

?>
<!DOCTYPE html>

<html lang="id">

	<head>
		<meta charset="utf-8">
		<title><?php echo $the_title; ?></title>
		<link rel="icon" href="<?php L('/assets/icon.png'); ?>" type="image/png">
		<link rel="stylesheet" href="<?php L('/assets/css/deprecated/global/style.css'); ?>">
		<link rel="stylesheet" href="<?php L('/assets/css/deprecated/' . $controller_css . '.css'); ?>">
		<link rel="stylesheet" href="<?php L('/assets/css/deprecated/' . $action_css . '.css'); ?>">
		<?php if ($css = $this['additional_css']): foreach ($css as $s): ?>
		<link rel="stylesheet" href="<?php L('/assets/' . $s . '.css'); ?>">
		<?php endforeach; endif; ?>

		<script src="<?php L('/assets/js/jquery-1.7.2.min.js'); ?>"></script>
		<script src="<?php L('/assets/js/bootstrap.js'); ?>"></script>
	</head>

	<body>
		<header class="global-header">
			<div class="container">
				<header class="masthead"><a href="<?php L($this->is_logged_in() ? $this->session->user->get_landing_page() : ''); ?>"><img src="<?php L('/assets/css/deprecated/global/masthead.png'); ?>" alt="Bina Antarbudaya" width="226" height="40"></a></header>
				<?php if ($this->is_logged_in()): ?>
				<ul class="user-controls">
					<li class="username"><?php echo $this->session->user->capable_of('chapter_admin') ? $this->user->chapter->get_title() : $this->session->user->username; ?></li>
					<li class="chapter"><a href="<?php L($this->is_logged_in() ? $this->session->user->get_landing_page() : ''); ?>"><?php echo $this->session->user->capable_of('chapter_admin') ? 'Dashboard' : (!$this->can_register() ? 'Pengumuman Seleksi' : 'Pendaftaran') ?></a></li>
					<li class="prefs"><a href="<?php L(array('controller' => 'user', 'action' => 'prefs')); ?>">Pengaturan</a></li>
					<li class="logout"><a href="<?php L(array('controller' => 'auth', 'action' => 'logout')); ?>">Logout</a></li>
				</ul>
				<?php elseif ($controller != 'auth'): ?>
				<ul class="user-controls">
					<!-- <strong class="activate-link"><a href="<?php L(array('controller' => 'applicant', 'action' => 'redeem')) ?>">Aktifkan PIN pendaftaran</a></strong> -->
					<li class="login">Sudah memiliki akun? &nbsp;<a href="<?php L(array('controller' => 'auth', 'action' => 'login')); ?>" class="login">Login &raquo;</a></li>
				</ul>
				<?php endif; ?>
			</div>
		</header>
		<div class="content">

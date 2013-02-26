<?php

$the_title = 'Pendaftaran Seleksi Bina Antarbudaya';
if ($page_title)
	$the_title .= ': ' . $page_title;

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
		<link rel="stylesheet" href="<?php L('/assets/css/style.css'); ?>">
		<?php foreach ($this->additional_css as $css): ?>
		<link rel="stylesheet" href="<?php L('/assets/css/' . $css . '.css'); ?>">
		<?php endforeach; ?>
	</head>

	<body>
		
		<header class="global-header">
			
			<div class="container">
				<h1 class="branding">
					<a href="<?php L($this->is_logged_in() ? $this->session->user->get_landing_page() : ''); ?>">Bina Antarbudaya</a>
				</h1>

				<div class="actions">
				<?php if ($this->is_logged_in()): ?>
					<nav class="global-nav logged-in">
						<ul>
							<li class="username">
								<i class="icon-user icon-white"></i>
								<?php
								echo $this->session->user->capable_of('chapter_admin') ?
									$this->user->chapter->get_title() :
									$this->session->user->username;
								?>
							</li>
							<?php /* TODO clean this up */ ?>
							<li class="continue">
							<?php if ($this->session->user->capable_of('chapter_admin')): ?>
								<i class="icon-home icon-white"></i>
								<a href="<?php L($this->session->user->get_landing_page()); ?>">
									Dashboard
								</a>
							<?php elseif ($this->user->applicant->confirmed): ?>
								<i class="icon-certificate icon-white"></i>
								<a href="<?php L($this->session->user->get_landing_page()); ?>">
									Pengumuman
								</a>
							</li>
							<li>
								<i class="icon-download-alt icon-white"></i>
								<a href="<?php L(array('controller' => 'applicant', 'action' => 'transcript')) ?>" data-toggle="dropdown">
									Transkrip Formulir
								</a>
							<?php else: ?>
								<i class="icon-pencil icon-white"></i>
								<a href="<?php L($this->session->user->get_landing_page()); ?>">
									Formulir Pendaftaran
								</a>
							<?php endif; ?>
							</li>
							<li class="prefs">
								<i class="icon-cog icon-white"></i> <a href="<?php L(array('controller' => 'user', 'action' => 'prefs')); ?>">Pengaturan</a>
							</li>
							<li class="logout">
								<i class="icon-off icon-white"></i>
								<a href="<?php L(array('controller' => 'auth', 'action' => 'logout')); ?>">Logout</a>
							</li>
						</ul>
					</nav>
				<?php elseif ($controller != 'auth'): ?>
					
					<a href="<?php L(array('controller' => 'auth', 'action' => 'login')) ?>">Login</a>
				<?php endif;?>
				</div>
			</div>
		</header>

		<div class="content">
			<div class="container">

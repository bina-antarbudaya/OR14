<?php

file_put_contents(HELIUM_PARENT_PATH . '/astaga.log', '[' . date('c') . '] ' . memory_get_usage() . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT'] . ' '
  . $_SERVER["REQUEST_URI"] . "\n", FILE_APPEND);

if (!function_exists('L')) {
	function L($u) {
		echo Helium::conf('base_uri') . $u;
	}
}
?>
<!DOCTYPE html>

<html lang="id">

	<head>
		<meta charset="utf-8">
		<title>Pendaftaran Seleksi Bina Antarbudaya: Formulir Pendaftaran</title>
		<link rel="icon" href="<?php L('/assets/icon.png'); ?>" type="image/png">
		<link rel="stylesheet" href="<?php L('/assets/css/global/style.css'); ?>">
		<link rel="stylesheet" href="<?php L('/assets/css/global/error.css'); ?>">
	</head>

	<body>
		<header class="global-header">
			<div class="container">
				<header class="masthead"><a href="<?php L('/') ?>"><img src="https://www.seleksi.bina-antarbudaya.info/assets/css/global/masthead.png" alt="Bina Antarbudaya" width="226" height="40"></a></header>
		</header>
		<div class="content">
<header class="page-title">
		<h1>Astaga!</h1>
</header>
<div class="container">
	
	<p>Terjadi sebuah kesalahan teknis. Kami akan segera memeriksanya.</p>
	<p>Tweet tentang masalah ini dengan tagar #WaduhCurhat.</p>
	<p><strong><a href="<?php echo $_SERVER['HTTP_REFERER'] ?>">Kembali ke halaman sebelumnya</a></strong></p>
		</div>
			</body>

</html>

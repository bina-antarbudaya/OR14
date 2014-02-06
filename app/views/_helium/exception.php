<?php

// file_put_contents(HELIUM_PARENT_PATH . '/astaga.log', '[' . date('c') . '] ' . memory_get_usage() . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT'] . ' '
//   . $_SERVER["REQUEST_URI"] . "\n", FILE_APPEND);

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
		<title>Pendaftaran Seleksi Bina Antarbudaya</title>
		<link rel="icon" href="<?php L('/assets/icon.png'); ?>" type="image/png">
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,600italic:latin">
		<link rel="stylesheet" href="<?php L('/assets/css/style.css'); ?>">
	</head>

	<body>
		<header class="global-header">
			<div class="container">
				<h1 class="branding">
					<a href="<?php L('/') ?>">Bina Antarbudaya</a>
				</h1>
			</div>
		</header>

		<div class="content">
			<div class="container">
				<header class="page-header">
					<h1>Maaf</h1>
				</header>
				
				<p>Terjadi sebuah kesalahan teknis. Kami akan segera memeriksa penyebabnya.</p>
				<p><a class="btn btn-primary" href="<?php echo $_SERVER['HTTP_REFERER'] ?>">Kembali ke halaman sebelumnya</a></p>
			</div>
		</div>
	</body>

</html>

<?php

function Helium_autoload($class_name) {
	if (Helium::$autoload)
		return Helium::load_class_file($class_name, true);
}

spl_autoload_register('Helium_autoload');

if (file_exists(HELIUM_APP_PATH . '/vendor/autoload.php'))
	require_once HELIUM_APP_PATH . '/vendor/autoload.php';
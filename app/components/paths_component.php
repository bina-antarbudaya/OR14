<?php

// Project Gatotkaca
// Common functions wrapper

class PathsComponent extends HeliumComponent {
	static $cookie_path;
	static $cookie_domain;

	public function init($controller = null) {
		$controller->_alias_method('http_redirect', array($this, 'redirect'));
		$controller->_alias_method(array($this, 'build_url'));
		
		if (Helium::conf('force_https') && !$_SERVER['HTTPS'])
			$this->redirect($_SERVER['REQUEST_URI']);
	}

	public static function redirect($target) {
		if (is_array($target) || strpos($target, ':') === false)
			$target = self::build_url($target);

		@header('Location: ' . $target);

		exit;
	}

	public static function build_url($path) {
		if (is_array($path)) {
			$router = Helium::router();
			$path = Helium::router()->build_path($path);
		}

		$path = ltrim($path, '/');
		$path = '/' . $path;

		if (substr($path, 0, 7) == '/assets') {
			return Helium::conf('assets_public_path') . substr($path, 7);
		}
		else {
			return Helium::conf('base_uri') . $path;
		}
	}
}
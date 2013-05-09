<?php

abstract class AppController extends HeliumController {
	public $components = array('cookies', 'sessions', 'auth', 'links', 'locale', 'paths', 'request');
	
	protected $additional_js = array();
	protected $additional_css = array();	

	/**
	 * Template functions
	 */

	protected function print_header($title = '') {
		$this['page_title'] = $title;
		$this->render('global/header');

		try {
			$controller_name = $this->params['controller'];
			$this->render($controller_name . '/header');
		}
		catch (HeliumException $e) {
			// do nothing
		}
	}
	
	protected function print_footer() {
		try {
			$controller_name = $this->params['controller'];
			$this->render($controller_name . '/footer');
		}
		catch (HeliumException $e) {
			// do nothing
		}

		$this->render('global/footer');
	}
	
	protected function require_css($css_file) {
		$this->additional_css[] = $css_file;
	}
	
	protected function require_js($js_file) {
		$this->additional_js[] = $js_file;
	}

	/**
	 * @deprecated
	 */ 
	protected function header($title = '') {
		$this['page_title'] = $title;
		$this->render('global/old_header');

		try {
			$controller_name = $this->params['controller'];
			$this->render($controller_name . '/header');
		}
		catch (HeliumException $e) {
			// do nothing
		}
	}

	/**
	 * @deprecated
	 */
	protected function footer() {
		try {
			$controller_name = $this->params['controller'];
			$this->render($controller_name . '/footer');
		}
		catch (HeliumException $e) {
			// do nothing
		}

		$this->render('global/old_footer');
	}
	
	protected function actions_nav(array $actions = array()) {
		$this['actions'] = $actions;
		$this->render('global/actions_nav');
	}
	
	protected function can_register() {
		return !Helium::db()->get_var('SELECT COUNT(*) FROM participants');
	}
}
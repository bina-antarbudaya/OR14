<?php

// Helium Framework: Helium
// Exception handler

class HeliumException extends Exception {
	const no_model = 1;
	const no_view = 2;
	const no_controller = 3;
	const no_action = 4;
	const no_class = 5;
	const no_route = 6;
	const no_routes_file = 11;
	const no_component = 7; // components and helpers are factory-loaded
	const no_helper = 8;
	const failed_to_redirect = 9;
	const file_not_found = 10;
	const php_error = 256;
	const db_error = 128;

	public $code = 0;
	public $title = '';
	public $message = 'Unknown error';
	public $db_query = '';
	public $http_status = 500;
	public $file = '';
	public $line = 0;
	public $request = '';
	public $controller = '';
	public $action = '';
	public $params = array();

	public static $net = array();

	public function __construct($code) {
		$this->controller = Helium::$controller;
		$this->action = Helium::$action;
		$this->params = Helium::$params;
		$this->request = Helium::$request;

		// unset($this->params['controller'], $this->params['action']);

		$this->code = $code;

		if ($this->request && $first_slash = strpos($this->request, '/', 1)) {
			$first_dir = substr($this->request, 1, $first_slash - 1);
			$try = Helium::conf('app_path') . $first_dir;
			if ($first_dir && file_exists($try))
				$this->just_do_404($first_dir);
		}

		$args = func_get_args();
		array_shift($args);

		$title = 'Exception caught';
		$message = '';

		// figure out the error message
		// $message willg be sprintf()ized.
		// %1 will be the request
		// %2 will be the controller
		// %3 will be the action
		if (is_int($this->code)) {
			$args = str_replace('%', '%%', $args);

			switch ($this->code) {
			case self::no_model:
				list($model) = $args;
				$table = Inflector::tableize($model);
				$message = "No model defined for table <kbd>$table</kbd>.";
				break;
			case self::no_controller:
				$message = 'No controller defined for <kbd>%2$s</kbd>.';
				break;
			case self::no_action:
				$message = 'No action defined for <kbd>%s</kbd>.';
				break;
			case self::no_view:
				$message = 'No view defined for <kbd>%2$s::%3$s</kbd>.';
				break;
			case self::no_class:
				list($class) = $args;
				$message = "Class <kbd>$class</kbd> does not exist.";
				break;
			case self::no_route:
				$message = "Request <kbd>%s</kbd> cannot be resolved.";
				break;
			case self::no_component:
				list($component) = $args;
				$message = "Component $component is not defined.";
				break;
			case self::no_helper:
				list($helper) = $args;
				$message = "Helper $helper is not defined.";
				break;
			case self::failed_to_redirect:
				list($uri) = $args;
				$message = "Failed to redirect to <kbd>$uri</kbd>";
				break;
			case self::db_error:
				list($message) = $args;
				$title = 'Database error';
				$db = Helium::db();
				$this->db_query = $db->last_query;
				if (!$this->config_file_exists())
					$message = 'Configuration file does not exist.';
				break;
			case self::php_error:
				list($php_error_code, $message, $this->file, $this->line) = $args;
				$php_error_code_map = array(E_ERROR => 'Fatal error',
											E_WARNING => 'Warning',
											E_PARSE => 'Parse error',
											E_NOTICE => 'Warning',
											E_USER_WARNING => 'Warning');
				$error_type = $php_error_code_map[$php_error_code];
				if ($error_type)
					$message = "<strong>$error_type:</strong> $message";
				else
					$message = "<strong>Error code $php_error_code</strong> $message";
				break;
			case self::file_not_found:
				$this->http_status = 404;
				$message = "Static file <kbd>%s</kbd> was not found.";
				break;
			case self::no_routes_file:
				$message = "No routes defined for site.";
				break;
			default:
				$message = 'Unknown error.';
			}
		}
		elseif (is_string($this->code)) {
			$message = $this->code;
		}

		$message = sprintf($message, $this->request, $this->controller, $this->action);
		$this->log_message($message);

		$filename = str_replace('\\', '/', $this->file);
		$filename = $this->filter_filenames($filename);
		$this->formatted_filename = $filename;

		$this->trace = $this->getTrace();
		$this->trace_string = $this->getTraceAsString();

		$this->title = $title;

		$clean_trace = array();
		foreach ($this->trace as $key => $line) {
			if (!$line)
				continue;

			$dummy = array();
			if (is_array($line['args'])) {
				foreach ($line['args'] as $arg) {
					if (is_string($arg)) {
						if (strlen($arg) > 30)
							$arg = substr($arg, 0, 27) . '...';
						$dummy[] = "<code class=\"string\">\"$arg\"</code>";
					}
					else {
						if (is_object($arg))
							$arg = get_class($arg);
						$dummy[] = "<code class=\"value\">$arg</code>";
					}
				}
			}
			$line['args'] = $this->filter_filenames(implode('<br/>', $dummy));

			$line['file'] = str_replace('\\', '/', $line['file']);
			$line['file'] = $this->filter_filenames($line['file']);

			$clean_trace[$key] = $line;
		}

		$this->formatted_trace = $clean_trace;
	}

	private function filter_filenames($filename = '') {
		$sensitives = array('helium_path' => 'helium', 'app_path' => 'app', 'parent_path' => 'parent');
		foreach ($sensitives as $sensitive => $token) {
			$search = array(Helium::conf($sensitive), substr(Helium::conf($sensitive), 0, 27) . '...');
			$replace = array('<kbd class="variable">' . $token . '</kbd>', '<kbd class="variable">parent</kbd>...');
			$filename = str_replace($search, $replace, $filename);
		}

		return $filename;
	}

	private function config_file_exists() {
		$config_file = Helium::conf('config_file');
		return file_exists($config_file);
	}

	private function log_message($message) {
		self::$net[] = $message;
		$this->message = $message;
	}

	public function output() {
		$this->send_http_status();
		$formatted_trace = $this->formatted_trace;
		$params = $this->params;
		$message = $this->filter_filenames($this->message);
		$formatted_filename = $this->formatted_filename;
		$line = $this->line;
		$title = $this->title;
		$db_query = $this->db_query;
		$production = Helium::conf('production');
		if (is_string($production) && file_exists(HELIUM_APP_PATH . '/views' . $production))
			require HELIUM_APP_PATH . '/views' . $production;
		elseif ($production && file_exists(HELIUM_APP_PATH . '/views/_helium/exception.php'))
			require HELIUM_APP_PATH . '/views/_helium/exception.php';
		else
			require 'debugger/debugger.php';
		exit;
	}

	private function send_http_status($status = null) {
		$status = $status ? $status : $this->http_status;

		$statuses = array(401 => 'Unauthorized',
						  403 => 'Forbidden',
						  404 => 'Not Found',
						  405 => 'Method Not Allowed',
						  500 => 'Internal Server Error');
		$message = $statuses[$status];

		if (!headers_sent())
			@header("HTTP/1.1 $status $message");
	}

	private function just_do_404($dir) {
		switch ($dir) {
		case Helium::conf('stylesheets_dir'):
			$this->send_http_status(404);
			@header('Content-type: text/css');
			echo "/* file not found */";
			exit;
		case Helium::conf('javascripts_dir'):
			$this->send_http_status(404);
			@header('Content-type: application/x-javascript');
			echo "// file not found";
			exit;
		default:
			$this->code = self::file_not_found;
		}
	}
}

set_error_handler(
					function ($code, $message, $file, $line) {
						throw new HeliumException(HeliumException::php_error, $code, $message, $file, $line);
					},
					E_ALL ^ E_NOTICE
				);
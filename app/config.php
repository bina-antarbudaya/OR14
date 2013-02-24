<?php

// HeliumConfiguration
// Edit this class to set global configuration variables for your application

define('PICTURE_UPLOAD_PATH', HELIUM_PARENT_PATH . '/uploads');
define('HELIUM_LOG_FILE', HELIUM_PARENT_PATH . '/helium.log');


class HeliumConfiguration extends HeliumDefaults {

	/* Application configuration */
	public $app_name = 'ramayana';	// name of application
	public $production = false;		// set to true to disable debugging
	// public $enable_reactor = false;	// true to enable Reactor
	
	public $base_uri = 'http://skynet.bina-antarbudaya.info';
	// public $force_https = true;

	public $log_file = HELIUM_LOG_FILE;

	public $session_cookie_name = 'gatotkaca';
	public $session_length = '1 week';
	public $session_check_password_hash = true;
	public $session_check_user_agent = true;
	public $session_check_ip_address = false;
	
	public $picture_upload_path = PICTURE_UPLOAD_PATH;
	public $picture_public_path = 'http://skynet.bina-antarbudaya.info/uploads';
	
	public $db_user = 'username';	// username
	public $db_pass = 'password';	// password
	public $db_name = 'skynetx';	// database name – optional; defaults to $db_user
	public $db_host = 'localhost';	// database server – optional; defaults to localhost
	
	public $site_timezone = 'Asia/Jakarta';
	public $recaptcha_public_key = '6LeuJ8ISAAAAAI8CHltLLYj6i-SuNOaYOd9hgfVc';
	public $recaptcha_private_key = '6LeuJ8ISAAAAANw4cl3_OSQUG5sDrEs1fmQPv0qh';
	
	// -- @deprecated --
	public $program_year = 2015;
	public $registration_deadline = '2013-04-15 23:59:59';
	public $selection_one_date = '2013-04-28';
	public $selection_two_date = '2013-05-19';
	public $selection_three_date = '2013-06-09';
	public $selection_one_announcement_date = '2013-05-03 00:00:00'; // chapter time
	public $selection_two_announcement_date = '2013-05-13 00:00:00'; // chapter time
	public $selection_three_announcement_date = '2013-05-13 00:00:00'; // chapter time
	
	public $applicant_prefix = "INAYPSc/14-15/";
	public $programs = array('afs', 'yes');
}
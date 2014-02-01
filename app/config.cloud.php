<?php

/**
 * Configuration file for cloud deployments
 *
 * The settings in this file would be used for production.
 * As such, aside from the version you see here, this file is not version-controlled.
 */

class HeliumConfiguration extends HeliumDefaults {

	/* Application configuration */
	public $app_name = 'or14';	// name of application
	public $production = true;		// set to true to disable debugging
	// public $enable_reactor = false;	// true to enable Reactor
	
	public $base_uri = 'http://localhost';
	// public $force_https = true;

	public $log_file = HELIUM_LOG_FILE;

	// Session settings
	public $session_cookie_name = 'or14';
	public $session_length = '1 week';
	public $session_check_password_hash = true;
	public $session_check_user_agent = true;
	public $session_check_ip_address = false;

	// Azure-specific settings
	public $azure_storage_connection_string = 'UseDevelopmentStorage=true';

	// Replace with CDN endpoints
	public $picture_public_path = 'http://localhost/uploads';
	public $assets_public_path = 'http://localhost/assets';
	
	public $db_user = 'root';	// username
	public $db_pass = '';	// password
	public $db_name = 'seleksi';	// database name – optional; defaults to $db_user
	public $db_host = 'localhost';	// database server – optional; defaults to localhost
	
	public $site_timezone = 'Asia/Jakarta';
	public $recaptcha_public_key = '6LeuJ8ISAAAAAI8CHltLLYj6i-SuNOaYOd9hgfVc';
	public $recaptcha_private_key = '6LeuJ8ISAAAAANw4cl3_OSQUG5sDrEs1fmQPv0qh';

	public $program_year = 2016;
	public $registration_start = '2014-03-01 00:00:00';
	public $registration_deadline = '2014-04-14 23:59:59';
	public $selection_one_date = '2014-04-28';
	public $selection_two_date = '2014-05-19';
	public $selection_three_date = '2014-06-09';
	public $selection_one_announcement_date = '2014-05-03 00:00:00'; // chapter time
	public $selection_two_announcement_date = '2014-05-13 00:00:00'; // chapter time
	public $selection_three_announcement_date = '2014-05-13 00:00:00'; // chapter time

	public $programs = array('afs', 'yes');

	// @deprecated
	public $picture_upload_path = PICTURE_UPLOAD_PATH;
}
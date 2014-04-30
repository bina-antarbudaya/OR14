<?php

define('PICTURE_UPLOAD_PATH', HELIUM_PARENT_PATH . '/uploads');
define('HELIUM_LOG_FILE', HELIUM_PARENT_PATH . '/helium.log');

define('HOST', $_SERVER['HTTP_HOST']);
define('BASE_URI', 'http://' . HOST);
define('ASSETS_URI', BASE_URI . '/assets');

/**
 * Common app configuration
 */
class HeliumCommonConfiguration extends HeliumDefaults {

	// Basic app settings
	public $app_name = 'or14';		// app identifier
	public $production = false;		// set to true to disable debugging
	
	// URI settings
	public $base_uri = BASE_URI;
	public $force_https = false;

	// Log settings
	public $log_file = HELIUM_LOG_FILE;

	// Session settings
	public $session_cookie_name = 'or14';
	public $session_length = '20 minutes';
	public $session_check_password_hash = true;
	public $session_check_user_agent = true;
	public $session_check_ip_address = false;

	// Azure-specific settings
	public $use_azure_storage = true;
	public $azure_storage_connection_string = 'UseDevelopmentStorage=true';
	public $azure_storage_picture_container = 'uploads';

	// Replace with CDN endpoints
	public $picture_public_path = 'http://127.0.0.1:10000/devstoreaccount1/uploads';
	public $assets_public_path = ASSETS_URI;
	
	// Database settings
	public $db_user = 'root';	// username
	public $db_pass = '';	// password
	public $db_name = 'seleksi';	// database name – optional; defaults to $db_user
	public $db_host = 'localhost';	// database server – optional; defaults to localhost
	
	// Timezone settings
	public $site_timezone = 'Asia/Jakarta';

	// reCAPTCHA keys
	public $recaptcha_public_key = '6LeuJ8ISAAAAAI8CHltLLYj6i-SuNOaYOd9hgfVc';
	public $recaptcha_private_key = '6LeuJ8ISAAAAANw4cl3_OSQUG5sDrEs1fmQPv0qh';

	// SendGrid settings
	public $sendgrid_username = '<sendgrid username>';
	public $sendgrid_password = '<sendgrid password>';
	public $from_address = 'help@localhost';
	public $from_name = 'Bina Antarbudaya';

	// Dates
	public $program_year = 2016;
	public $registration_start = '2014-03-01 00:00:00';
	public $registration_deadline = '2014-04-13 23:59:59';
	public $selection_one_date = '2014-04-28';
	public $selection_two_date = '2014-05-19';
	public $selection_three_date = '2014-06-09';
	public $selection_one_announcement_date = '2014-05-10 00:00:00'; // chapter time
	public $selection_two_announcement_date = '2014-06-30 00:00:00'; // chapter time, NOT YET DECIDED
	public $selection_three_announcement_date = '2014-06-30 00:00:00'; // chapter time, NOT YET DECIDED
	public $results_deadline_selection_1 = '2014-05-07';
	public $results_deadline_selection_2 = '2014-06-30';
	public $results_deadline_selection_3 = '2014-06-30';

	public $programs = array('afs', 'yes');
	public $partners = array(
						'americas' => array(
							'ARG' => 'Argentina',
							'BRA' => 'Brazil',
							'CAN' => 'Canada',
							'MEX' => 'Mexico',
							'USA' => 'Amerika Serikat'
						),
						'europe' => array(
							'BEL' => 'Belgia',
							'CZE' => 'Republik Ceko',
							'DEN' => 'Denmark',
							'FIN' => 'Finlandia',
							'FRA' => 'Perancis',
							'GER' => 'Jerman',
							'HUN' => 'Hungaria',
							'ISL' => 'Islandia',
							'ITA' => 'Italia',
							'NED' => 'Belanda',
							'NOR' => 'Norwegia',
							'RUS' => 'Rusia',
							'SUI' => 'Swiss',
							'SWE' => 'Swedia',
							'TUR' => 'Turki'
						),
						'asia' => array(
							'CHN' => 'China',
							'IND' => 'India',
							'JPN' => 'Jepang',
							'PHI' => 'Filipina',
							'THA' => 'Thailand'
						)
					);

	// PIWIK Settings
	public $piwik_server = 'analytics.bina-antarbudaya.or.id';

	// @deprecated
	public $picture_upload_path = PICTURE_UPLOAD_PATH;
}

if (defined('CLOUD')) {
	require __DIR__ . '/config.cloud.php';
}
else {
	class HeliumConfiguration extends HeliumCommonConfiguration { }
}
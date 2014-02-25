<?php

/**
 * Configuration settings for non-cloud deployment
 */

define('PICTURE_UPLOAD_PATH', HELIUM_PARENT_PATH . '/uploads');
define('HELIUM_LOG_FILE', HELIUM_PARENT_PATH . '/helium.log');

if (defined('CLOUD')) {
	require __DIR__ . '/config.cloud.php';
}
else {
	class HeliumConfiguration extends HeliumDefaults {

		/* Application configuration */
		public $app_name = 'or14';	// name of application
		public $production = false;		// set to true to disable debugging
		// public $enable_reactor = false;	// true to enable Reactor
		
		public $base_uri = 'http://localhost';
		// public $force_https = true;

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
		public $assets_public_path = 'http://localhost/assets';
		
		public $db_user = 'root';	// username
		public $db_pass = '';	// password
		public $db_name = 'seleksi';	// database name – optional; defaults to $db_user
		public $db_host = 'localhost';	// database server – optional; defaults to localhost
		
		public $site_timezone = 'Asia/Jakarta';
		public $recaptcha_public_key = '6LeuJ8ISAAAAAI8CHltLLYj6i-SuNOaYOd9hgfVc';
		public $recaptcha_private_key = '6LeuJ8ISAAAAANw4cl3_OSQUG5sDrEs1fmQPv0qh';

		public $sendgrid_username = '<sendgrid username>';
		public $sendgrid_password = '<sendgrid password>';
		public $from_address = 'help@localhost';
		public $from_name = 'Bina Antarbudaya';

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
		public $partners = array(
							'americas' => array(
								'ARG' => 'Argentina',
								'BRA' => 'Brazil',
								'CAN' => 'Kanada',
								'MEX' => 'Meksiko',
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
								'RUS' => 'Russia',
								'SUI' => 'Swiss',
								'SWE' => 'Swedia',
								'TUR' => 'Turki'
							),
							'asia' => array(
								'CHN' => 'Cina',
								'IND' => 'India',
								'JPN' => 'Jepang',
								'PHI' => 'Filipina',
								'THA' => 'Thailand'
							)
						);

		// @deprecated
		public $picture_upload_path = PICTURE_UPLOAD_PATH;
	}
}
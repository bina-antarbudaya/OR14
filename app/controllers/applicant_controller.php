<?php

class ApplicantController extends AppController {

	public $default_action = 'redeem';

	public $applicant;

	/**
	 *
	 */
	public function init() {
		if ($this->is_logged_in())
			$this->applicant = $this->session->user->applicant;
	}

	/**
	 * @deprecated
	 */
	private function check_expiry() {
		if ($this->applicant )
		if ($this->applicant && $this->applicant->expires_on->earlier_than('now'))
			$this->auth->land();
	}

	/**
	 * @deprecated
	 */
	private function check_submitted() {
		if (!$this->applicant->submitted)
			$this->auth->land();
	}

	/**
	 * @deprecated
	 */
	public function expired() {}

	/**
	 * @deprecated
	 */
	private function require_finalized() {
		if (!$this->applicant->finalized && Helium::conf('production'))
			$this->http_redirect(array('controller' => 'applicant', 'action' => 'form'));
	}

	/**
	 * @deprecated
	 */
	public function guide() {
		$this->http_redirect('/uploads/guide.pdf'); exit;
	}

	/**
	 *
	 */
	public function redeem() {
		if ($this->applicant)
			$this->auth->land();

		$enable_recaptcha = $this['enable_recaptcha'] = false;

		$this['recaptcha'] = $recaptcha = new RECAPTCHA;

		unset($this->session['registration_code']);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// submitting a form
			$token = strtoupper(trim($_POST['token']));

			if (!isset($token))
				$error = 'incomplete';

			// validate the token
			if (!$error) {
				$code = RegistrationCode::find_by_token($token);
				if (!$code)
					$error = 'token_nonexistent';
			}
			
			if (!$error && !$code->is_available())
				$error = 'token_unavailable';
				
			if (!$error && $code->is_expired())
				$error = 'token_expired';

			// validate reCAPTCHA
			if ($enable_recaptcha && !$error && !$recaptcha->check_answer()) {
				$error = 'recaptcha';
			}

			if (!$error) {
				// Everything went alright

				// save the code into session,
				// then redirect to applicant/create
				$this->session['registration_code_token'] = $code->token;
				$this->http_redirect(array('controller' => 'applicant', 'action' => 'create'));
			}
			else {
				$this->session['error'] = $error;
				$this->http_redirect(array('controller' => 'applicant', 'action' => 'redeem'));
			}
		}

		$this['error'] = $this->session->flash('error');
		$this['chapters'] = Chapter::find('id != 1');
	}
	
	/**
	 * Reactivate expired account
	 */
	public function reactivate() {
		if ($_POST['username'] && $_POST['password']) {
			$this->auth->process_login($_POST['username'], $_POST['password']);
			$user = User::find_by_username_and_password($_POST['username'], $_POST['password']);
			if ($user && $user->capable_of('applicant')) {
				$this->applicant = $user->applicant;
			}
		}
		if (!$this->applicant)
			$this->require_role('applicant');

		//if (!$this->applicant->is_expired())
		//	$this->auth->land();

		$enable_recaptcha = $this['enable_recaptcha'] = false;

		$this['recaptcha'] = $recaptcha = new RECAPTCHA;

		unset($this->session['registration_code']);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// submitting a form
			$token = strtoupper(trim($_POST['token']));

			if (!isset($token))
				$error = 'incomplete';

			// validate the token
			if (!$error) {
				$code = RegistrationCode::find_by_token($token);
				if (!$code)
					$error = 'token_nonexistent';
			}
			
			if (!$error && !$code->is_available())
				$error = 'token_unavailable';
				
			if (!$error && $code->is_expired())
				$error = 'token_expired';
				
			if (!$error && $code->chapter_id != $this->applicant->chapter_id)
				$error = 'chapter_mismatch';

			// validate reCAPTCHA
			if ($enable_recaptcha && !$error && !$recaptcha->check_answer()) {
				$error = 'recaptcha';
			}

			if (!$error) {
				// Everything went alright

				// Extend current user's expiry
				$this->applicant->expires_on = $code->expires_on;
				$this->applicant->save();
				$this->auth->land();
			}
			else {
				$this->session['error'] = $error;
				$this->http_redirect(array('controller' => 'applicant', 'action' => 'redeem'));
			}
		}

		$this['error'] = $this->session->flash('error');
		$this['chapters'] = Chapter::find('id != 1');
	}

	/**
	 *
	 */
	public function create() {
		if ($this->applicant)
			$this->auth->land();
	
		// we need a code on hand to get to this form

		$token = $this->session['registration_code_token'];
		$code = RegistrationCode::find_by_token($token);
		// registration code validation
		// if this doesn't pass, redirect back to applicant/redeem
		if (!$code) {
			$this->http_redirect(array('controller' => 'applicant', 'action' => 'redeem'));
		}
		elseif (!$code->validate()) {
			$this->session['registration_code_error'] = $code->validation_error;
			$this->http_redirect(array('controller' => 'applicant', 'action' => 'redeem'));
		}

		$this['expires_on'] = $code->expires_on;
		$this['form'] = new FormDisplay;
		$this['chapter_name'] = $code->chapter->chapter_name;
		$this['token'] = $this->session['registration_code_token'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// submitting a form here
			$username = trim($_POST['username']);
			$password = $_POST['password'];
			$retype_password = $_POST['retype_password'];
			$email = trim($_POST['email']);

			// validate username, password and email.
			$validate = array();

			if (!isset($username, $password, $retype_password, $email))
				$error = 'incomplete';

			if (!$error) {
				$username_check = (bool) User::find(array('username' => $username))->first();
				if ($username_check)
					$error = 'username_availability';
			}

			if (!$error) {
				// username validation
				// username can only contain letters, numbers and underscore. min. 3 chars.
				$username_pattern = "/^[a-z0-9_\-]{4,}$/i";
				if (!preg_match($username_pattern, $username))
					$error = 'username_format';
			}

			// validate retype password
			if ($password != $retype_password)
				$error = 'retype_password';
			
			if (strlen($password) < 8)
				$error = 'password';

			// validate email
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				$error = 'email';

			if (!$error) {
				// everything set to go

				$db = Helium::db();
				
				try {
					$db->autocommit(false);

					// redeem the reg code
					$code->redeem();

					$chapter = $code->chapter;

					// create the user
					$user = new User;
					$user->username = $username;
					$user->set_password($password);
					$user->email = $email;
					$user->role = 1;
					$user->chapter_id = $code->chapter_id;
					$user->save();

					// create the applicant
					$applicant = new Applicant;
					$applicant->map_vertical_partitions();
					$applicant->expires_on = clone $code->expires_on;
					$applicant->chapter_id = $code->chapter_id;
					$applicant->program_year = $code->program_year;
					$applicant->citizenship = 'Indonesia';
					
					$applicant->user_id = $user->id;
					$applicant->applicant_email = $email;

					$province = $chapter->chapter_area;
					$city = $chapter->chapter_name;
					$address_keys = array('applicant', 'high_school');
					foreach ($address_keys as $k) {
						$p = $k . '_address_province';
						$applicant->$p = $province;
					}
					if ($city != $province) {
						foreach ($address_keys as $k) {
							$c = $k . '_address_city';
							$applicant->$c = $city;
						}
					}

					$applicant->save();

					$db->commit();

					// assign the code to the user
					$code->applicant_id = $applicant->id;
					$code->save();

					// link everything up
					$applicant->save();
					$user->save();

					// login as the new user
					$this->auth->process_login($username, $password);
					$this->session->is_persistent = $_POST['remember'];
					$this->session['just_logged_in'] = true;
					$this->session->save();
					
					$db->commit();
					
					$db->autocommit(true);
				}
				catch (HeliumException $e) {
					$db->rollback();
					$error = 'db_fail';

					if (!Helium::config('production')) {
						throw $e;
					}
				}

				if (!$error) {
					$this['mode'] = 'success';

					$this->session['registration_code'] = '';
					$this->http_redirect(array('controller' => 'applicant', 'action' => 'form'));
				}
			}
			if ($error) {
				$this->session['username'] = $username;
				$this->session['email'] = $email;
				$this['error'] = $error;
			}
		}
	}

	/**
	 * List applicants
	 * @deprecated - see chapter/applicants
	 */
	public function index() {
		$this->require_role('chapter_staff');
		
		$applicants = Applicant::find();

		$db = Helium::db();
		
		// -- Filtering --
		
		// Filter by chapter
		// This can only be used by national admin
		if ($this->user->capable_of('national_admin')) {
			if ($this->params['chapter_id'] && $this->params['chapter_id'] != 1) {
				$applicants->narrow(array('chapter_id' => $this->params['chapter_id']));
				$is_search = true;
				$chapter = Chapter::find($this->params['chapter_id']);
			}
		}
		// Otherwise, only list applicants from user's chapter
		else {
			$applicants->narrow(array('chapter_id' => $this->user->chapter_id));
			$chapter = $this->user->chapter;
		}
		
		// Filter by stage
		$applicants->add_additional_column('expired', "expires_on < '" . (new HeliumDateTime('now')) . "'");
		switch ($this->params['stage']) {
			case 'expired':
				$applicants->narrow(array('expired' => true, 'confirmed' => false));
				break;
			case 'unexpired':
				$applicants->narrow(array('expired' => false));
				$applicants->widen(array('confirmed' => true));
				break;
			case 'confirmed':
				$applicants->narrow(array('confirmed' => true));
				break;
			case 'finalized':
				$applicants->narrow(array('finalized' => true));
				break;
			case 'anomaly':
				$applicants->narrow(array('confirmed' => false, 'expired' => true, 'finalized' => true));
				break;
			case 'incomplete':
				$applicants->narrow(array('confirmed' => false, 'expired' => false, 'finalized' => false));
				break;
		}
		
		// Filter by school
		if ($this->params['school_name']) {
			$school = Applicant::sanitize_school(stripslashes($this->params['school_name']));
			$applicants->narrow(array('sanitized_high_school_name' => $school));
			$is_search = true;
			$search_title = $school;
		}

		// Filter by name
		if ($this->params['name']) {
			$criteria = $db->prepare("`sanitized_full_name` LIKE '%%%s%%'", str_replace(' ', '%', $this->params['name']));
			$applicants->narrow($criteria);
			$is_search = true;
			$search_title = $this->params['name'];
		}
		
		// Filter by test IDs
		if ($this->params['test_ids']) {
			$test_ids = $this->split_test_ids($this->params['test_ids']);
			$query = "('" . implode("','", $test_ids) . "')";
			$applicants->narrow('test_id IN ' . $query);
		}

		// TODO
		// Filter by POB
		// Filter by DOB
		
		// -- Ordering --
		
		switch ($this->params['order_by']) {
			case 'school':
				$order_by = 'sanitized_high_school_name';
				break;
			case 'name':
				$order_by = 'sanitized_full_name';
				break;
			case 'test_id':
			default:
				$order_by = 'test_id';
		}
		$applicants->set_order_by($order_by);
		
		if (strtoupper($this->params['order']) == 'DESC')
			$applicants->set_order('DESC');
		else
			$applicants->set_order('ASC');

		// -- Pagination --
		$batch_length = 100;
		$applicants->set_batch_length($batch_length);
		if (!$this->params['page'])
			$this->params['page'] = 1;
		$page = $this->params['page'];
		$count_all = $applicants->count_all();
		$applicants->set_batch_number($page);
		$first = (($page - 1) * $batch_length) + 1;
		$last = ($first + $batch_length - 1) > $count_all ? $count_all : ($first + $batch_length - 1);

		// Applicants is now ready for listing.
		$this['applicants'] = $applicants;
		$this['chapter'] = $chapter;
		$this['total_pages'] = $applicants->get_number_of_batches();
		$this['current_page'] = $page;
		$this['first'] = $first;
		$this['last'] = $last;
		$this['count_all'] = $count_all;
		$this['search_title'] = $search_title;
		$this['current_stage'] = $this->params['stage'];

		if ($this->user->capable_of('national_admin'))
			$this['schools'] = $this->get_schools();
		else
			$this['schools'] = $this->get_schools($this->user->chapter_id);
			
		$this['form'] = new FormDisplay;
		
		$this->session['back_to'] = $this->params;
	}

	/**
	 * Applicant stats
	 */
	public function stats() {
		$this->require_role('chapter_staff');
		
		// $applicants = Applicant::find();

		$db = Helium::db();
		
		$constraints = array();
		
		// -- Filtering --
		
		// Filter by chapter
		// This can only be used by national admin
		if ($this->user->capable_of('national_admin')) {
			if ($this->params['chapter_id'] && $this->params['chapter_id'] != 1) {
				$constraints[] = 'chapter_id=' . $this->params['chapter_id'];
				$chapter = Chapter::find($this->params['chapter_id']);
			}
		}
		// Otherwise, only list applicants from user's chapter
		else {
			$constraints[] = 'chapter_id=' . $this->user->chapter_id;
			$chapter = $this->user->chapter;
		}
		
		// Filter by stage
		// $applicants->add_additional_column('expired', "expires_on < '" . (new HeliumDateTime('now')) . "'");
		switch ($this->params['stage']) {
			case 'expired':
				// $applicants->narrow(array('expired' => true, 'confirmed' => false));
				break;
			case 'unexpired':
				// $applicants->narrow(array('expired' => false));
				// $applicants->widen(array('confirmed' => true));
				$constraints[] = 'expires_on > NOW() OR finalized=1';
				break;
			case 'confirmed':
				$constraints[] = 'confirmed=1';
				break;
			case 'finalized':
				$constraints[] = 'finalized=1';
				break;
			case 'anomaly':
				$constraints[] = 'expires_on <= NOW() AND confirmed=0 AND finalized=1';
				break;
			case 'incomplete':
				$constraints[] = 'confirmed=0 AND finalized=0 AND expires_on > NOW()';
				break;
		}
		
		// Filter by school
		if ($this->params['school_name']) {
			$constraints[] = "sanitized_high_school_name='" . $this->params['school_name'] . "'";
			$is_search = true;
			$search_title = $this->params['school_name'];
		}

		// Filter by name
		if ($this->params['name']) {
			$constraints[] = $db->prepare("`sanitized_full_name` LIKE '%%%s%%'", str_replace(' ', '%', $this->params['name']));
			$is_search = true;
			$search_title = $this->params['name'];
		}
		
		// Filter by test IDs
		if ($this->params['test_ids']) {
			$test_ids = $this->split_test_ids($this->params['test_ids']);
			$query = "('" . implode("','", $test_ids) . "')";
			$constraints[] = 'test_id IN ' . $query;
		}

		// TODO
		// Filter by POB
		// Filter by DOB

		// STAT GROUPING
		$stats = array(
			'sex' => array(
				'type' => 'pie',
				'field' => 'sex',
				'partition' => 'applicant_personal_details',
			),
			'school' => array(
				'type' => 'pie',
				'field' => 'sanitized_high_school_name',
				'partition' => 'applicant_personal_details',
			),
			'chapter' => array(
				'type' => 'pie',
				'field' => 'chapter_id',
				'base_query' => "SELECT COUNT(*) AS rows, %s AS chapter_id, chapter_name AS value FROM applicants INNER JOIN chapters %s ON chapter_id=chapters.id WHERE %s AND (applicants.expires_on > NOW() or applicants.finalized=1) GROUP BY %s ORDER BY rows DESC",
			),
			'province' => array(
				'type' => 'pie',
				'field' => 'applicant_address_province',
				'partition' => 'applicant_contact_info',
			),
			'city' => array(
				'type' => 'pie',
				'field' => "CONCAT(applicant_address_city, ', ', applicant_address_province)",
				'partition' => 'applicant_contact_info',
			),
		);

		$db = Helium::db();

		if ($constraints)
			$constraint_string = implode(' AND ', $constraints);
		else
			$constraint_string = '1';
		
		foreach ($stats as $key => $group) {
			unset($type, $field, $partition, $base_query, $join_string);
			extract($group);
			if (!$base_query)
				$base_query = "SELECT COUNT(*) AS rows, %s AS value FROM applicants %s WHERE %s AND (applicants.expires_on > NOW() or applicants.finalized=1) GROUP BY %s ORDER BY rows DESC";

			if ($partition)
				$join_string = $db->prepare("INNER JOIN `%s` ON `%s`.applicant_id = applicants.id", $partition, $partition);

			$the_query = sprintf($base_query, $field, $join_string, $constraint_string, $field);
			$results = $db->get_results($the_query);
			
			$series = array();
			$total = 0;
			if (!$results) $results = array();
			foreach ($results as $row) {
				$rows = $row->rows;
				$value = $row->value;
				$value = trim($value); // perhaps more trimming could be in order

				if ($series[$value])
					$series[$value] += $rows;
				else
					$series[$value] = $rows;

				$total += $rows;
			}
			
			$stats[$key]['data'] = compact('series', 'total');
		}
		
		// country preferences - special stats
		/*
		$countries = $db->get_col("SELECT country_preference_1, COUNT(*) AS rows  FROM applicant_program_choices WHERE country_preference_1 IS NOT NULL AND country_preference_1 != '' GROUP BY country_preference_1 ORDER BY rows DESC");
		*/
		$country_stats = array();
		foreach (/* $countries */ array() as $country) {
			$numbers = array();
			for ($i = 1; $i <= 10; $i++) {
				$cq = "SELECT COUNT(*) FROM applicant_program_choices INNER JOIN applicants ON applicants.id=applicant_program_choices.applicant_id WHERE country_preference_$i='$country' AND $constraint_string";
				$numbers[$i] = $db->get_var($cq);
			}
			$country_stats[$country] = $numbers;
		}
		
		$total_afs = $db->get_var("SELECT COUNT(*) FROM applicant_program_choices INNER JOIN applicants ON applicants.id=applicant_program_choices.applicant_id WHERE program_afs='1' AND $constraint_string");
		
		// other countries - special stats
		$other_countries = $db->get_col("SELECT country_preference_other FROM applicant_program_choices INNER JOIN applicants ON applicants.id=applicant_program_choices.applicant_id WHERE country_preference_other != 'N/A' AND country_preference_other != 'NA' AND $constraint_string");
		$split_pattern = "/[,;\/&]+|\s+(dan|and)\s+/i";
		
		$country_cases = array();
		$other_countries_series = array();
		$other_countries_total = 0;
		
		$country_normalization_patterns = array(
			'/london|inggris|britania raya|england|uk/i' => 'United Kingdom',
			'/\s+\(.*\)/' => '',
			'/korea( selatan)?|south korea/i' => 'South Korea',
			'/kanada/i' => 'Canada',
			'/singapura/i' => 'Singapore',
			'/spanyol/i' => 'Spain',
			'/^arab( saudi)?$/i' => 'Saudi Arabia',
			'/turki/i' => 'Turkey',
			'/rusia/i' => 'Russia',
			'/^(republik rakyat )?ch?ina$|^rrc$/i' => "China, People's Republic of",
			'/selandia baru|nz/i' => 'New Zealand'
		);
		
		if (!is_array($other_countries)) $other_countries = array();
		foreach ($other_countries as $list_of_countries) {
			$split_countries = preg_split($split_pattern, $list_of_countries);
			foreach ($split_countries as $country_name) {
				$country_name = trim($country_name, ' -');
				if (!$country_name)
					continue;

				$country_name = preg_replace(array_keys($country_normalization_patterns), array_values($country_normalization_patterns), $country_name);

				$lowercased = strtolower($country_name);
				if ($proper_case = $country_cases[$lowercased]) {
					$other_countries_series[$proper_case]++;
				}
				else {
					$country_cases[$lowercased]	= $country_name;
					$other_countries_series[$country_name] = 1;
				}
				$other_countries_total++;
			}
		}
		
		$stats['other_countries'] = array(
			'type' => 'bar',
			'data' => array(
				'series' => $other_countries_series,
				'total' => $total_afs
			)
		);

		$this['stats'] = $stats;
		$this['country_stats'] = $country_stats;
		$this['total_afs'] = $total_afs;
		$this['chapter'] = $chapter;
		$this['search_title'] = $search_title;
		$this['current_stage'] = $this->params['stage'];

		$this->session['back_to'] = $this->params;
	}

	/**
	 * 
	 */
	public function split_test_ids($test_ids) {
		$test_ids = preg_split('/(\s|[.,])+/', $test_ids);
		return $test_ids;
	}

	/**
	 *
	 */
	public function get_schools($chapter_id = null) {
		$db = Helium::db();

		$q = "SELECT DISTINCT sanitized_high_school_name FROM applicants";
		if ($chapter_id)
			$q .= $db->prepare(" WHERE chapter_id = '%s'", $chapter_id);

		return $db->get_col($q);
	}

	/**
	 * Edit an applicant's application form.
	 *
	 * Accessible either as an applicant or as an admin, with slight UI differences.
	 */
	public function form() {
		$this->require_authentication();
		
		if ($this->session->user->capable_of('chapter_admin')) {
			$this['admin'] = true;

			if ($this->params['readonly'])
				$readonly = true;

			$id = $this->params['id'];
			if (!$id)
				$error = 'not_found';
			else {
				$applicant = Applicant::find($id);
				if (!$applicant)
					$error = 'not_found';

				if (!$error && !$this->user->capable_of('national_admin') && ($this->user->chapter_id != $applicant->chapter_id))
					$error = 'forbidden';
			
				// if (!$error && $applicant->finalized)
				// 	$error = 'applicant_finalized';
			}
		}
		else {
			$this->require_role('applicant');
			$user_id = $this->session->user->id;
			$applicant = $this->session->user->applicant;

			if ($applicant->finalized || $applicant->is_expired())
				$this->auth->land();
		}

		if (!$error) {

			$applicant_id = $applicant->id;

			$pictures = Picture::find(compact('applicant_id'));
			$pictures->set_order('DESC');
			$picture = $this['picture'] = $pictures->first();

			$this['new'] = $this->session->flash('just_logged_in');
			$this['upload_error'] = $this->session->flash('upload_error');
			$this['errors'] = $this->session->flash('form_errors');
			$this['incomplete'] = $this->session->flash('incomplete');
			$this['notice'] = $this->session->flash('notice');

			$subforms = array(	'siblings' => 'applicant_siblings',
								'applicant_organizations' => 'applicant_organizations',
								'applicant_arts_achievements' => 'applicant_arts_achievements',
								'applicant_sports_achievements' => 'applicant_sports_achievements',
								'applicant_other_achievements' => 'applicant_other_achievements',
								'applicant_work_experiences' => 'applicant_work_experiences');


			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['applicant_id'] != $applicant->id) {
				// Data swapping workaround

				// Data swapping detected

				// Logout
				$this->auth->process_logout();

				// Redirect to login
				$this->http_redirect(array('controller' => 'auth', 'action' => 'login', 'error' => 'Terjadi kesalahan teknis. Penyimpanan data dibatalkan.'));

				file_put_contents(HELIUM_APP_PATH . '/error.log', "Dataswap detected: {$applicant->id} was logged in but form belonged to {$_POST['applicant_id']}", FILE_APPEND);
			}
			elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// store the form values in the DB
				$proc = new FormProcessor;
				$proc->add_uneditables('id', 'applicant_id', 'user_id', 'chapter_id', 'program_year', 'expires_on', 'confirmed', 'finalized', 'local_id', 'test_id');
				$proc->associate($applicant);
				$proc->commit();
				$applicant->save();

				$this->session['success'] = true;
				$this->session['last_pane'] = $_POST['last_pane'];

				foreach ($subforms as $f => $d) {
					$old = $applicant->$d;
					$old->delete_all();
					$new = $_POST[$f];

					if ($new) {
						foreach ($new as $node) {
							if ($node) {
								foreach ($node as $n) {
									if ($not_empty)
										break;

									if (is_array($n)) {
										foreach ($n as $o)
											if ($o)
												$not_empty = true;
									}
									elseif ($n)
										$not_empty = true;
								}
								if ($not_empty) {
									$sp = new FormProcessor($node);
									$class_name = Inflector::classify($d);
									$sb = new $class_name;
									$sb->applicant_id = $applicant->id;
									$sp->add_uneditables('id', 'applicant_id');
									$sp->associate($sb);
									$sp->commit();
									$sb->save();
								}
							}

							$not_empty = false;
						}
					}
				}

				// // handle upload, if any.
				if (isset($_FILES['picture']) && $_FILES['picture']['tmp_name']) {
					$file = $_FILES['picture'];
					$pic = new Picture;
					$try = $pic->upload_original($file);

					if ($try) { // Upload success
						$pic->save();

						$this->session['picture_id'] = $pic->id;

						if ($this->session->user->capable_of('chapter_admin') && $this->params['id'])
							$this->http_redirect(array('controller' => 'applicant', 'action' => 'crop_picture', 'id' => $this->params['id']));
						else
							$this->http_redirect(array('controller' => 'applicant', 'action' => 'crop_picture'));

						exit;
					}
					else {
						$this->session['upload_error'] = $pic->upload_error;
					}
				}

				// finalization process
				if ($_POST['finalize']) {
					// we validate the completeness of the form here first.
					$try = $applicant->finalize();

					if ($try) {
						$applicant->save();
						$this->http_redirect(array('controller' => 'applicant', 'action' => 'finalized'));
					}
					else {
						$errors = $applicant->validation_errors;
						$errors = array_map(function($e) {
							switch ($e) {
								case 'incomplete':
									return 'Formulir belum lengkap. Pastikan seluruh bagian formulir ini telah terisi.';
								case 'picture':
									return 'Adik belum mengunggah (upload) foto.';
								case 'birth_date':
									return 'Tanggal lahir Adik harus di antara <strong>1 Agustus 1995</strong> dan <strong>1 Agustus 1997</strong>';
								default:
									return $e;
							}
						}, $errors);
						$this->session['()_errors'] = $errors;
						$this->session['incomplete'] = $applicant->incomplete_fields;
					}
				}

				$this->http_redirect($this->params);
				// @header('Location: ' . PathsComponent::build_url($this->params) . $_POST['last_pane']);
			}

			$form = new FormDisplay;
			$form->associate($applicant);
			$this['form'] = $form;
			$this['expires_on'] = $applicant->expires_on;
		
			$this['applicant'] = $applicant;

			$this['program_year'] = $applicant->program_year;

			$this['last_pane'] = substr($this->session->flash('last_pane'), 1);

			$this['crop_success'] = $this->session->flash('crop_success');

			$this['success'] = $this->session->flash('success');
			
			$this['schools'] = self::get_schools();

			$applicant_siblings = $applicant->applicant_siblings;
			$applicant_siblings->set_order_by('date_of_birth');
			$applicant_siblings->set_order('ASC');
			$sforms = array();
			$i = 0;
			foreach ($applicant_siblings as $s) {
				$d = new FormDisplay;
				$d->associate($s);
				$d->make_subform("siblings[$i]");
				$i++;
				$sforms[] = $d;
			}

			$this['sibling_forms'] = $sforms;
		
			$subform_forms = array();
			foreach ($subforms as $f => $d) {
				$nodes = $applicant->$d;
				$i = 1;
				$forms = array();
				if ($nodes) {
					foreach ($nodes as $s) {
						$d = new FormDisplay;
						$d->associate($s);
						$d->make_subform($f . '[' . $i . ']');
						$i++;
						$forms[] = $d;
					}
				}

				$subform_forms[$f] = $forms;
			}
		
			$this['subforms'] = $subform_forms;
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * View a read-only, complete version of an applicant's application form.
	 *
	 * Accessible either as an applicant or as an admin, with slight UI differences.
	 */
	public function details() {
		$this->require_authentication();
		
		if ($this->session->user->capable_of('chapter_admin')) {
			$this['admin'] = true;

			if ($this->params['readonly'])
				$readonly = true;

			$id = $this->params['id'];
			if (!$id)
				$error = 'not_found';
			else {
				$applicant = Applicant::find($id);
				if (!$applicant)
					$error = 'not_found';

				if (!$error && !$this->user->capable_of('national_admin') && ($this->user->chapter_id != $applicant->chapter_id))
					$error = 'forbidden';
			}
		}
		else {
			$this->require_role('applicant');
			$user_id = $this->session->user->id;
			$applicant = $this->session->user->applicant;
		}

		if (!$error) {

			$applicant_id = $applicant->id;

			$pictures = Picture::find(compact('applicant_id'));
			$pictures->set_order('DESC');
			$picture = $this['picture'] = $pictures->first();

			$subforms = array(	'siblings' => 'applicant_siblings',
								'applicant_organizations' => 'applicant_organizations',
								'applicant_arts_achievements' => 'applicant_arts_achievements',
								'applicant_sports_achievements' => 'applicant_sports_achievements',
								'applicant_other_achievements' => 'applicant_other_achievements',
								'applicant_work_experiences' => 'applicant_work_experiences');

			$this['a'] = $applicant;

			foreach ($subforms as $k => $sf)
				$this[$k] = $applicant->$sf;

			$form = new FormTranscript;
			$form->associate($applicant);
			$this['form'] = $form;
			$this['expires_on'] = $applicant->expires_on;
		
			$this['applicant'] = $applicant;
		
			$this['program_year'] = $applicant->program_year;
		
			$this['last_pane'] = substr($this->session->flash('last_pane'), 1);

			$applicant_siblings = $applicant->applicant_siblings;
			$applicant_siblings->set_order_by('date_of_birth');
			$applicant_siblings->set_order('ASC');
			$sforms = array();
			$i = 0;
			foreach ($applicant_siblings as $s) {
				$d = new FormTranscript;
				$d->associate($s);
				$d->make_subform("siblings[$i]");
				$i++;
				$sforms[] = $d;
			}

			$this['sibling_forms'] = $sforms;
		
			$subform_forms = array();
			foreach ($subforms as $f => $d) {
				$nodes = $applicant->$d;
				$i = 0;
				$forms = array();
				if ($nodes) {
					foreach ($nodes as $s) {
						$d = new FormTranscript;
						$d->associate($s);
						$d->make_subform($f . '[' . $i . ']');
						$i++;
						$forms[] = $d;
					}
				}

				$subform_forms[$f] = $forms;
			}
		
			$this['subforms'] = $subform_forms;
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * View applicant
	 */
	public function view() {
		$this->require_role('chapter_staff');

		if ($id = $this->params['id'])
			$applicant = Applicant::find($id);
		elseif ($test_id = $this->params['test_id'])
			$applicant = Applicant::find_by_test_id($test_id);
		elseif ($username = $this->params['username']) {
			$db = Helium::db();
			$id = $db->get_var($db->prepare("SELECT applicants.id FROM applicants INNER JOIN users ON applicants.user_id=users.id WHERE username='%s'", $username));
			$applicant = Applicant::find($id);
		}
		
		$this->session['applicant_back_to'] = $this->params;
		
		if (!$applicant)
			$error = 'not_found';
		else {
			$this['applicant'] = $applicant;
			$this['picture'] = $applicant->picture;
		}
		
		if (!$error && !$this->user->capable_of('national_admin') && ($applicant->chapter_id != $this->user->chapter_id))
			$error = 'forbidden';
		
		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['finalized'])
				$applicant->finalize();
			else
				$applicant->finalized = 0;

			if ($_POST['force_finalize'])
				$applicant->force_finalize();

			$applicant->confirmed = $applicant->finalized ? $_POST['confirmed'] : false;
			$applicant->save();
		}
		
		if (!$error) {
			$back_to = $this->session['back_to'];
			if (!$back_to)
				$back_to = array('controller' => 'applicant', 'action' => 'index');

			$this['back_to'] = $back_to;

			$this['can_edit'] = $this->user->capable_of('chapter_admin'); // && !$applicant->finalized;
		}
		else
			$this['error'] = $error;
	}

	/**
	 *
	 */
	public function crop_picture() {
		$this->require_role('applicant');

		$this->check_expiry();

		if (!$this->session['picture_id'])
			$this->http_redirect(array('controller' => 'applicant', 'action' => 'form'));
		else {
			$picture_id = $this->session['picture_id'];
			$picture = Picture::find($picture_id);

			if (!$picture)
				$this->http_redirect(array('controller' => 'applicant', 'action' => 'form'));
			else
				$this['picture'] = $picture;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			extract($_POST);
			$params = compact('width', 'height', 'x', 'y');
			$pic = $picture;
			$crop = $pic->process($params);
			if ($crop) {
				// Crop success!
				$applicant = $this->applicant;
				if (!$applicant && $this->params['id'])
					$applicant = Applicant::find($this->params['id']);
				
				// // Delete the existing picture
				// if ($applicant->picture)
				// 	$applicant->picture->destroy();

				// Link the new picture
				$pic->applicant_id = $applicant->id;
				$pic->save();
				unset($this->session['picture_id']);
				
				$this->session['last_pane'] = '#foto';
				$this->session['crop_success'] = true;

				// back to the form
				if ($this->session->user->capable_of('chapter_admin'))
					$this->http_redirect(array('controller' => 'applicant', 'action' => 'form', 'id' => $this->params['id']));
				else
					$this->http_redirect(array('controller' => 'applicant', 'action' => 'form'));
			}
			else {
				$this->session['error'] = 'Pengunggahan foto gagal.';
			}

			exit;
		}
	}

	/**
	 *
	 */
	public function card() {
		$this->require_role('applicant');

		if ($this->session->user->capable_of('chapter_admin')) {
			$this['admin'] = true;

			if ($this->params['readonly'])
				$readonly = true;

			$id = $this->params['id'];
			if (!$id)
				$error = 'not_found';
			else {
				$applicant = Applicant::find($id);
				if (!$applicant)
					$error = 'not_found';

				if (!$error && !$this->user->capable_of('national_admin') && ($this->user->chapter_id != $applicant->chapter_id))
					$error = 'forbidden';
			}
		}
		else {
			$user_id = $this->session->user->id;
			$applicant = $this->session->user->applicant;
		}

		if ($error)
			$this->render = false;

		$applicant_id = $applicant->id;
		$picture = $applicant->picture;

		$this['name'] = $applicant->sanitized_full_name;
		$this['applicant'] = $applicant;
		$this['picture'] = $picture;
		
		// 2013-04-26: Allow national admin to override inability to view unfinalized applicant cards
		if (!$applicant->finalized && !$this->user->capable_of('national_admin'))
			$this->render = false;
	}

	/**
	 *
	 */
	public function finalized() {
		$this->require_role('applicant');

		if ($this->params['preview'] && $this->session->user->capable_of('chapter_admin')) {
			$applicant = $this['applicant'] = new Applicant;
			$applicant->in_acceleration_class = true;
			$applicant->chapter_id = $this->session->user->capable_of('national_admin') ? $this->params['preview'] : $this->session->user->chapter_id;
			$this['is_preview'] = true;
			$this['back_to_chapter_id'] = $applicant->chapter_id;
		}
		else {
			$applicant = $this['applicant'] = $this->applicant;

			if (!$applicant->finalized || $applicant->confirmed)
				$this->auth->land();
			elseif (!$this->can_register())
				$this->http_redirect(array('controller' => 'applicant', 'action' => 'results'));
		}
	}

	/**
	 *
	 */
	public function confirmed() {
		$this->require_role('applicant');

		$applicant = $this['applicant'] = $this->applicant;

		if (!$applicant->confirmed)
			$this->auth->land();
		elseif (!$this->can_register())
			$this->http_redirect(array('controller' => 'applicant', 'action' => 'results'));
	}

	/**
	 * 
	 */
	public function transcript() {		
		$this->details();
		
		// if (!$this->applicant->participant->passed_selection_one ||
		// 	($this->applicant->participant->selection_two_batch &&
		// 		$this->applicant->participant->selection_two_batch->announcement_date->later_than('now')))
		// 	$this->render = false;
	}

	public function file() {
		$filename = $this->params['file'];

		$this['applicant'] = $this->applicant;
		$this['file'] = $filename;
	}

	public function results() {
		$db = Helium::db();
		if ($test_id = $_POST['test_id']) {
			$value = $_POST['dob'];
			if (is_array($value)) { // Classic three-select form control
				$year = $month = $day = $hour = $minute = $second = 0;
				foreach ($value as $k => $v) {
					if (!is_numeric($v))
						unset($value[$k]);
					else
						$value[$k] = intval($v);
				}

				extract($value);
				$dob = new HeliumDateTime('now');
				$dob->setDate($year, $month, $day);
				$dob->setTime(0, 0, 0);
				$dob = (string) $dob;
			}
			$applicant_id = $db->get_var("SELECT id FROM applicants WHERE date_of_birth='$dob' AND test_id='$test_id'");
			
			$applicant = Applicant::find(array('date_of_birth' => $dob, 'test_id' => $test_id))->first();
		}
		
		if (!$applicant && $this->applicant)
			$applicant = $this->applicant;
		
		if (!$applicant && $_POST['on_fail_go_to'])
			$this->http_redirect($_POST['on_fail_go_to']);

		$this['applicant'] = $applicant;

		if ($applicant) {
			if (!$applicant->finalized)
				$this->auth->land();
			
			$announcement_dates_original = $announcement_dates = array(
				1 => Helium::conf('selection_one_announcement_date'),	
				2 => Helium::conf('selection_two_announcement_date'),
				3 => Helium::conf('selection_three_announcement_date'),
			);

			$chapter_id = $applicant->chapter_id;

			$db = Helium::db();
			
			// for selection 2
			$wave = $db->get_var('SELECT announcement_date FROM selection_two_batches WHERE chapter_id=' . $chapter_id . ' AND announcement_date_follows_national=0 ORDER BY announcement_date ASC LIMIT 0,1'); // may be null
			if ($wave && $wave{0} != '0')
				$announcement_dates[1] = $wave;

			// for selection 3
			$wave = $db->get_var('SELECT announcement_date FROM selection_three_batches WHERE chapter_id=' . $chapter_id . ' AND announcement_date_follows_national=0 ORDER BY announcement_date ASC LIMIT 0,1'); // may be null
			if ($wave && $wave{0} != '0')
				$announcement_dates[2] = $wave;
			
			$now = new HeliumDateTime('now');
			
			$selection_to_announce = 0; // no announcement yet
			foreach ($announcement_dates as $n => $a) {
				$a = new HeliumDateTime($a, $applicant->chapter->chapter_timezone);
				if ($now >= $a)
					$selection_to_announce = $n;
			}
			
			$batch_count = $db->get_var('SELECT COUNT(*) FROM selection_two_batches WHERE chapter_id=' . $chapter_id);
			if (!$batch_count)
				$selection_to_announce = 0;
			
			if (!$selection_to_announce) {
				$selection_dates = array(
					3 => Helium::conf('selection_three_date'),
					2 => Helium::conf('selection_two_date'),
					1 => Helium::conf('selection_one_date'),
				);
				foreach ($selection_dates as $n => $a) {
					$a = new HeliumDateTime($a, $applicant->chapter->chapter_timezone);
					if ($now >= $a)
						$next_selection = $n;
				}
			}
			
			$participant = $applicant->participant;
			
			$props = array(
				1 => 'passed_selection_one',
				2 => 'passed_selection_two',
				3 => 'passed_selection_three'
			);
			
			if ($selection_to_announce)
				$selection_result = $participant->selection_results($selection_to_announce);

			if ($selection_to_announce == 1) {
				$next_announcement_wave_date = $db->get_var('SELECT announcement_date FROM selection_two_batches WHERE announcement_date > NOW() AND announcement_date_follows_national=0 AND chapter_id=' . $chapter_id . ' LIMIT 0,1'); // may be null

				if ($next_announcement_wave_date && $next_announcement_wave_date != '0000-00-00 00:00:00') {
					$next_announcement_wave_date = new HeliumDateTime($next_announcement_wave_date, $applicant->chapter->chapter_timezone);

					$selection_to_announce = 0;
					$next_selection = 1;
				}
			}
		}
		
		$this['selection_to_announce'] = $selection_to_announce; // 0..3
		$this['selection_result'] = $selection_result; // true|false
		$this['next_selection'] = $next_selection;
		$this['next_announcement_wave_date'] = $next_announcement_wave_date; // may be null
		
	}

	/**
	 * @deprecated
	 */
	public function confirm() {
		$this->require_role('volunteer');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$aid = $_POST['applicant_id'];
			$ap = Applicant::find($aid);
			$ap->confirm();
			$ap->save();
			$this['notice'] = 'Applicant submission confirmed.';
			$this->params['id'] = $aid;
		}

		$id = $this->params['id'];
		if (!$id)
			$this['no_applicant'] = true;
		else {
			$a = $this['a'] = Applicant::find($id);
			$this['d'] = $a->applicant_detail;
			if (!$a)
				$this['error'] = 'Invalid Applicant';
			// elseif (!$a->finalized)
			// 	$this['error'] = 'Applicant not yet finalized';
		}
	}

	public function sanitize_all() {
		$this->render = false;
		$db = Helium::db();
		$page = $this->params['id'];
		$start = $page * 50;
		$finish = $start + 50;
		$raw_schools = $db->get_col('SELECT DISTINCT high_school_name FROM applicant_high_schools LIMIT ' . $start . ',' . $finish);

		if (count($raw_schools)) {
			$replace = array();
			foreach ($raw_schools as $sch)
				$replace[$sch] = Applicant::sanitize_school($sch);

			echo '<pre>';
			foreach ($replace as $f => $r) {
				$db->query($db->prepare("UPDATE applicants INNER JOIN applicant_high_schools ON applicant_high_schools.applicant_id=applicants.id SET sanitized_high_school_name='%s' WHERE high_school_name='%s'", $r, $f));
				echo "$f -> $r\n";
			}
			?><script>window.location.href='<?php L(array('action' => 'sanitize_all', 'id' => $page + 1)) ?>'</script><?php
		}
		exit;
	}

}
<?php

/**
 * ChapterController
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package chapter
 */
class ChapterController extends AppController {
	public $default_action = 'dashboard';

	// @deprecated
	public function index() {
		$this->require_role('admin');

		$chapters = Chapter::find('id != 1');
		$charr = array();
		$chapp = array();
		foreach ($chapters as $ch) {
			$charr[$ch->id] = $ch;
			$chapp[$ch->id] = $ch->get_applicant_count();
		}
		$chapters = array();
		arsort($chapp);
		foreach (array_keys($chapp) as $id) {
			$chapters[] = $charr[$id];
		}

		$this['chapters'] = $chapters;
	}

	/**
	 * Dashboard
	 * 
	 * There are two 'phases', each with different functions on the dashboard:
	 * - registration (before conf[registration_deadline]):
	 *   Expose PIN generation, applicant count based on status
	 *   (active, expired, finalized, etc)
	 * - post_registration (after conf[registration_deadline]):
	 *   Expose list of actions to prepare for selections
	 *   (download tabulation template, upload participants that pass)
	 *   as well as participant count based on selection stage
	 *   (all, pass selection 1, pass selection 2, etc)
	 */
	public function dashboard() {
		$this->require_authentication();
		$this->require_role('chapter_staff');

		if ($this->user->capable_of('national_admin')) {
			$chapter_code = strtoupper($this->params['chapter_code']);
			$chapter_id = $this->params['id'];
			if ($chapter_id) {
				$chapter = Chapter::find($chapter_id);
			}
			elseif ($chapter_code) {
				$chapter = Chapter::find(compact('chapter_code'));
				$chapter = $chapter->first();
			}
			else
				$chapter = $this->user->chapter;
		}
		elseif ($this->user->capable_of('chapter_staff')) {
			$chapter = $this->user->chapter;
		}
		else {
			$error = 'forbidden';
		}

		if (!$error && !$chapter) {
			$error = 'not_found';
		}

		$registration_deadline = new HeliumDateTime(Helium::conf('registration_deadline'));
		if ($registration_deadline->later_than('now')) {
			$current_phase = 'registration';
		}
		else {
			$current_phase = 'post_registration';
		}

		$this['current_phase'] = $current_phase;

		if (!$error) {
			$this['chapter'] = $chapter;
			$this['national'] = $chapter->is_national_office();
			foreach ($chapter->_columns() as $col) {
				$this[$col] = $chapter->$col;
			}

			// Quick stats for registration phase
			$this['registration_codes'] = $chapter->registration_codes;
			$this['code_count'] = $chapter->registration_codes->count_all();

			$ac = clone $chapter->registration_codes;
			$ac->narrow('availability=0');
			$this['ac'] = $ac;
			$this['activated_code_count'] = $ac->count_all();

			$now = new HeliumDateTime;
			$ec = clone $chapter->registration_codes;
			$ec->narrow("availability=1 AND expires_on < '$now'");
			$this['expired_code_count'] = $ec->count_all();

			$vc = clone $chapter->registration_codes;
			$vc->narrow("availability=1 AND expires_on > '$now'");
			$this['available_code_count'] = $vc->count_all();

			$this['applicants'] = $chapter->applicants;
			$this['total_applicant_count'] = $chapter->applicants->count_all();

			$aa = clone $chapter->applicants;
			$aa->narrow("(finalized=1 OR expires_on > '$now')");
			$this['active_applicant_count'] = $aa->count_all();

			$ca = clone $chapter->applicants;
			$ca->narrow('confirmed=1');
			$this['confirmed_applicant_count'] = $ca->count_all();

			$this['applicant_tipping_point'] = $ca->count_all() == $aa->count_all();

			$fa = clone $chapter->applicants;
			$fa->narrow('finalized=1');
			$this['finalized_applicant_count'] = $fa->count_all();

			$this['incomplete_applicant_count'] = $aa->count_all() - $fa->count_all();

			$nca = clone $chapter->applicants;
			$nca->narrow('finalized=1 && confirmed=0');
			$this['not_yet_confirmed_applicant_count'] = $nca->count_all();

			$ea = clone $chapter->applicants;
			$ea->narrow("finalized=0 AND expires_on < '$now'");
			$this['expired_applicant_count'] = $ea->count_all();

			// Weird
			$na = clone $chapter->applicants;
			$na->narrow("confirmed=0 AND finalized=1 AND expires_on <'$now'");
			$this['anomalous_applicant_count'] = $na->count_all();

			$na = clone $chapter->applicants;
			$na->set_order_by('id');
			$na->set_order('DESC');
			$na->narrow("sanitized_full_name != ''");
			$na->set_batch_length(10);

			$this['na'] = $na;

			// Quick stats post-registration
			$db = Helium::db();
			$participant_count_query =
				'SELECT COUNT(*) FROM applicants INNER JOIN participants ' .
				'ON applicants.id=participants.applicant_id WHERE applicants.finalized=1';
			if (!$this['national'])
				$participant_count_query .= ' AND chapter_id=' . $this->user->chapter_id;

			$this['participant_count'] = $db->get_var($participant_count_query);
			$this['participant_count_passed_1'] = $db->get_var($participant_count_query . ' AND passed_selection_one=1');
			$this['participant_count_passed_2'] = $db->get_var($participant_count_query . ' AND passed_selection_two=1');
			$this['participant_count_passed_3'] = $db->get_var($participant_count_query . ' AND passed_selection_three=1');
			$this['participant_count_failed_1'] = $db->get_var($participant_count_query . ' AND passed_selection_one=0');
			$this['participant_count_failed_2'] = $db->get_var($participant_count_query . ' AND passed_selection_two=0');
			$this['participant_count_failed_3'] = $db->get_var($participant_count_query . ' AND passed_selection_three=0');

			$selection_dates = array(
				3 => date_create(Helium::conf('selection_three_date')),
				2 => date_create(Helium::conf('selection_two_date')),
				1 => date_create(Helium::conf('selection_one_date')) );
			$now = new DateTime('now');
			
			$next_selection_stage = 4;
			foreach ($selection_dates as $n => $date) {
				if ($now < $date)
					$next_selection_stage = $n;
			}
			$this['next_selection_stage'] = $next_selection_stage;
			
			$this->session['chapter_back_to'] = $this->params;
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * Everything about applicants/participants, all in one
	 */
	public function applicants() {
		$this->require_authentication();

		// Utility variables
		$db = Helium::db();
		$constraints = array();

		// Here's how this works:
		// 1. Read filter from query string
		//    If there is no filter, apply default filter
		// 2. Display applicants based on params[view]
		//    view=stats or view=list
		//    If there is no view param, apply default view.

		// Filtering
		// Final product: $constraints

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
		$current_stage = $this->params['stage'] ? $this->params['stage'] : 'active';
		switch ($current_stage) {
			case 'incomplete':
				$constraints[] = 'confirmed=0 AND finalized=0 AND expires_on > NOW()';
				break;
			case 'expired':
				$constraints[] = 'confirmed=0 AND finalized=0 AND expires_on < NOW()';
				break;
			case 'confirmed':
				$constraints[] = 'confirmed=1';
				break;
			case 'finalized':
				$constraints[] = 'finalized=1';
				break;
			case 'not_yet_confirmed':
				$constraints[] = 'confirmed=0 AND finalized=1';
				break;
			case 'active':
				$constraints[] = 'expires_on > NOW() OR finalized=1';
				break;
			case 'selection_1':
				$constraints[] = 'finalized=1';
				break;
			case 'failed_selection_1':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_one=0)';
				break;
			case 'selection_2':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_one=1)';
				break;
			case 'failed_selection_2':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_two=0 AND passed_selection_one=1)';
				break;
			case 'selection_3':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_two=1)';
				break;
			case 'failed_selection_3':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_two=0 AND passed_selection_two=1 AND passed_selection_one=1)';
				break;
			case 'national_selection':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_selection_three=1)';
				break;
			case 'national_candidate':
				$constraints[] = 'id IN (SELECT applicant_id FROM participants WHERE passed_national_selection=1)';
				break;
		}

		// Other, free-text filters
		$filter = $this->params;
		if ($filter['name'] || $filter['school_name'] || $filter['combo']) {
			$current_stage = 'search';
			$search_active = true;
			$search_title = array();

			// Filter by school
			if ($filter['school_name']) {
				$sanitized_school_name = Applicant::sanitize_school($filter['school_name']);
				$disjunctions = array();
				$disjunctions[] = $db->prepare("sanitized_high_school_name='%s'", $sanitized_school_name);
				$disjunctions[] = $db->prepare("`sanitized_high_school_name` LIKE '%%%s%%'", str_replace(' ', '%', $filter['school_name']));
				$constraints[] = '(' . implode(') OR (', $disjunctions) . ')';
				$search_title[] = $school_name;
			}

			// Filter by name
			if ($filter['name']) {
				$constraints[] = $db->prepare("`sanitized_full_name` LIKE '%%%s%%'", str_replace(' ', '%', $filter['name']));
				$search_title = $filter['name'];
			}

			// Filter by name OR username OR test_id
			if ($filter['combo']) {
				$disjunctions = array();
				$disjunctions[] = $db->prepare("`sanitized_full_name` LIKE '%%%s%%'", str_replace(' ', '%', $filter['combo']));
				$disjunctions[] = $db->prepare("`user_id` IN (SELECT id FROM users WHERE username='%s')", $filter['combo']);
				$disjunctions[] = $db->prepare("`test_id`='%s'", $filter['combo']);
				$constraints[] = '(' . implode(') OR (', $disjunctions) . ')';
				$search_title = $filter['name'];
			}
		}

		// View selection
		$view = $this->params['view'];
		$acceptable_views = array('list', 'stats');
		if (!$view || !in_array($view, $acceptable_views)) {
			$view = 'list';
		}

		switch ($view) {
			case 'list':
				// List-specific magic here: pagination, etc.
				$applicants = Applicant::find();
				foreach ($constraints as $constraint) {
					$applicants->narrow('(' . $constraint . ')');
				}

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

				// -- Ordering --
				$order_by = $this->params['order_by'] ? $this->params['order_by'] : 'test_id';
				$order = $this->params['order'] == 'desc' ? 'desc' : 'asc';
				$applicants->set_order_by($order_by);
				$applicants->set_order($order);

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
				$this['count_all'] = $applicants->count_all();
				$this['current_order'] = $order;
				$this['current_order_by'] = $order_by;
				break;
			case 'stats':

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
					'acceleration_class' => array(
						'type' => 'pie',
						'field' => 'in_acceleration_class',
						'partition' => 'applicant_program_choices'
					),
					'program_choices' => array(
						'type' => 'pie',
						'field' => "IF(program_afs, IF(program_green_academy, IF(program_yes, 'AFS, YES, dan GA-SP', 'AFS dan GA-SP'), IF(program_yes, 'AFS dan YES', 'AFS saja')), 'Tidak mengisi program')",
						'partition' => 'applicant_program_choices'
					),
					'school_funding_type' => array(
						'type' => 'pie',
						'field' => "IF(sanitized_high_school_name != '', sanitized_high_school_name LIKE '%Negeri%', '')",
					),
					'school_education_type' => array(
						'type' => 'pie',
						'field' => "
							IF(in_pesantren,
								'Pesantren',
								IF(sanitized_high_school_name != '',
									IF(sanitized_high_school_name LIKE 'SMA%',
										'SMA',
										IF (sanitized_high_school_name LIKE 'SMK %',
											'SMK',
											IF(sanitized_high_school_name LIKE 'MA %',
												'MA',
												'Sekolah internasional, home schooling, atau jenis sekolah lainnya'
											)
										)
									),
									''
								)
							)",
						'partition' => 'applicant_program_choices'
					),
				);

				foreach (Helium::conf('partners') as $region => $countries) {
					$n_countries = count($countries);
					$key_base = 'pref_' . $region . '_';
					for ($i = 1; $i <= $n_countries; $i++) {
						$key = $key_base . $i;
						$stats[$key] = array(
							'type' => 'pie',
							'field' => $key,
							'partition' => 'applicant_program_choices'
						);
					}
				}

				if ($constraints)
					$constraint_string = '(' . implode(') AND (', $constraints) . ')';
				else
					$constraint_string = '1';
				
				foreach ($stats as $key => $group) {
					unset($type, $field, $partition, $base_query, $join_string);
					extract($group);
					if (!$base_query)
						$base_query = "SELECT COUNT(*) AS rows, %s AS value FROM applicants %s WHERE %s GROUP BY %s ORDER BY rows DESC";

					if ($partition)
						$join_string = $db->prepare("INNER JOIN `%s` ON `%s`.applicant_id = applicants.id", $partition, $partition);

					$the_query = sprintf($base_query, $field, $join_string, $constraint_string, $field);
					$results = $db->get_results($the_query);
					
					$series = array();
					$total = 0;
					if (is_array($results)) {
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
					}
					
					$stats[$key]['data'] = compact('series', 'total');
				}
				
				// Other contry preferences - special stats
				$additional_country_preferences_base_query =
					"SELECT country_preference_other FROM applicants INNER JOIN applicant_program_choices ON applicant_program_choices.applicant_id = applicants.id WHERE %s";

				$the_query = sprintf($additional_country_preferences_base_query, $constraint_string);
				$values = $db->get_col($the_query);
				if (is_array($values)) {
					$series = array();
					$total = 0;
					foreach ($values as $country_preferences) {
						$countries = trim($country_preferences);
						$countries = preg_split("/\s*[;,\/]\s*/", $countries);

						foreach ($countries as $country) {
							if ($series[$country])
								$series[$country]++;
							else
								$series[$country] = 1;

							$total++;
						}
					}
					$stats['country_preferences_other'] = array();
					$stats['country_preferences_other']['data'] = compact('series', 'total');
				}
				else {
					$stats['country_preferences_other'] = array('series' => array(), 'total' => 0);
				}

				// country preferences - special stats
				/*
				$countries = $db->get_col("SELECT country_preference_1, COUNT(*) AS rows  FROM applicant_program_choices WHERE country_preference_1 IS NOT NULL AND country_preference_1 != '' GROUP BY country_preference_1 ORDER BY rows DESC");
				$country_stats = array();
				foreach ($countries as $country) {
					$numbers = array();
					for ($i = 1; $i <= 10; $i++) {
						$cq = "SELECT COUNT(*) FROM applicant_program_choices INNER JOIN applicants ON applicants.id=applicant_program_choices.applicant_id WHERE country_preference_$i='$country' AND $constraint_string";
						$numbers[$i] = $db->get_var($cq);
					}
					$country_stats[$country] = $numbers;
				}
				*/
				
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
				
				if (!$other_countries)
					$other_countries = array();
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
				$this['count_all'] = $db->get_var("SELECT COUNT(*) FROM applicants WHERE $constraint_string");
				break;
		}

		$this['search'] = $this->params;
		$this['current_stage'] = $current_stage;
		$this['view'] = $view;
		// $this['search_title'] = $search_title ? implode(', ', $search_title) : '';
		$this['search_active'] = $search_active;
	}

	public function view_applicant() {
		$this->require_authentication();
		$this->require_role('chapter_staff');

		if ($id = $this->params['id']) {
			$applicant = Applicant::find($id);
		}
		elseif ($test_id = $this->params['test_id']) {
			$applicant = Applicant::find_by_test_id($test_id);
		}
		elseif ($username = $this->params['username']) {
			$db = Helium::db();
			// $id = $db->get_var($db->prepare("SELECT applicants.id FROM applicants INNER JOIN users ON applicants.user_id=users.id WHERE username='%s'", $username));
			$applicant = Applicant::find($db->prepare("user_id IN (SELECT id FROM users WHERE username='%s')", $username));
		}
		
		$this->session['applicant_back_to'] = $this->params;
		
		if (!$applicant) {
			$error = 'not_found';
		}
		else {
			$this['applicant'] = $applicant;
			$this['picture'] = $applicant->picture;
		}
		
		if (!$error && !$this->user->capable_of('national_admin') && ($applicant->chapter_id != $this->user->chapter_id)) {
			$error = 'forbidden';
		}
		
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
				$back_to = array('controller' => 'chapter', 'action' => 'applicants');

			$this['back_to'] = $back_to;

			$this['can_edit'] = $this->user->capable_of('chapter_admin'); // && !$applicant->finalized;
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * @deprecated
	 */
	public function create() {
		$this->require_role('admin');

		$this['form'] = $form = new FormDisplay;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Validate input

			// Check 1: Required chapter fields
			if (!$error) {
				$required = array('chapter_code', 'chapter_name');
				foreach ($required as $r) {
					if (!trim($_POST[$r]) || !trim($_POST['user']['username'])) {
						$errors = 'incomplete_form';
						break;
					}
				}
			}

			// Check 2: Existing chapter code?
			if (!$error) {
				$chapter_code = strtoupper(trim($_POST['chapter_code']));
				$check = Chapter::find(compact('chapter_code'));
				if ($check->count_all()) {
					$error = 'chapter_code_conflict';
					$this['chapter_code'] = $chapter_code;
				}
			}

			// Check 3: Existing chapter name?
			if (!$error) {
				$chapter_name = trim($_POST['chapter_name']);
				$check = Chapter::find(compact('chapter_name'));
				if ($check->count_all()) {
					$error = 'chapter_name_conflict';
					$this['chapter_name'] = $chapter_name;
				}
			}

			// Check 4: Password match
			if (!$error) {
				if ($_POST['user']['password'] != $_POST['user']['confirm_password']) {
					$error = 'password_mismatch';
				}
			}

			// Check 5: Password length
			if (!$error) {
				if (strlen($_POST['user']['password']) < 8) {
					$error = 'password_too_short';
				}
			}

			$db = Helium::db();

			try {
				$db->autocommit(false);

				// Validation passed
				if (!$error) {
					$chapter = new Chapter;
					$proc = new FormProcessor;
					$proc->associate($chapter);
					$proc->commit();

					if ($chapter->facebook_url == 'http://facebook.com/')
						$chapter->facebook_url = '';
					if ($chapter->site_url == 'http://')
						$chapter->site_url = '';
					if ($chapter->twitter_username{0} == '@')
						$chapter->twitter_username = ltrim($chapter->twitter_username, '@');

					$save = $chapter->save();
					$db->commit();
					if (!$save) {
						$error = 'chapter_addition_failed';
					}
				}

				// Chapter addition succeeded
				if (!$error) {
					$user = new User;
					$user->chapter_id = $chapter->id;
					$user->username = trim($_POST['user']['username']);
					$user->email = $chapter->chapter_email;
					$user->set_password($_POST['user']['password']);
					$user->role = 4;
					$save = $user->save();
					$db->commit();
					if (!$save) {
						$error = 'user_addition_failed';
						$chapter->destroy();
					}
				}
			}
			catch (HeliumException $e) {
				$db->rollback();
				$db->autocommit(true);

				$error = 'user_addition_failed';
			}

			if (!$error) {
				// Everything went well
				if (!$_POST['create_again'])
					$this->http_redirect(array('controller' => 'chapter', 'action' => 'index'));
				else {
					$this['success'] = true;
				}
			}

			// Something wrong happened
			else {
				$this['error'] = $error;

				// Restore form values
				unset($_POST['user']['password'], $_POST['user']['confirm_password']);
				$form->feed($_POST);
			}
		}

		$this['timezones'] = array('Asia/Jakarta' => 'WIB', 'Asia/Ujung_Pandang' => 'WITA', 'Asia/Jayapura' => 'WIT');
	}

	/**
	 * Control panel for a chapter (or national office, for that matter)
	 * @deprecated
	 */
	public function view() {
		$this->require_role('chapter_staff');

		if ($this->user->capable_of('national_admin')) {
			$chapter_code = strtoupper($this->params['chapter_code']);
			$chapter_id = $this->params['id'];
			if ($chapter_id) {
				$chapter = Chapter::find($chapter_id);
			}
			elseif ($chapter_code) {
				$chapter = Chapter::find(compact('chapter_code'));
				$chapter = $chapter->first();
			}
			else
				$chapter = $this->user->chapter;
		}
		elseif ($this->user->capable_of('chapter_staff')) {
			$chapter = $this->user->chapter;
		}
		else {
			$error = 'forbidden';
		}

		if (!$error && !$chapter) {
			$error = 'not_found';
		}

		if (!$error) {
			$this['chapter'] = $chapter;
			$this['national'] = $chapter->is_national_office();
			foreach ($chapter->_columns() as $col) {
				$this[$col] = $chapter->$col;
			}

			if ($this->can_register()) {

				$this['registration_codes'] = $chapter->registration_codes;
				$this['code_count'] = $chapter->registration_codes->count_all();

				$ac = clone $chapter->registration_codes;
				$ac->narrow('availability=0');
				$this['ac'] = $ac;
				$this['activated_code_count'] = $ac->count_all();

				$now = new HeliumDateTime;
				$ec = clone $chapter->registration_codes;
				$ec->narrow("availability=1 AND expires_on < '$now'");
				$this['expired_code_count'] = $ec->count_all();

				$vc = clone $chapter->registration_codes;
				$vc->narrow("availability=1 AND expires_on > '$now'");
				$this['available_code_count'] = $vc->count_all();

				$this['applicants'] = $chapter->applicants;
				$this['total_applicant_count'] = $chapter->applicants->count_all();

				$aa = clone $chapter->applicants;
				$aa->narrow("(confirmed=1 OR expires_on > '$now')");
				$this['active_applicant_count'] = $aa->count_all();

				$ca = clone $chapter->applicants;
				$ca->narrow('confirmed=1');
				$this['confirmed_applicant_count'] = $ca->count_all();

				$this['applicant_tipping_point'] = $ca->count_all() == $aa->count_all();

				$fa = clone $chapter->applicants;
				$fa->narrow('confirmed=0 AND finalized=1');
				$this['finalized_applicant_count'] = $fa->count_all();

				$this['incomplete_applicant_count'] = $aa->count_all() - $fa->count_all() - $ca->count_all();

				$ea = clone $chapter->applicants;
				$ea->narrow("confirmed=0 AND finalized=0 AND expires_on < '$now'");
				$this['expired_applicant_count'] = $ea->count_all();

				$na = clone $chapter->applicants;
				$na->narrow("confirmed=0 AND finalized=1 AND expires_on <'$now'");
				$this['anomalous_applicant_count'] = $na->count_all();

				$na = clone $chapter->applicants;
				$na->set_order_by('id');
				$na->set_order('DESC');
				$na->narrow("sanitized_full_name != ''");
				$na->set_batch_length(10);

				$this['na'] = $na;
			}
			else {
				$db = Helium::db();
				$pcq = 'SELECT COUNT(*) FROM participants INNER JOIN applicants ON applicants.id=participants.applicant_id WHERE 1 ';
				if (!$this['national'])
					$pcq .= 'AND chapter_id=' . $this->user->chapter_id;

				$this['participant_count'] = $db->get_var($pcq);
				$this['participant_count_2'] = $db->get_var($pcq . ' AND passed_selection_one=1');
				$this['participant_count_3'] = $db->get_var($pcq . ' AND passed_selection_two=1');
				$this['participant_count_4'] = $db->get_var($pcq . ' AND passed_selection_three=1');
				$selection_fields = 

				$selection_dates = array(
					3 => date_create(Helium::conf('selection_three_date')),
					2 => date_create(Helium::conf('selection_two_date')),
					1 => date_create(Helium::conf('selection_one_date')), );
				$now = new DateTime('now');
				
				foreach ($selection_dates as $n => $date) {
					if ($now < $date)
						$next_selection_stage = $n;
				}
				$this['next_selection_stage'] = $next_selection_stage;
			}
			
			$this->session['chapter_back_to'] = $this->params;
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * Edit a chapter's contact details (address, etc)
	 */
	public function edit() {
		$this->require_authentication();

		if ($this->user->capable_of('national_admin')) {
			$chapter_code = strtoupper($this->params['chapter_code']);
			$chapter_id = $this->params['id'];
			if ($chapter_id) {
				$chapter = Chapter::find($chapter_id);
			}
			else {
				$chapter = Chapter::find(compact('chapter_code'));
				$chapter = $chapter->first();
			}
		}
		elseif ($this->user->capable_of('chapter_staff')) {
			$chapter = $this->user->chapter;
		}
		else {
			$error = 'forbidden';
		}

		if (!$error && !$chapter) {
			$error = 'not_found';
		}

		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			// Form submission handling
			$proc = new FormProcessor;
			$proc->add_uneditables('chapter_code', 'chapter_name');
			$proc->associate($chapter);
			$proc->commit();

			// Parsing for internet-related fields
			if ($chapter->facebook_url == 'http://facebook.com/')
				$chapter->facebook_url = '';
			if ($chapter->site_url == 'http://')
				$chapter->site_url = '';
			if ($chapter->twitter_username{0} == '@')
				$chapter->twitter_username = ltrim($chapter->twitter_username, '@');

			$chapter->parse_depots_yaml();
			
			$save = $chapter->save();
			if (!$save)
				$error = 'edit_failed';
		}

		if (!$error) {
			// Normal content
			$this['chapter'] = $chapter;

			$form = new FormDisplay;
			$form->associate($chapter);
			$this['form'] = $form;

			$this['timezones'] = array('Asia/Jakarta' => 'WIB', 'Asia/Ujung_Pandang' => 'WITA', 'Asia/Jayapura' => 'WIT');

			$this['national'] = $chapter->is_national_office();

			$this['back_to'] = $this->session['chapter_back_to'] ? $this->session['chapter_back_to'] : array('controller' => 'chapter', 'action' => 'view', 'id' => $chapter_id);
			$this['back_to'] = array('controller' => 'chapter', 'action' => 'view', 'id' => $chapter_id);
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * Migrate all finalized applicants to the participants table
	 */
	public function migrate_applicants() {
		$this->require_role('national_admin');
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$db = Helium::db();

			if (!$db->get_var('SELECT COUNT(*) FROM participants')) {
				$query = "INSERT INTO participants (applicant_id) (SELECT id FROM applicants WHERE finalized=1)";
				$db->query($query);
			}
		}
		
		$this->http_redirect(array('action' => 'view'));
	}

	public function participant_tab() {
		$q = 'SELECT test_id, sanitized_full_name, sanitized_high_school_name, grades_y10t1_average FROM applicants INNER JOIN applicant_secondary_school_grade_history ON applicants.id=applicant_secondary_school_grade_history.applicant_id WHERE finalized=1 ';
		
		if ($this->session->user->capable_of('national_admin') && $this->params['chapter_id'])
			$q .= 'AND chapter_id=' . (int) $this->params['chapter_id'];
		elseif (!$this->session->user->capable_of('national_admin'))
			$q .= 'AND chapter_id=' . (int) $this->user->chapter_id;
			
		$q .= ' ORDER BY test_id ASC';

		$this['participants'] = Helium::db()->get_results($q);
	}
}
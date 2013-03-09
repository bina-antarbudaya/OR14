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
	 */
	public function dashboard() {
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

		// TODO change this
		$current_phase = 'registration';

		$this['current_phase'] = $current_phase;

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
				$fa->narrow('finalized=1');
				$this['finalized_applicant_count'] = $fa->count_all();

				$this['incomplete_applicant_count'] = $aa->count_all() - $fa->count_all();

				$nca = clone $chapter->applicants;
				$nca->narrow('finalized=1 && confirmed=0');
				$this['not_yet_confirmed_applicant_count'] = $nca->count_all();

				$ea = clone $chapter->applicants;
				$ea->narrow("confirmed=0 AND finalized=0 AND expires_on < '$now'");
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
	 * Everything about applicants/participants, all in one
	 */
	public function applicants() {
		// Utility variables
		$db = Helium::db();
		$app = 'Applicant'; // change to BetterApplicant for optimization

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
				$constraints[] = 'expires_on <= NOW() AND confirmed=0 AND finalized=1';
				break;
			case 'active':
			case 'selection_1':
				$constraints[] = 'expires_on > NOW() OR finalized=1';
				break;
		}

		// Specific filters -- through a custom search query
		$filter = $this->params['filter'];
		if (is_array($filter)) {
			$current_stage = 'search';
			$search_title = array();

			// Filter by school
			if ($filter['school_name']) {
				$constraints[] = "sanitized_high_school_name='" . $filter['school_name'] . "'";
				$search_title[] = $filter['school_name'];
				unset($filter['school_name']);
			}

			// Filter by name
			if ($filter['name']) {
				$constraints[] = $db->prepare("`sanitized_full_name` LIKE '%%%s%%'", str_replace(' ', '%', $this->params['name']));
				$search_title = $filter['name'];
				unset($filter['name']);
			}

			// For other filters, merge them into constraints
			foreach ($filter as $k => $v) {
				$constraints[$k] = $v;
			}
		}

		// View selection
		$view = $this->params['view'];
		$acceptable_views = array('list', 'stats');
		if (!$view && !in_array($view, $acceptable_views)) {
			$view = 'list';
		}

		switch ($view) {
			case 'list':
				// List-specific magic here: pagination, etc.
				$applicants = $app::find('chapter_id=5');
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
				);

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
				break;
		}

		$this['current_stage'] = $current_stage;
		$this['view'] = $view;
		$this['search_title'] = $search_title ? implode(', ', $search_title) : '';
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
		$q = 'SELECT test_id, sanitized_full_name, sanitized_high_school_name, grades_y10t1_average FROM applicants INNER JOIN applicant_secondary_school_grade_history ON applicants.id=applicant_secondary_school_grade_history.applicant_id WHERE id IN (SELECT applicant_id FROM participants) ';
		
		if ($this->session->user->capable_of('national_admin') && $this->params['chapter_id'])
			$q .= 'AND chapter_id=' . (int) $this->params['chapter_id'];
		elseif (!$this->session->user->capable_of('national_admin'))
			$q .= 'AND chapter_id=' . (int) $this->user->chapter_id;
			
		$q .= ' ORDER BY test_id ASC';

		$this['participants'] = Helium::db()->get_results($q);
	}
}
<?php

/**
 * ChapterController
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package chapter
 */
class ChapterController extends AppController {
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
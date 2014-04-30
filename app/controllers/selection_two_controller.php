<?php

class SelectionTwoController extends AppController {
	public function init() {
		$this->require_role('chapter_admin');
		if (!Helium::db('SELECT COUNT(*) FROM participants'))
			$this->http_redirect(array('chapter', 'migrate_applicants'));
	}

	public function create_batch() {
		/*
		On GET, ask for:
			1. List of Test IDs who pass selection 1
			2. Announcement date for these test IDs
		On POST,
			1. Create new SelectionTwoBatch, with announcement date 
			2. Update participants table, set selection_two_batch_id
			   to the new SelectionTwoBatch's ID for the listed participants.
			3. Redirect to edit_batch page for the batch
		*/
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// POST
			$db = Helium::db();

			if (!$_POST['token']) {
				$this['stage'] = 'confirm';

				$batch = new SelectionTwoBatch;
				$proc = new FormProcessor;
				$proc->associate($batch);
				$proc->commit();

				// Parse test IDs, get SQL query portion
				$test_ids = Applicant::split_test_ids($_POST['test_ids']);
				$test_ids_escaped = array();
				foreach ($test_ids as $test_id)
					$test_ids_escaped[] = $db->escape($test_id);
				$test_ids_string = "('" . implode("','", $test_ids_escaped) . "')";
				
				
				if (!$this->user->capable_of('national_admin'))
					$chapter_id = $this->user->chapter_id;
				else
					$chapter_id = $db->escape($_POST['chapter_id']);
				
				$applicant_ids_query = "SELECT id, test_id, sanitized_full_name FROM applicants WHERE test_id IN $test_ids_string AND chapter_id=$chapter_id AND finalized=1";
				
				$participant_ids_query = "SELECT id FROM participants WHERE applicant_id IN (SELECT id FROM applicants WHERE test_id IN $test_ids_string AND chapter_id=$chapter_id)";

				$participant_ids = $db->get_col($participant_ids_query);
				if (!$participant_ids)
					$participant_ids_string = '()';
				else
					$participant_ids_string = "('" . implode("','", $participant_ids) . "')";

				$applicant_ids = $db->get_col($applicant_ids_query);
				if (!$applicant_ids)
					$applicant_ids_string = '()';
				else
					$applicant_ids_string = "('" . implode("','", $applicant_ids) . "')";

				$token = mt_rand();

				$this->session['s2'] = array(
					'applicant_ids_string' => $applicant_ids_string,
					'participant_ids_string' => $participant_ids_string,
					'announcement_date_string' => (string) $batch->announcement_date,
					'announcement_date_follows_national' => $batch->announcement_date_follows_national,
					'chapter_id' => $chapter_id,
					'token' => $token
				);

				$participant_ids = $db->get_col($participant_ids_query);
				$this['participants'] = $db->get_results($applicant_ids_query);
				$this['token'] = $token;
			}
			elseif ($_POST['token'] == $this->session['s2']['token']) {
				try {
					$prev_autocommit = $db->autocommit();
					$db->autocommit(false);

					extract($this->session['s2']);

					$batch = new SelectionTwoBatch;
					$batch->announcement_date = new HeliumDateTime($announcement_date_string);

					if (!$this->user->capable_of('national_admin'))
						$batch->chapter_id = $this->user->chapter_id;
					else
						$batch->chapter_id = $chapter_id;

					Chapter::ensure_applicants_migrated($batch->chapter_id);

					$batch->save();

					$batch_id = $batch->id;

					// Use a subquery to update participants table
					$participants_update = $db->query("UPDATE participants SET passed_selection_one=1, selection_two_batch_id=$batch_id, personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE applicant_id IN $applicant_ids_string");

					$participants_mask = $db->query("UPDATE participants SET passed_selection_one=0 WHERE applicant_id IN (SELECT id FROM applicants WHERE chapter_id='$batch->chapter_id') AND passed_selection_one IS NULL");
					
					$db->commit();

					$db->autocommit($prev_autocommit);
				}
				catch (HeliumException $e) {
					$db->rollback();

					$e->output();
					$error = 'db_fail'; // Database failure. Allow user to try again.
				}

				if (!$error) {
					$this->http_redirect(array('controller' => 'selection_two', 'action' => 'edit_batch', 'id' => $batch_id, 'new' => 1));
				}
			}
		}
		// GET
		
		if (!$batch)
			$batch = new SelectionTwoBatch;
		$form = new FormDisplay;
		$form->associate($batch);
		$this['batch'] = $batch;
		$this['form'] = $form;
		
		$this['error'] = $error;
		
		if ($this->session->user->capable_of('admin')) {
			$this['can_choose_chapter'] = true;
			$chapters = Chapter::find();
			$tch = array();
			foreach ($chapters as $c) {
				if (!$c->is_national_office()) {
					$id = $c->id;
					$ch[$id] = $c->chapter_name;
				}
			}

			$this['chapters'] = $ch;
		}
	}

	public function view_batch() {
		/*
		List of chambers and shifts
		Param: id
		*/
		$this->require_role('chapter_admin');

		$batch = SelectionTwoBatch::find($this->params['id']);
		if (!$batch) {
			$error = 'not_found';
		}
		elseif (!$error && !$this->user->has_access_to($batch)) {
			$error = 'unauthorized';
		}
		
		if (!$error) {
			$db = Helium::db();
			$rows = $db->get_results('SELECT participants.*, test_id, sanitized_full_name full_name, sanitized_high_school_name high_school_name FROM participants INNER JOIN applicants ON participants.applicant_id=applicants.id WHERE selection_two_batch_id=' . $batch->id);
			$p = $e = array();
			foreach ($rows as $row) {
				$pcn = $row->personality_chamber_number;
				$ecn = $row->english_chamber_number;
				
				// Personality
				
				if (!$p[$pcn])
					$p[$pcn] = array(
						'local_batch_number' => $batch->local_batch_number,
						'type' => 'P',
						'order' => array()
					);

				$p[$pcn]['order'][$row->personality_turn_number] = array(
					'applicant_id' => $row->applicant_id,
					'test_id' => $row->test_id,
					'full_name' => $row->full_name,
					'high_school_name' => $row->high_school_name,
					'ecn' => $ecn,
					'etn' => $row->english_turn_number
				);
				
				if (!$e[$ecn])
					$e[$ecn] = array(
						'local_batch_number' => $batch->local_batch_number,
						'type' => 'E',
						'order' => array()
					);

				$e[$ecn]['order'][$row->english_turn_number] = array(
					'applicant_id' => $row->applicant_id,
					'test_id' => $row->test_id,
					'full_name' => $row->full_name,
					'high_school_name' => $row->high_school_name,
					'pcn' => $pcn,
					'ptn' => $row->personality_turn_number
				);
			}
			
			ksort($p);
			ksort($e);
			foreach ($p as $k => $pp) {
				ksort($p[$k]['order']);
			}
			foreach ($e as $k => $ee) {
				ksort($e[$k]['order']);
			}
			
			$this['p'] = $p;
			$this['e'] = $e;
		}
	}
	
	public function edit_batch() {
		/*
		Edit the dates of a batch
		or re-assign chambers and shifts
		Param: id
		*/
		$this->require_role('chapter_admin');

		if (!$this->params['id'])
			$this->params['id'] = $batch_id = Helium::db()->get_var('SELECT id FROM selection_two_batches WHERE chapter_id=' . $this->user->chapter_id);
		$batch = SelectionTwoBatch::find($this->params['id']);
		if (!$batch) {
			$error = 'not_found';
		}
		elseif (!$error && !$this->user->has_access_to($batch)) {
			$error = 'unauthorized';
		}
		
		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$proc = new FormProcessor;
			$proc->associate($batch);
			$proc->add_uneditables('chapter_id');
			$proc->commit();
			$batch->save();
			
			// Do room assignment magic
			if ($_POST['pcc'] != $batch->get_personality_chamber_count() || $_POST['ecc'] != $batch->get_english_chamber_count()) {
				$db = Helium::db();
				
				$prev_autocommit = $db->autocommit();
				try {
					$db->autocommit(false);

					$db->query('UPDATE participants SET personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE selection_two_batch_id=' . $batch->id);

					$participant_ids = $db->get_col('SELECT id FROM participants WHERE selection_two_batch_id=' . $batch->id);
					$assignments = $this->get_assignments($participant_ids, $_POST['pcc'], $_POST['ecc']);
					// var_dump($assignments); exit;
					foreach ($assignments as $participant_id => $a) {
						extract($a);
						$q = $db->prepare('UPDATE participants SET personality_chamber_number=%d, english_chamber_number=%d, personality_turn_number=%d, english_turn_number=%d WHERE id=%d', $personality_chamber_number, $english_chamber_number, $personality_turn_number, $english_turn_number, $participant_id);
						$db->query($q);
					}
					
					$db->commit();
				}
				catch (HeliumException $e) {
					$db->rollback();
					$e->output();
				}
				
				$db->autocommit(false);
			}
		}
		
		$db = Helium::db();
		
		$form = new FormDisplay;
		$form->associate($batch);
		$this['form'] = $form;
		$this['batch'] = $batch;
		$this['batch_id'] = $batch->id;
		
		if ($this->session->user->capable_of('admin')) {
			$this['can_choose_chapter'] = true;
			$chapters = Chapter::find();
			$tch = array();
			foreach ($chapters as $c) {
				if (!$c->is_national_office()) {
					$id = $c->id;
					$ch[$id] = $c->chapter_name;
				}
			}

			$this['chapters'] = $ch;
		}
		
		$this['participant_count'] = Helium::db()->get_var('SELECT COUNT(*) FROM participants WHERE selection_two_batch_id=' . $batch->id);
	}
	
	public function index() {
		$bc = $this->get_batch_count();
		if ($bc == 0)
			$this->http_redirect(array('controller' => 'selection_two', 'action' => 'create_batch'));
		elseif ($bc == 1) {
			$batch_id = Helium::db()->get_var('SELECT id FROM selection_two_batches WHERE chapter_id=' . $this->user->chapter_id);
			$this->http_redirect(array('controller' => 'selection_two', 'action' => 'edit_batch', 'id' => $batch_id));
		}
		else {
			$batches = SelectionTwoBatch::find(array('chapter_id' => $this->user->chapter_id));
		}
		
		$this['batches'] = $batches;
	}
	
	public function delete_batch() {
		$batch = SelectionTwoBatch::find($this->params['id']);
		if (!$batch) {
			$error = 'not_found';
		}
		elseif (!$error && !$this->user->has_access_to($batch)) {
			$error = 'unauthorized';
		}
		
		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$db = Helium::db();
			$db->query("UPDATE participants SET passed_selection_one=NULL, selection_two_batch_id=NULL, personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE selection_two_batch_id={$batch->id}");
			$batch->destroy();
			$this->http_redirect(array('controller' => 'selection_two', 'action' => 'index'));
		}
	}

	protected function get_batch_count($chapter_id = 0) {
		if (!$chapter_id)
			$chapter_id = $this->user->chapter_id;

		return (int) Helium::db()->get_var('SELECT COUNT(*) FROM selection_two_batches WHERE chapter_id=' . (int) $chapter_id);
	}

	protected function get_assignments(Array $participant_ids, $pcc, $ecc) {
		$participant_count = count($participant_ids);
		
		$personality_min = 1;
		$personality_max = (int) $pcc;
		$english_min = 1;
		$english_max = (int) $ecc;

		$highest_chamber_count = ($personality_max > $english_max) ? $personality_max : $english_max;

		// shifts are used to separate the order of interviewing for a participant.
		// one chamber starts from 1, the other starts from half the total number of shifts then loops.
		$shift_min = 1;
		$shift_max = ceil($participant_count / $highest_chamber_count);
		$shift_halfpoint = ceil($shift_max / 2);

		// loop variables
		$current_personality = $personality_min;
		$current_english = $english_min;
		$current_shift = $shift_min;
		$global_assignments = array();
		
		$shifts = array();
		for ($i = $shift_min; $i <= $shift_max; $i++)
			$shifts[$i] = array();

		$personality_order = array();
		for ($i = $personality_min; $i <= $personality_max; $i++)
			$personality_order[$i] = $shifts;

		$english_order_1 = array();
		$english_order_2 = array();
		for ($i = $english_min; $i <= $english_max; $i++)
			$english_order_1[$i] = $english_order_2[$i] = $shifts;

		// loop 1: shift
		shuffle($participant_ids);
		foreach ($participant_ids as $participant_id) {
			$participant_id = (int) $participant_id;

			// current_element
			$assignment = array(
				'participant_id' => $participant_id,
				'shift' => $current_shift,
				'personality' => 0,
				'english' => 0
			);

			$global_assignments[$participant_id] = $assignment;
			$shifts[$current_shift][] = $participant_id;

			if ($current_shift == $shift_max)
				$current_shift = $shift_min;
			else
				$current_shift++;
		}
		ksort($shifts);
		ksort($global_assignments);

		// loop 2: chambers
		foreach ($shifts as $k => $participant_ids) {
			foreach ($participant_ids as $participant_id) {
				$global_assignments[$participant_id]['personality'] = $current_personality;
				$global_assignments[$participant_id]['english'] = $current_english;

				$assignment = $global_assignments[$participant_id];

				// push assignment into chamber sorting tables
				$current_shift = $assignment['shift'];
				
				$personality_order[$current_personality][$current_shift][] = $assignment;

				if ($current_shift > $shift_halfpoint)
					$english_order_1[$current_english][$current_shift][] = $assignment;
				else
					$english_order_2[$current_english][$current_shift][] = $assignment;

				// iterate chamber iterators

				foreach (array('personality', 'english') as $var) {
					$current_var = 'current_' . $var;
					$var_max = $var . '_max';
					$var_min = $var . '_min';
					if ($$current_var == $$var_max)
						$$current_var = $$var_min;
					else
						$$current_var++;
				}
			}
		}

		// order the interview schedule by shift
		$personality_schedules = array();
		foreach ($personality_order as $chamber => $shifts) {
			ksort($shifts);
			$chamber_schedule = array();
			foreach ($shifts as $assignments)
				foreach ($assignments as $assignment)
					$chamber_schedule[] = $assignment;

			$personality_schedules[$chamber] = $chamber_schedule;
		}
		ksort($personality_schedules);

		// for english chambers, we do something different
		$english_schedules = array();
		foreach ($english_order_1 as $chamber => $earlier_shifts) {
			ksort($earlier_shifts);
			$chamber_schedule = array();
			foreach ($earlier_shifts as $assignments)
				foreach ($assignments as $assignment)
					$chamber_schedule[] = $assignment;
			
			$later_shifts = $english_order_2[$chamber];
			if ($later_shifts) {
				ksort($later_shifts);
				foreach ($later_shifts as $assignments)
					foreach ($assignments as $assignment)
						$chamber_schedule[] = $assignment;
			}

			$english_schedules[$chamber] = $chamber_schedule;
		}
		ksort($english_schedules);

		$return = array();
		
		foreach ($global_assignments as $assignment) {
			extract($assignment);
			$return[$participant_id] = array(
				'personality_chamber_number' => $personality,
				'english_chamber_number' => $english
			);
		}
		foreach ($personality_schedules as $chamber => $assignments) {
			foreach ($assignments as $order => $assignment) {
				extract($assignment);
				$return[$participant_id]['personality_turn_number'] = $order + 1;
			}
		}
		foreach ($english_schedules as $chamber => $assignments) {
			foreach ($assignments as $order => $assignment) {
				extract($assignment);
				$return[$participant_id]['english_turn_number'] = $order + 1;
			}
		}

		return $return;
	}
}
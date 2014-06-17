<?php

class SelectionThreeController extends AppController {
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
			1. Create new SelectionThreeBatch, with announcement date 
			2. Update participants table, set selection_three_batch_id
			   to the new SelectionThreeBatch's ID for the listed participants.
			3. Redirect to edit_batch page for the batch
		*/
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// POST
			$db = Helium::db();

			if (!$_POST['token']) {
				$this['stage'] = 'confirm';

				$batch = new SelectionThreeBatch;
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
				
				$applicant_ids_query = "SELECT id, test_id, sanitized_full_name FROM applicants WHERE test_id IN $test_ids_string AND chapter_id=$chapter_id AND id IN (SELECT applicant_id FROM participants)";
				
				$participant_ids_query = "SELECT id FROM participants WHERE applicant_id IN (SELECT id FROM applicants WHERE test_id IN $test_ids_string AND chapter_id=$chapter_id)";

				$participant_ids = $db->get_col($participant_ids_query);
				if (!$participant_ids)
					$participants_ids_string = '()';
				else
					$participant_ids_string = "('" . implode("','", $participant_ids) . "')";

				$token = mt_rand();

				$this->session['s2'] = array(
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

					$batch = new SelectionThreeBatch;
					$batch->announcement_date = new HeliumDateTime($announcement_date_string);

					if (!$this->user->capable_of('national_admin'))
						$batch->chapter_id = $this->user->chapter_id;
					else
						$batch->chapter_id = $chapter_id;

					$batch->save();

					$batch_id = $batch->id;

					// Use a subquery to update participants table
					$participants_update = $db->query("UPDATE participants SET passed_selection_two=1, selection_three_batch_id=$batch_id, personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE id IN $participant_ids_string");
					
					$participants_mask = $db->query("UPDATE participants SET passed_selection_two=0 WHERE applicant_id IN (SELECT id FROM applicants WHERE chapter_id='$batch->chapter_id') AND passed_selection_two IS NULL");
					
					$db->commit();

					$db->autocommit($prev_autocommit);
				}
				catch (HeliumException $e) {
					$db->rollback();

					$e->output();
					$error = 'db_fail'; // Database failure. Allow user to try again.
				}

				if (!$error) {
					$this->http_redirect(array('controller' => 'selection_three', 'action' => 'edit_batch', 'id' => $batch_id, 'new' => 1));
				}
			}
		}
		// GET
		
		if (!$batch)
			$batch = new SelectionThreeBatch;
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

		$this['default_announcement_date'] = new HeliumDateTime(Helium::conf('selection_two_announcement_date'));
	}

	public function view_batch() {
		/*
		List of chambers and shifts
		Param: id
		*/
		$this->require_role('chapter_admin');

		$batch = SelectionThreeBatch::find($this->params['id']);
		if (!$batch) {
			$error = 'not_found';
		}
		elseif (!$error && !$this->user->has_access_to($batch)) {
			$error = 'unauthorized';
		}
		
		if (!$error) {
			$db = Helium::db();
			$rows = $db->get_results('SELECT participants.*, test_id, sanitized_full_name full_name, sanitized_high_school_name high_school_name FROM participants INNER JOIN applicants ON participants.applicant_id=applicants.id WHERE selection_three_batch_id=' . $batch->id);
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
			$this->params['id'] = $batch_id = Helium::db()->get_var('SELECT id FROM selection_three_batches WHERE chapter_id=' . $this->user->chapter_id);
		$batch = SelectionThreeBatch::find($this->params['id']);
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
			if (false) {
				$db = Helium::db();
				
				$prev_autocommit = $db->autocommit();
				try {
					$db->autocommit(false);

					$db->query('UPDATE participants SET personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE selection_three_batch_id=' . $batch->id);

					$participant_ids = $db->get_col('SELECT id FROM participants WHERE selection_three_batch_id=' . $batch->id);
					// $assignments = $this->get_assignments($participant_ids, $_POST['pcc'], $_POST['ecc']);
					// // var_dump($assignments); exit;
					// foreach ($assignments as $participant_id => $a) {
					// 	extract($a);
					// 	$q = $db->prepare('UPDATE participants SET personality_chamber_number=%d, english_chamber_number=%d, personality_turn_number=%d, english_turn_number=%d WHERE id=%d', $personality_chamber_number, $english_chamber_number, $personality_turn_number, $english_turn_number, $participant_id);
					// 	$db->query($q);
					// }
					
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
		
		$this['participant_count'] = Helium::db()->get_var('SELECT COUNT(*) FROM participants WHERE selection_three_batch_id=' . $batch->id);
	}
	
	public function index() {
		$bc = $this->get_batch_count();
		if ($bc == 0)
			$this->http_redirect(array('controller' => 'selection_three', 'action' => 'create_batch'));
		elseif ($bc == 1) {
			$batch_id = Helium::db()->get_var('SELECT id FROM selection_three_batches WHERE chapter_id=' . $this->user->chapter_id);
			$this->http_redirect(array('controller' => 'selection_three', 'action' => 'edit_batch', 'id' => $batch_id));
		}
		else {
			$batches = SelectionThreeBatch::find(array('chapter_id' => $this->user->chapter_id));
		}
		
		$this['batches'] = $batches;
	}
	
	public function delete_batch() {
		$batch = SelectionThreeBatch::find($this->params['id']);
		if (!$batch) {
			$error = 'not_found';
		}
		elseif (!$error && !$this->user->has_access_to($batch)) {
			$error = 'unauthorized';
		}
		
		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$db = Helium::db();
			$db->query("UPDATE participants SET passed_selection_one=NULL, selection_three_batch_id=NULL, personality_chamber_number=NULL, english_chamber_number=NULL, personality_turn_number=NULL, english_turn_number=NULL WHERE selection_three_batch_id={$batch->id}");
			$batch->destroy();
			$this->http_redirect(array('controller' => 'selection_three', 'action' => 'index'));
		}
	}

	protected function get_batch_count($chapter_id = 0) {
		if (!$chapter_id)
			$chapter_id = $this->user->chapter_id;

		return (int) Helium::db()->get_var('SELECT COUNT(*) FROM selection_three_batches WHERE chapter_id=' . (int) $chapter_id);
	}
}
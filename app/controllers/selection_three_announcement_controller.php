<?php

class SelectionThreeAnnouncementController extends AppController {
	public function init() {
		$this->require_role('chapter_admin');
		if (!Helium::db('SELECT COUNT(*) FROM participants'))
			$this->http_redirect(array('chapter', 'migrate_applicants'));
	}

	public function index() {
		/*
		If number of students who pass selection 3 = 0, redirect to announce().
		Else, list students who pass selection 3.
		*/
		
		$chapter_id = $this->user->chapter_id;
		$applicants = Applicant::find("id IN (SELECT applicant_id FROM participants WHERE passed_selection_three=1 AND applicant_id IN (SELECT id FROM applicants WHERE  chapter_id=$chapter_id))");
		$this['applicants'] = $applicants;
	}

	public function announce() {
		/*
		On GET, ask for:
			1. List of Test IDs who pass selection 3
		On POST,
			1. Set all chapter participant's passed_selection_three = 0.
			2. Update participants table, set passed_selection_three = 1 ID for the submitted participants.
			3. Redirect to edit_batch page for the batch
		*/
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// POST
			$db = Helium::db();

			if (!$_POST['token']) {
				$this['stage'] = 'confirm';

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

					if (!$chapter_id)
						$chapter_id = $this->user->chapter_id;
					
					// Set all to 0 first
					$participants_mask = $db->query("UPDATE participants SET passed_selection_three=0 WHERE applicant_id IN (SELECT id FROM applicants WHERE chapter_id='$chapter_id')");
					
					// Set relevant ones to 1
					$participants_update = $db->query("UPDATE participants SET passed_selection_three=1 WHERE id IN $participant_ids_string");
					
					$db->commit();

					$db->autocommit($prev_autocommit);
				}
				catch (HeliumException $e) {
					$db->rollback();

					$e->output();
					$error = 'db_fail'; // Database failure. Allow user to try again.
				}

				if (!$error) {
					$this->http_redirect(array('controller' => 'selection_three_announcement', 'action' => 'index', 'success' => $batch_id, 'new' => 1));
				}
			}
		}
		// GET
		

		$form = new FormDisplay;

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
	
	public function get_batch_count() {
		return 0;
	}
}
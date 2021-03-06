<?php

/**
 * RegistrationCodeController
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package chapter
 */
class RegistrationCodeController extends AppController {
	public function index() {
		$this->require_role('chapter_admin');

		$chapter = $this->request->get_relevant_chapter(true);
		
		if (!$chapter)
			$error = 'forbidden';

		if (!$error) {
			$batches = $chapter->registration_code_batches;
			$batches->set_batch_length(0);
			$this['chapter'] = $chapter;
			$this['batches'] = $batches;
		}
	}
	
	/**
	 * View, as a webpage, a batch of registration codes.
	 */
	public function view() {
		$this->require_role('chapter_admin');
		$chapter_id = $this->params['chapter_id'];
		$batch_id = $this->params['batch_id'];
		$expires_on = $this->params['expires_on'];
		
		$user = $this->session->user;
		
		if (!isset($batch_id)) {
			$error = 'incomplete_request';
		}
		
		if (!$error) {
			$batch = RegistrationCodeBatch::find($batch_id);
			if (!$batch)
				$error = 'not_found';
			else
				$chapter = $batch->chapter;
		}

		// TODO: Revise
		if (!$error && !$this->user->has_access_to($batch)) {
			$error = 'forbidden';
		}

		if (!$error) {
			$codes = $batch->registration_codes;
			$count_all = $codes->count_all();
			if (!$count_all)
				$error = 'no_codes_found';
		}

		if (!$error) {
			$codes->set_batch_length($count_all);
			$this['codes'] = $codes;
			$this['chapter_name'] = $chapter->chapter_name;
			$exp = new HeliumDateTime($batch->expires_on);
			$exp->setTimezone($chapter->chapter_timezone);
			$this['expires_on'] = $exp;
			$this['timezone'] = __($chapter->chapter_timezone);
			
			$this['format'] = $this->params['format'];
		}
		else {
			$this['error'] = $error;
		}
	}

	/**
	 * Output as a printable PDF a batch of registration codes.
	 */
	public function view_pdf() {}

	public function issue() {
		$this->require_role('chadmin');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($this->session->user->capable_of('admin')) {
				$chapter_id = (int) $_POST['chapter_id'];
				$is_national_admin = true;
			}
			else {
				$chapter_id = $this->session->user->chapter_id;
			}

			$db = Helium::db();

			$q = $db->prepare("SELECT chapter_timezone FROM chapters WHERE id='%d'", $chapter_id);
			$timezone = $db->get_var($q);

			$q = $db->prepare("SELECT chapter_timezone FROM chapters WHERE id='%d'", $chapter_id);
			$timezone = $db->get_var($q);

			if (!$timezone)
				$error = 'not_found';

			if (!$error) {
				$e = $_POST['expires_on'];
				$datestring = "{$e[year]}-{$e[month]}-{$e[day]} 23:59:59";
				$expires_on = new HeliumDateTime($datestring, $timezone);
				
				// Latest expiry is set in config.php
				// Ignore this for national admin
				$max_expiry = new HeliumDateTime(Helium::conf('registration_deadline'), $timezone);
				if ($expires_on->later_than($max_expiry) && !$is_national_admin) {
					$expiry_flag = true;
					$expires_on = $max_expiry;
				}

				$expires_on->setTimezone(Helium::conf('site_timezone'));

				$number_of_codes = (int) $_POST['number_of_codes'];

				$codes = array();
				for ($i = 1; $i <= $number_of_codes; $i++) {
					$codes[] = RegistrationCode::generate_token();
				}
				
				// Program year
				// TODO: Override by setting (not Helium::conf)
				$program_year = Helium::conf('program_year');
				
				// Create a new batch
				$batch = new RegistrationCodeBatch;
				$batch->chapter_id = $chapter_id;
				$batch->generated_by = $this->session->user->id;
				$batch->expires_on = $expires_on;
				$batch->code_count = $number_of_codes;
				$batch->program_year = $program_year;
				$batch->save();
				
				$batch_id = $batch->id;

				$sql = 'INSERT INTO registration_codes (token, chapter_id, expires_on, availability, program_year, registration_code_batch_id) VALUES ';
				$expires_on = (string) $expires_on;
				foreach ($codes as $i => $code) {
					$sql .= " ('$code', '$chapter_id', '$expires_on', 1, '$program_year', '$batch_id')";
					if ($i < (count($codes) - 1))
						$sql .= ',';
				}

				// it should be fairly safe to assume that there are no conflicts
				$db->query($sql);
			}

			if (!$error) {
				$action = 'view';
				$controller = 'registration_code';
				$expires_on = (string) $expires_on;
				$this->http_redirect(compact('controller', 'action', 'batch_id'));
			}
		}

		$exp = new HeliumDateTime('now');
		$exp->modify('+14 days');
		$expires_on = array('year' => $exp->format('Y'), 'month' => $exp->format('m'), 'day' => $exp->format('d'));

		$form = new FormDisplay;
		$form->feed(compact('expires_on'));

		$this['form'] = $form;

		$this['timezone'] = __($this->session->user->chapter->chapter_timezone);
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

	public function recover() {
		$pin = $this->params['pin'];
		$db = Helium::db();
		if (strlen($pin) != 16)
			$error = 'invalid_pin';
		else {
			$query = $db->prepare("SELECT username, users.id user_id, applicant_id, token, registration_codes.expires_on expires_on FROM registration_codes LEFT OUTER JOIN applicants ON applicants.id=registration_codes.applicant_id LEFT OUTER JOIN users ON applicants.user_id=users.id WHERE token='%s'", $pin);
			$row = $db->get_row($query);
			$this['username'] = $row->username;
			$this['user_id'] = $row->user_id;
			$this['applicant_id'] = $row->applicant_id;
			$this['token'] = $row->token;
			$this['expires_on'] = new HeliumDateTime($row->expires_on);
			$this['pin'] = $pin;
		}
	}
}
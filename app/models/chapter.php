<?php

/**
 * Chapter
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package chapter
 */
class Chapter extends HeliumRecord {
	public $id;
	public $chapter_name;
	public $chapter_code;
	public $chapter_timezone;
	
	public function init() {
		// $this->add_vertical_partition('chapter_info');
		
		$this->auto_serialize('depots');
	}

	public function rebuild() {
		if ($this->is_national_office()) {
			$this->applicants = Applicant::find('all');
			$this->registration_codes = RegistrationCode::find('all');
			$this->registration_code_batches = RegistrationCodeBatch::find('all');
			
			$this->chapters = Chapter::find('id != ' . $this->id);
		}
		else {
			$this->has_many('applicants');
			$this->has_many('registration_codes');
			$this->has_many('registration_code_batches');
		}

		$this->has_many('users');
	}

	public function before_save() {
		$this->chapter_code = strtoupper($this->chapter_code);
	}

	public function is_national_office() {
		return ($this->id == 1);
	}

	public function get_applicant_count() {
		return Applicant::find(array('chapter_id' => $this->id))->count_all();
	}

	public function get_user_count() {
		return User::find(array('chapter_id' => $this->id))->count_all();
	}
	
	public function get_registration_code_count() {
		return RegistrationCode::find(array('chapter_id' => $this->id))->count_all();
	}
	
	public function get_inline_address() {
		return str_replace(array("\r", "\n"), ', ', $this->chapter_address);
	}
	
	public function get_mappable_address() {
		return $this->get_inline_address() . ', Indonesia';
	}
	
	public function get_title() {
		return $this->is_national_office() ? $this->chapter_name : 'Chapter ' . $this->chapter_name;
	}
	
	public function get_email() {
		return 'chapter' . strtolower(str_replace(' ', '', $this->chapter_name)) . '@bina-antarbudaya.info';
	}
	
	public function parse_depots_yaml() {
		$depots = @Spyc::YAMLLoad($this->depots_yaml);
		if (!is_array($depots[0]))
			$depots = array($depots);
		
		foreach ($depots as $depot) {
			if (is_array($depot))
			foreach ($depot as $k => $v) {
				$nk = strtolower($k);
				if ($k != $nk) {
					unset($depot[$k]);
					$depot[$nk] = $v;
				}
			}
		}
		
		$this->depots = $depots;
	}
	
	public function get_participants() {
		$participants = Participant::find();
		$participants->include_association('applicant');
		$participants->narrow(array('applicants.chapter_id' => $this->id));
	}

	public static function ensure_applicants_migrated($chapter_id) {
		$db = Helium::db();
		$chapter_id = intval($chapter_id);
		if (!$chapter_id) {
			throw new HeliumException('No chapter_id provided in Chapter::ensure_applicants_migrated.');
		}

		$check_query = "SELECT COUNT(*) FROM participants WHERE applicant_id IN (" .
					   "SELECT id FROM applicants WHERE chapter_id=$chapter_id )";

		if (intval($db->get_var($check_query)) > 0) {
			return;
		}

		$query = "INSERT INTO participants (applicant_id) " .
				 "SELECT id FROM applicants WHERE applicants.finalized=1 AND applicants.chapter_id=$chapter_id";

		$try = $db->query($query);
	}
}


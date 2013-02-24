<?php

/**
 * Applicant
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package applicant
 */
class ApplicantCore extends Applicant {
	/**
	 * Record properties
	 */
	public $id;
	public $user_id;
	public $chapter_id;
	public $local_id;
	public $test_id;
	public $program_year = 2014;
	public $confirmed;
	public $finalized = false;
	public $expires_on;
	public $sanitized_full_name;
	public $sanitized_high_school_name;
	public $place_of_birth;
	public $date_of_birth;
	
	/**
	 * An array of tables of the applicant's complete data
	 */
	public static $satellite_tables = array('applicant_activities', 'applicant_contact_info', 'applicant_education', 'applicant_fathers', 'applicant_guardians', 'applicant_high_schools', 'applicant_mothers', 'applicant_family', 'applicant_personal_details', 'applicant_personality', 'applicant_primary_school_grade_history', 'applicant_program_choices', 'applicant_recommendations', 'applicant_referral', 'applicant_secondary_school_grade_history', 'applicant_selection_progress', 'applicant_travel_history');
	
	/**
	 * Validation properties
	 */
	public $validation_errors = array();
	public $incomplete_fields = array();
	
	/**
	 * Default values
	 */
	public function defaults() {
		$this->expires_on = new HeliumDateTime;
		$this->confirmed = false;
		$this->finalized = false;
	}

	/**
	 * Associations and partitions
	 */
	public function init() {
		$this->belongs_to('user');
		$this->belongs_to('chapter');

		$this->has_one('picture');
		$this->has_one('participant');

		$this->has_many('applicant_siblings', array('foreign_key' => 'applicant_id'));
		$this->has_many('applicant_organizations', array('foreign_key' => 'applicant_id'));
		$this->has_many('applicant_sports_achievements', array('foreign_key' => 'applicant_id'));
		$this->has_many('applicant_arts_achievements', array('foreign_key' => 'applicant_id'));
		$this->has_many('applicant_work_experiences', array('foreign_key' => 'applicant_id'));
		$this->has_many('applicant_other_achievements', array('foreign_key' => 'applicant_id'));
	}

	/**
	 * Find by user
	 */
	public function find_by_user($user_id) {
		if (is_object($user_id))
			$user_id = $user_id->id;

		$try = Applicant::find(compact('user_id'));

		return $try->first();
	}
	
	/**
	 * Find by test ID
	 */
	public function find_by_test_id($test_id) {
		$test_id = strtoupper($test_id);
		$try = Applicant::find(compact('test_id'));

		return $try->first();
	}

	/**
	 * Validate details
	 *
	 * Make sure all the required fields are filled in.
	 */
	public function validate() {
		$check = $errors = array();

		$applicant_id = $this->id;
		$d = $this->applicant_detail;

		$required = array('first_name', 'place_of_birth', 'applicant_email', 'applicant_address_street', 'sex', 'body_height', 'body_weight', 'blood_type', 'citizenship', 'religion', 'father_full_name', 'mother_full_name', 'number_of_children_in_family', 'nth_child', 'high_school_name', 'high_school_admission_year', 'high_school_graduation_year', 'junior_high_school_name', 'junior_high_school_graduation_year', 'elementary_school_name', 'elementary_school_graduation_year', 'years_speaking_english', 'favorite_subject', 'dream', 'arts_hobby', 'sports_hobby', 'motivation', 'hopes', 'recommendations_school_name', 'recommendations_school_address', 'recommendations_school_occupation', 'recommendations_school_work_address', 'recommendations_school_relationship', 'recommendations_nonschool_name', 'recommendations_nonschool_address', 'recommendations_nonschool_occupation', /* 'recommendations_nonschool_work_address', */ 'recommendations_nonschool_relationship', 'recommendations_close_friend_name', 'recommendations_close_friend_address', 'recommendations_close_friend_relationship', 'personality', 'strengths_and_weaknesses', 'stressful_conditions', 'biggest_life_problem', 'plans');
		
		for ($i = 1; $i <= 8; $i++) {
			if ($i != 6 && $i != 9) {
				// Allow acceleration class in primary and secondary schools
				$required[] = "grades_y{$i}t1_average";
				$required[] = "grades_y{$i}t1_subjects";
				$required[] = "grades_y{$i}t2_average";
				$required[] = "grades_y{$i}t2_subjects";
			}
		}

		$required[] = "grades_y10t1_average";
		$required[] = "grades_y10t1_subjects";

		foreach ($required as $f) {
			$try = trim($this->$f, "- \t\n\r\0\x0B");
			if (!$try) {
				$check['incomplete'] = false;
				$this->incomplete_fields[] = $f;
			}
		}

		$check['picture'] = (bool) $this->picture;

		list($a, $y, $j) = array($this->program_afs, $this->program_yes, $this->program_jenesys);
		if (!$a && !$y && !$j) {
			$check['program'] = false;
		}

		$bd = $this->date_of_birth;
		$lower = new HeliumDateTime(($this->program_year - 19) . '-08-01');
		$upper = new HeliumDateTime(($this->program_year - 17) . '-08-01');
		$check['birth_date'] = $bd >= $lower && $bd <= $upper;
		// $check['birth_date'] = $bd->later_than($lower) && $bd->earlier_than($upper);

		foreach ($check as $c => $v) {
			if (!$v)
				$errors[] = $c;
		}

		$this->validation_errors = $errors;
		
		if ($errors)
			return false;
		else
			return true;
	}

	public static function split_test_ids($ids_string) {
		$ids_string = strtoupper($ids_string);
		$test_ids = preg_split('/\s+/', $ids_string);
		
		return $test_ids;
	}
	
	/**
	 * Creates a row on all satellite tables prior to insertion of this record
	 */
	public function sync_satellite_tables() {
		$db = Helium::db();
		foreach (self::$satellite_tables as $table) {
			$cq = "SELECT applicant_id FROM $table WHERE applicant_id=$this->id";
			$check = $db->get_var($cq);
			if (!$check)
				$db->query("INSERT INTO $table (applicant_id) VALUES ($this->id)");
		}
	}
}
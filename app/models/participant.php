<?php

/**
 * Participant
 *
 * An applicant which has completed registration and is eligible for selections.
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package selection
 */
class Participant extends HeliumRecord {
	public $full_name;
	public $school;
	public $selection1_chamber_id;
	public $applicant_id;
	public $id;

	public function init() {
		$this->belongs_to('applicant');
		$this->belongs_to('selection_two_batch');
	}

	/**
	 * null: no results
	 */
	public function selection_results($selection_stage) {
		$map = array(1 => 'passed_selection_one', 2 => 'passed_selection_two', 3 => 'passed_selection_three', 4 => 'passed_national_selection');
		$var = $map[$selection_stage];

		if ($this->$var)
			return true;
		else {
			$is_null = Helium::db()->get_var("SELECT $var IS NULL FROM participants WHERE id='{$this->id}'");
			
			if ($is_null)
				return null;
			else
				return false;
		}
	}

	// not used
	public function passed_selection($selection_stage) {
		$map = array(1 => 'passed_selection_one', 2 => 'passed_selection_two', 3 => 'passed_selection_three', 4 => 'passed_national_selection');
		$var = $map[$selection_stage];
		return $this->$var;
	}

	// not used
	public function can_join_selection($selection_stage, &$last_selection_failed = 0) {
		$selection_stage = (int) $selection_stage;
		if ($selection_stage == 1) // selection 1 - everyone's eligible
			return true;

		for ($i = 1; $i < $selection_stage; $i++) {
			if (!$this->passed_selection($i))
				return false;
		}

		return true;
	}

	// not used
	public function get_first_selection_failed() {
		$last_selection = 4;
		for ($i = 1; $i <= $last_selection; $i++) {
			if (!$this->passed_selection($i))
				return $i;
		}
	}
}

?>
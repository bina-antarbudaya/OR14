<?php

/**
 * SelectionTwoAssignment
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package applicant
 */
class SelectionTwoBatch extends HeliumRecord {
	public $id;
	public $local_batch_number;
	public $chapter_id;
	public $announcement_date;
	public $announcement_date_follows_national = 1;
	public $reregistration_start_date;
	public $reregistration_finish_date;
	public $selection_date;
	public $selection_date_follows_national = 1;
	public $selection_venue;
	
	public function init() {
		$this->belongs_to('chapter');
	}
	
	public function get_announcement_date() {
		if ($this->announcement_date_follows_national)
			return self::get_national_announcement_date();
		else
			return $this->announcement_date;
	}
	
	public function get_selection_date() {
		if ($this->selection_date_follows_national)
			return self::get_national_selection_date();
		else
			return $this->selection_date;
	}
	
	public static function get_national_announcement_date() {
		return new HeliumDateTime(Helium::conf('selection_one_announcement_date'));
	}
	
	public static function get_national_selection_date() {
		return new HeliumDateTime(Helium::conf('selection_two_date'));
	}
	
	public function generate_local_batch_number() {
		$db = Helium::db();
		$query = $db->prepare("SELECT MAX(local_batch_number) FROM selection_two_batches WHERE chapter_id='%d' ORDER BY local_batch_number DESC", $this->chapter_id);
		$last = $db->get_var($query);

		return $last + 1;
	}
	
	public function before_save() {
		if ($this->chapter_id)
			$this->local_batch_number = $this->generate_local_batch_number();
	}
	
	public function get_personality_chamber_count() {
		return Helium::db()->get_var('SELECT COUNT(DISTINCT personality_chamber_number) FROM participants WHERE selection_two_batch_id=' . $this->id);
	}
	
	public function get_english_chamber_count() {
		return Helium::db()->get_var('SELECT COUNT(DISTINCT english_chamber_number) FROM participants WHERE selection_two_batch_id=' . $this->id);
	}
	
	public function get_participant_count() {
		return Helium::db()->get_var('SELECT COUNT(*) FROM participants WHERE selection_two_batch_id=' . $this->id);
	}
}


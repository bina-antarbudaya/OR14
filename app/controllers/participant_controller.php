<?php

class ParticipantController extends AppController {
	public function init() {
		$this->require_role('chapter_admin');
		$this->chapter = $this->user->chapter;
	}

	private function get_participants() {
	}

	/**
	 * Migrate all finalized applicants to the participants table
	 */
	public function migrate_applicants() {		
		$db = Helium::db();

		// Limit to unmigrated applicants only
		$this->require_role('national_admin');
		
		$query = "INSERT INTO participants (applicant_id) SELECT applicant_id FROM applicants WHERE finalized=1 AND applicant_id NOT IN (SELECT applicant_id FROM participants)";
	}

	/**
	 * Assign written chambers to participants
	 */
	private function do_assign_written_chambers(HeliumRecordCollection $participants) {
		// We'll do this sometime soon.
	}
	
	public function participant_list() {
		// too much data to handle using the nice way, let's get raw here.
		ini_set('memory_limit', '128M');

		$db = Helium::db();

		if ($this->session->user->capable_of('national_admin'))
			$chapter_id = (string) $this->params['chapter_id'];
		else
			$chapter_id = (string) $this->session->user->chapter_id;
			
		$chapter_id = $db->escape($chapter_id);
		if ($chapter_id === '1')
			$chapter_id = '';

		$query_all = "SELECT test_id, sanitized_full_name, sanitized_high_school_name, applicant_email, applicant_mobilephone, CONCAT(applicant_address_street, ', ', applicant_address_city, ', ', applicant_address_province) applicant_address, confirmed FROM applicants INNER JOIN applicant_contact_info ON applicants.id=applicant_contact_info.applicant_id WHERE finalized=1 ";
		if ($chapter_id)
			$query_all .= " AND chapter_id='$chapter_id'";
		$query_all .= " ORDER BY test_id";

		$applicants = $db->get_results($query_all);

		$this['participants'] = $applicants;
		$this['output_mode'] = 'xlsx';
	}
}
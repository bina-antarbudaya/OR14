<?php

/**
 * Applicant
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package applicant
 */
class Applicant extends HeliumPartitionedRecord {
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

		$this->has_many('applicant_siblings');
		$this->has_many('applicant_organizations');
		$this->has_many('applicant_sports_achievements');
		$this->has_many('applicant_arts_achievements');
		$this->has_many('applicant_work_experiences');
		$this->has_many('applicant_other_achievements');

		$this->add_vertical_partition('applicant_activities');
		$this->add_vertical_partition('applicant_contact_info');
		$this->add_vertical_partition('applicant_education');
		$this->add_vertical_partition('applicant_fathers');
		$this->add_vertical_partition('applicant_guardians');
		$this->add_vertical_partition('applicant_high_schools');
		$this->add_vertical_partition('applicant_mothers');
		$this->add_vertical_partition('applicant_family');
		$this->add_vertical_partition('applicant_personal_details');
		$this->add_vertical_partition('applicant_personality');
		$this->add_vertical_partition('applicant_primary_school_grade_history');
		$this->add_vertical_partition('applicant_program_choices');
		$this->add_vertical_partition('applicant_recommendations');
		$this->add_vertical_partition('applicant_referral');
		$this->add_vertical_partition('applicant_secondary_school_grade_history');
		$this->add_vertical_partition('applicant_selection_progress');
		$this->add_vertical_partition('applicant_travel_history');
	}

	/**
	 * Partitioned table magic
	 */
	public function _map_vertical_partitions() {
		$start = microtime(true);

		static $done;
		
		if (!$done) {
			/* Static mapping! */
			$this->_vertical_partition_table_map = array (
			  'applicant_activities' => 
			  array (
			    0 => 'arts_hobby',
			    1 => 'arts_organized',
			    2 => 'sports_hobby',
			    3 => 'sports_organized',
			  ),
			  'applicant_contact_info' => 
			  array (
			    0 => 'applicant_address_street',
			    1 => 'applicant_address_city',
			    2 => 'applicant_address_province',
			    3 => 'applicant_address_postcode',
			    4 => 'applicant_phone_areacode',
			    5 => 'applicant_phone_number',
			    6 => 'applicant_fax_areacode',
			    7 => 'applicant_fax_number',
			    8 => 'applicant_mobilephone',
			    9 => 'applicant_email',
			  ),
			  'applicant_education' => 
			  array (
			    0 => 'elementary_school_name',
			    1 => 'elementary_school_graduation_year',
			    2 => 'junior_high_school_name',
			    3 => 'junior_high_school_graduation_year',
			    4 => 'years_speaking_english',
			    5 => 'other_languages',
			    6 => 'years_speaking_other_languages',
			    7 => 'favorite_subject',
			    8 => 'dream',
			  ),
			  'applicant_fathers' => 
			  array (
			    0 => 'father_full_name',
			    1 => 'father_education',
			    2 => 'father_occupation',
			    3 => 'father_job_title',
			    4 => 'father_office_name',
			    5 => 'father_office_address_street',
			    6 => 'father_office_address_city',
			    7 => 'father_office_address_province',
			    8 => 'father_office_address_postcode',
			    9 => 'father_office_phone_areacode',
			    10 => 'father_office_phone_number',
			    11 => 'father_office_fax_areacode',
			    12 => 'father_office_fax_number',
			    13 => 'father_office_mobilephone',
			    14 => 'father_office_email',
			  ),
			  'applicant_guardians' => 
			  array (
			    0 => 'guardian_full_name',
			    1 => 'guardian_relationship_to_applicant',
			    2 => 'guardian_address_street',
			    3 => 'guardian_address_city',
			    4 => 'guardian_address_province',
			    5 => 'guardian_address_postcode',
			    6 => 'guardian_phone_areacode',
			    7 => 'guardian_phone_number',
			    8 => 'guardian_fax_areacode',
			    9 => 'guardian_fax_number',
			    10 => 'guardian_mobilephone',
			    11 => 'guardian_email',
			    12 => 'guardian_education',
			    13 => 'guardian_occupation',
			    14 => 'guardian_job_title',
			    15 => 'guardian_office_name',
			    16 => 'guardian_office_address_street',
			    17 => 'guardian_office_address_city',
			    18 => 'guardian_office_address_province',
			    19 => 'guardian_office_address_postcode',
			    20 => 'guardian_office_phone_areacode',
			    21 => 'guardian_office_phone_number',
			    22 => 'guardian_office_fax_areacode',
			    23 => 'guardian_office_fax_number',
			    24 => 'guardian_office_mobilephone',
			    25 => 'guardian_office_email',
			  ),
			  'applicant_high_schools' => 
			  array (
			    0 => 'high_school_name',
			    1 => 'high_school_graduation_month',
			    2 => 'high_school_graduation_year',
			    3 => 'high_school_admission_year',
			    4 => 'high_school_headmaster_name',
			    5 => 'high_school_address_street',
			    6 => 'high_school_address_city',
			    7 => 'high_school_address_province',
			    8 => 'high_school_address_postcode',
			    9 => 'high_school_phone_areacode',
			    10 => 'high_school_phone_number',
			    11 => 'high_school_fax_areacode',
			    12 => 'high_school_fax_number',
			    13 => 'high_school_mobilephone',
			    14 => 'high_school_email',
			  ),
			  'applicant_mothers' => 
			  array (
			    0 => 'mother_full_name',
			    1 => 'mother_education',
			    2 => 'mother_occupation',
			    3 => 'mother_job_title',
			    4 => 'mother_office_name',
			    5 => 'mother_office_address_street',
			    6 => 'mother_office_address_city',
			    7 => 'mother_office_address_province',
			    8 => 'mother_office_address_postcode',
			    9 => 'mother_office_phone_areacode',
			    10 => 'mother_office_phone_number',
			    11 => 'mother_office_fax_areacode',
			    12 => 'mother_office_fax_number',
			    13 => 'mother_office_mobilephone',
			    14 => 'mother_office_email',
			  ),
			  'applicant_family' => 
			  array (
			    0 => 'number_of_children_in_family',
			    1 => 'nth_child',
			  ),
			  'applicant_personal_details' => 
			  array (
			    0 => 'first_name',
			    1 => 'middle_name',
			    2 => 'last_name',
			    3 => 'sex',
			    4 => 'body_height',
			    5 => 'body_weight',
			    6 => 'blood_type',
			    7 => 'citizenship',
			    8 => 'religion',
			  ),
			  'applicant_personality' => 
			  array (
			    0 => 'motivation',
			    1 => 'hopes',
			    2 => 'personality',
			    3 => 'strengths_and_weaknesses',
			    4 => 'stressful_conditions',
			    5 => 'biggest_life_problem',
			    6 => 'plans',
			  ),
			  'applicant_primary_school_grade_history' => 
			  array (
			    0 => 'grades_y1t1_average',
			    1 => 'grades_y1t1_subjects',
			    2 => 'grades_y1t2_average',
			    3 => 'grades_y1t2_subjects',
			    4 => 'grades_y2t1_average',
			    5 => 'grades_y2t1_subjects',
			    6 => 'grades_y2t2_average',
			    7 => 'grades_y2t2_subjects',
			    8 => 'grades_y3t1_average',
			    9 => 'grades_y3t1_subjects',
			    10 => 'grades_y3t2_average',
			    11 => 'grades_y3t2_subjects',
			    12 => 'grades_y4t1_average',
			    13 => 'grades_y4t1_subjects',
			    14 => 'grades_y4t2_average',
			    15 => 'grades_y4t2_subjects',
			    16 => 'grades_y5t1_average',
			    17 => 'grades_y5t1_subjects',
			    18 => 'grades_y5t2_average',
			    19 => 'grades_y5t2_subjects',
			    20 => 'grades_y6t1_average',
			    21 => 'grades_y6t1_subjects',
			    22 => 'grades_y6t2_average',
			    23 => 'grades_y6t2_subjects',
			  ),
			  'applicant_program_choices' => 
			  array (
			    0 => 'program_afs',
			    1 => 'program_yes',
			    2 => 'program_jenesys_year',
			    3 => 'program_jenesys_short',
			    4 => 'in_acceleration_class',
			    5 => 'in_pesantren',
			    6 => 'country_preference_1',
			    7 => 'country_preference_2',
			    8 => 'country_preference_3',
			    9 => 'country_preference_4',
			    10 => 'country_preference_5',
			    11 => 'country_preference_6',
			    12 => 'country_preference_7',
			    13 => 'country_preference_8',
			    14 => 'country_preference_9',
			    15 => 'country_preference_10',
			    16 => 'country_preference_other',
			    17 => 'pref_europe_1',
			    18 => 'pref_europe_2',
			    19 => 'pref_europe_3',
			    20 => 'pref_europe_4',
			    21 => 'pref_europe_5',
			    22 => 'pref_europe_6',
			    23 => 'pref_europe_7',
			    24 => 'pref_europe_8',
			    25 => 'pref_europe_9',
			    26 => 'pref_europe_10',
			    27 => 'pref_americas_1',
			    28 => 'pref_americas_2',
			    29 => 'pref_americas_3',
			    30 => 'pref_americas_4',
			    31 => 'pref_americas_5',
			    32 => 'pref_americas_6',
			    33 => 'pref_asia_1',
			    34 => 'pref_asia_2',
			    35 => 'pref_asia_3',
			    36 => 'pref_asia_4',
			    37 => 'pref_asia_5',
			    38 => 'pref_asia_6',
			  ),
			  'applicant_recommendations' => 
			  array (
			    0 => 'recommendations_school_name',
			    1 => 'recommendations_school_address',
			    2 => 'recommendations_school_occupation',
			    3 => 'recommendations_school_work_address',
			    4 => 'recommendations_school_relationship',
			    5 => 'recommendations_nonschool_name',
			    6 => 'recommendations_nonschool_address',
			    7 => 'recommendations_nonschool_occupation',
			    8 => 'recommendations_nonschool_work_address',
			    9 => 'recommendations_nonschool_relationship',
			    10 => 'recommendations_close_friend_name',
			    11 => 'recommendations_close_friend_address',
			    12 => 'recommendations_close_friend_relationship',
			  ),
			  'applicant_referral' => 
			  array (
			    0 => 'relative_returnee_name',
			    1 => 'relative_returnee_relationship',
			    2 => 'relative_returnee_program',
			    3 => 'relative_returnee_program_type',
			    4 => 'relative_returnee_destination',
			    5 => 'relative_returnee_address_street',
			    6 => 'relative_returnee_address_city',
			    7 => 'relative_returnee_address_province',
			    8 => 'relative_returnee_address_postcode',
			    9 => 'relative_returnee_homephone_areacode',
			    10 => 'relative_returnee_homephone_number',
			    11 => 'relative_returnee_fax_areacode',
			    12 => 'relative_returnee_fax_number',
			    13 => 'relative_returnee_mobilephone',
			    14 => 'relative_returnee_email',
			    15 => 'past_binabud_activities',
			    16 => 'past_binabud_activities_year',
			    17 => 'referrer',
			    18 => 'relative_returnee_exists',
			    19 => 'past_binabud_has',
			    20 => 'past_binabud_activities_who',
			    21 => 'past_binabud_activities_relationship',
			  ),
			  'applicant_secondary_school_grade_history' => 
			  array (
			    0 => 'grades_y7t1_average',
			    1 => 'grades_y7t1_subjects',
			    2 => 'grades_y7t2_average',
			    3 => 'grades_y7t2_subjects',
			    4 => 'grades_y8t1_average',
			    5 => 'grades_y8t1_subjects',
			    6 => 'grades_y8t2_average',
			    7 => 'grades_y8t2_subjects',
			    8 => 'grades_y9t1_average',
			    9 => 'grades_y9t1_subjects',
			    10 => 'grades_y9t2_average',
			    11 => 'grades_y9t2_subjects',
			    12 => 'grades_y10t1_average',
			    13 => 'grades_y10t1_subjects',
			  ),
			  'applicant_selection_progress' => 
			  array (
			    0 => 'selection_1_passed',
			    1 => 'selection_2_passed',
			    2 => 'selection_3_passed',
			  ),
			  'applicant_travel_history' => 
			  array (
			    0 => 'short_term_travel_destination',
			    1 => 'short_term_travel_when',
			    2 => 'short_term_travel_purpose',
			    3 => 'long_term_travel_destination',
			    4 => 'long_term_travel_when',
			    5 => 'long_term_travel_purpose',
			    6 => 'long_term_travel_activities',
			    7 => 'short_term_travel_has',
			    8 => 'long_term_travel_has',
			  ),
			);

			$this->_vertical_partition_column_map = array (
			  'arts_hobby' => 'applicant_activities',
			  'arts_organized' => 'applicant_activities',
			  'sports_hobby' => 'applicant_activities',
			  'sports_organized' => 'applicant_activities',
			  'applicant_address_street' => 'applicant_contact_info',
			  'applicant_address_city' => 'applicant_contact_info',
			  'applicant_address_province' => 'applicant_contact_info',
			  'applicant_address_postcode' => 'applicant_contact_info',
			  'applicant_phone_areacode' => 'applicant_contact_info',
			  'applicant_phone_number' => 'applicant_contact_info',
			  'applicant_fax_areacode' => 'applicant_contact_info',
			  'applicant_fax_number' => 'applicant_contact_info',
			  'applicant_mobilephone' => 'applicant_contact_info',
			  'applicant_email' => 'applicant_contact_info',
			  'elementary_school_name' => 'applicant_education',
			  'elementary_school_graduation_year' => 'applicant_education',
			  'junior_high_school_name' => 'applicant_education',
			  'junior_high_school_graduation_year' => 'applicant_education',
			  'years_speaking_english' => 'applicant_education',
			  'other_languages' => 'applicant_education',
			  'years_speaking_other_languages' => 'applicant_education',
			  'favorite_subject' => 'applicant_education',
			  'dream' => 'applicant_education',
			  'father_full_name' => 'applicant_fathers',
			  'father_education' => 'applicant_fathers',
			  'father_occupation' => 'applicant_fathers',
			  'father_job_title' => 'applicant_fathers',
			  'father_office_name' => 'applicant_fathers',
			  'father_office_address_street' => 'applicant_fathers',
			  'father_office_address_city' => 'applicant_fathers',
			  'father_office_address_province' => 'applicant_fathers',
			  'father_office_address_postcode' => 'applicant_fathers',
			  'father_office_phone_areacode' => 'applicant_fathers',
			  'father_office_phone_number' => 'applicant_fathers',
			  'father_office_fax_areacode' => 'applicant_fathers',
			  'father_office_fax_number' => 'applicant_fathers',
			  'father_office_mobilephone' => 'applicant_fathers',
			  'father_office_email' => 'applicant_fathers',
			  'guardian_full_name' => 'applicant_guardians',
			  'guardian_relationship_to_applicant' => 'applicant_guardians',
			  'guardian_address_street' => 'applicant_guardians',
			  'guardian_address_city' => 'applicant_guardians',
			  'guardian_address_province' => 'applicant_guardians',
			  'guardian_address_postcode' => 'applicant_guardians',
			  'guardian_phone_areacode' => 'applicant_guardians',
			  'guardian_phone_number' => 'applicant_guardians',
			  'guardian_fax_areacode' => 'applicant_guardians',
			  'guardian_fax_number' => 'applicant_guardians',
			  'guardian_mobilephone' => 'applicant_guardians',
			  'guardian_email' => 'applicant_guardians',
			  'guardian_education' => 'applicant_guardians',
			  'guardian_occupation' => 'applicant_guardians',
			  'guardian_job_title' => 'applicant_guardians',
			  'guardian_office_name' => 'applicant_guardians',
			  'guardian_office_address_street' => 'applicant_guardians',
			  'guardian_office_address_city' => 'applicant_guardians',
			  'guardian_office_address_province' => 'applicant_guardians',
			  'guardian_office_address_postcode' => 'applicant_guardians',
			  'guardian_office_phone_areacode' => 'applicant_guardians',
			  'guardian_office_phone_number' => 'applicant_guardians',
			  'guardian_office_fax_areacode' => 'applicant_guardians',
			  'guardian_office_fax_number' => 'applicant_guardians',
			  'guardian_office_mobilephone' => 'applicant_guardians',
			  'guardian_office_email' => 'applicant_guardians',
			  'high_school_name' => 'applicant_high_schools',
			  'high_school_graduation_month' => 'applicant_high_schools',
			  'high_school_graduation_year' => 'applicant_high_schools',
			  'high_school_admission_year' => 'applicant_high_schools',
			  'high_school_headmaster_name' => 'applicant_high_schools',
			  'high_school_address_street' => 'applicant_high_schools',
			  'high_school_address_city' => 'applicant_high_schools',
			  'high_school_address_province' => 'applicant_high_schools',
			  'high_school_address_postcode' => 'applicant_high_schools',
			  'high_school_phone_areacode' => 'applicant_high_schools',
			  'high_school_phone_number' => 'applicant_high_schools',
			  'high_school_fax_areacode' => 'applicant_high_schools',
			  'high_school_fax_number' => 'applicant_high_schools',
			  'high_school_mobilephone' => 'applicant_high_schools',
			  'high_school_email' => 'applicant_high_schools',
			  'mother_full_name' => 'applicant_mothers',
			  'mother_education' => 'applicant_mothers',
			  'mother_occupation' => 'applicant_mothers',
			  'mother_job_title' => 'applicant_mothers',
			  'mother_office_name' => 'applicant_mothers',
			  'mother_office_address_street' => 'applicant_mothers',
			  'mother_office_address_city' => 'applicant_mothers',
			  'mother_office_address_province' => 'applicant_mothers',
			  'mother_office_address_postcode' => 'applicant_mothers',
			  'mother_office_phone_areacode' => 'applicant_mothers',
			  'mother_office_phone_number' => 'applicant_mothers',
			  'mother_office_fax_areacode' => 'applicant_mothers',
			  'mother_office_fax_number' => 'applicant_mothers',
			  'mother_office_mobilephone' => 'applicant_mothers',
			  'mother_office_email' => 'applicant_mothers',
			  'number_of_children_in_family' => 'applicant_family',
			  'nth_child' => 'applicant_family',
			  'first_name' => 'applicant_personal_details',
			  'middle_name' => 'applicant_personal_details',
			  'last_name' => 'applicant_personal_details',
			  'sex' => 'applicant_personal_details',
			  'body_height' => 'applicant_personal_details',
			  'body_weight' => 'applicant_personal_details',
			  'blood_type' => 'applicant_personal_details',
			  'citizenship' => 'applicant_personal_details',
			  'religion' => 'applicant_personal_details',
			  'motivation' => 'applicant_personality',
			  'hopes' => 'applicant_personality',
			  'personality' => 'applicant_personality',
			  'strengths_and_weaknesses' => 'applicant_personality',
			  'stressful_conditions' => 'applicant_personality',
			  'biggest_life_problem' => 'applicant_personality',
			  'plans' => 'applicant_personality',
			  'grades_y1t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y1t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y1t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y1t2_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y2t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y2t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y2t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y2t2_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y3t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y3t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y3t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y3t2_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y4t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y4t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y4t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y4t2_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y5t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y5t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y5t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y5t2_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y6t1_average' => 'applicant_primary_school_grade_history',
			  'grades_y6t1_subjects' => 'applicant_primary_school_grade_history',
			  'grades_y6t2_average' => 'applicant_primary_school_grade_history',
			  'grades_y6t2_subjects' => 'applicant_primary_school_grade_history',
			  'program_afs' => 'applicant_program_choices',
			  'program_yes' => 'applicant_program_choices',
			  'program_jenesys_year' => 'applicant_program_choices',
			  'program_jenesys_short' => 'applicant_program_choices',
			  'in_acceleration_class' => 'applicant_program_choices',
			  'in_pesantren' => 'applicant_program_choices',
			  'country_preference_1' => 'applicant_program_choices',
			  'country_preference_2' => 'applicant_program_choices',
			  'country_preference_3' => 'applicant_program_choices',
			  'country_preference_4' => 'applicant_program_choices',
			  'country_preference_5' => 'applicant_program_choices',
			  'country_preference_6' => 'applicant_program_choices',
			  'country_preference_7' => 'applicant_program_choices',
			  'country_preference_8' => 'applicant_program_choices',
			  'country_preference_9' => 'applicant_program_choices',
			  'country_preference_10' => 'applicant_program_choices',
			  'country_preference_other' => 'applicant_program_choices',
			  'pref_europe_1' => 'applicant_program_choices',
			  'pref_europe_2' => 'applicant_program_choices',
			  'pref_europe_3' => 'applicant_program_choices',
			  'pref_europe_4' => 'applicant_program_choices',
			  'pref_europe_5' => 'applicant_program_choices',
			  'pref_europe_6' => 'applicant_program_choices',
			  'pref_europe_7' => 'applicant_program_choices',
			  'pref_europe_8' => 'applicant_program_choices',
			  'pref_europe_9' => 'applicant_program_choices',
			  'pref_europe_10' => 'applicant_program_choices',
			  'pref_americas_1' => 'applicant_program_choices',
			  'pref_americas_2' => 'applicant_program_choices',
			  'pref_americas_3' => 'applicant_program_choices',
			  'pref_americas_4' => 'applicant_program_choices',
			  'pref_americas_5' => 'applicant_program_choices',
			  'pref_americas_6' => 'applicant_program_choices',
			  'pref_asia_1' => 'applicant_program_choices',
			  'pref_asia_2' => 'applicant_program_choices',
			  'pref_asia_3' => 'applicant_program_choices',
			  'pref_asia_4' => 'applicant_program_choices',
			  'pref_asia_5' => 'applicant_program_choices',
			  'pref_asia_6' => 'applicant_program_choices',
			  'recommendations_school_name' => 'applicant_recommendations',
			  'recommendations_school_address' => 'applicant_recommendations',
			  'recommendations_school_occupation' => 'applicant_recommendations',
			  'recommendations_school_work_address' => 'applicant_recommendations',
			  'recommendations_school_relationship' => 'applicant_recommendations',
			  'recommendations_nonschool_name' => 'applicant_recommendations',
			  'recommendations_nonschool_address' => 'applicant_recommendations',
			  'recommendations_nonschool_occupation' => 'applicant_recommendations',
			  'recommendations_nonschool_work_address' => 'applicant_recommendations',
			  'recommendations_nonschool_relationship' => 'applicant_recommendations',
			  'recommendations_close_friend_name' => 'applicant_recommendations',
			  'recommendations_close_friend_address' => 'applicant_recommendations',
			  'recommendations_close_friend_relationship' => 'applicant_recommendations',
			  'relative_returnee_name' => 'applicant_referral',
			  'relative_returnee_relationship' => 'applicant_referral',
			  'relative_returnee_program' => 'applicant_referral',
			  'relative_returnee_program_type' => 'applicant_referral',
			  'relative_returnee_destination' => 'applicant_referral',
			  'relative_returnee_address_street' => 'applicant_referral',
			  'relative_returnee_address_city' => 'applicant_referral',
			  'relative_returnee_address_province' => 'applicant_referral',
			  'relative_returnee_address_postcode' => 'applicant_referral',
			  'relative_returnee_homephone_areacode' => 'applicant_referral',
			  'relative_returnee_homephone_number' => 'applicant_referral',
			  'relative_returnee_fax_areacode' => 'applicant_referral',
			  'relative_returnee_fax_number' => 'applicant_referral',
			  'relative_returnee_mobilephone' => 'applicant_referral',
			  'relative_returnee_email' => 'applicant_referral',
			  'past_binabud_activities' => 'applicant_referral',
			  'past_binabud_activities_year' => 'applicant_referral',
			  'referrer' => 'applicant_referral',
			  'relative_returnee_exists' => 'applicant_referral',
			  'past_binabud_has' => 'applicant_referral',
			  'past_binabud_activities_who' => 'applicant_referral',
			  'past_binabud_activities_relationship' => 'applicant_referral',
			  'grades_y7t1_average' => 'applicant_secondary_school_grade_history',
			  'grades_y7t1_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y7t2_average' => 'applicant_secondary_school_grade_history',
			  'grades_y7t2_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y8t1_average' => 'applicant_secondary_school_grade_history',
			  'grades_y8t1_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y8t2_average' => 'applicant_secondary_school_grade_history',
			  'grades_y8t2_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y9t1_average' => 'applicant_secondary_school_grade_history',
			  'grades_y9t1_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y9t2_average' => 'applicant_secondary_school_grade_history',
			  'grades_y9t2_subjects' => 'applicant_secondary_school_grade_history',
			  'grades_y10t1_average' => 'applicant_secondary_school_grade_history',
			  'grades_y10t1_subjects' => 'applicant_secondary_school_grade_history',
			  'selection_1_passed' => 'applicant_selection_progress',
			  'selection_2_passed' => 'applicant_selection_progress',
			  'selection_3_passed' => 'applicant_selection_progress',
			  'short_term_travel_destination' => 'applicant_travel_history',
			  'short_term_travel_when' => 'applicant_travel_history',
			  'short_term_travel_purpose' => 'applicant_travel_history',
			  'long_term_travel_destination' => 'applicant_travel_history',
			  'long_term_travel_when' => 'applicant_travel_history',
			  'long_term_travel_purpose' => 'applicant_travel_history',
			  'long_term_travel_activities' => 'applicant_travel_history',
			  'short_term_travel_has' => 'applicant_travel_history',
			  'long_term_travel_has' => 'applicant_travel_history',
			);


			$this->_vertical_partition_column_types = array (
			  'user_id' => 'int',
			  'chapter_id' => 'int',
			  'local_id' => 'int',
			  'test_id' => 'string',
			  'program_year' => 'int',
			  'expires_on' => 'datetime',
			  'sanitized_full_name' => 'string',
			  'sanitized_high_school_name' => 'string',
			  'place_of_birth' => 'string',
			  'date_of_birth' => 'datetime',
			  'finalized' => 'int',
			  'confirmed' => 'int',
			  'arts_hobby' => 'string',
			  'arts_organized' => 'string',
			  'sports_hobby' => 'string',
			  'sports_organized' => 'string',
			  'applicant_address_street' => 'string',
			  'applicant_address_city' => 'string',
			  'applicant_address_province' => 'string',
			  'applicant_address_postcode' => 'string',
			  'applicant_phone_areacode' => 'string',
			  'applicant_phone_number' => 'string',
			  'applicant_fax_areacode' => 'string',
			  'applicant_fax_number' => 'string',
			  'applicant_mobilephone' => 'string',
			  'applicant_email' => 'string',
			  'elementary_school_name' => 'string',
			  'elementary_school_graduation_year' => 'int',
			  'junior_high_school_name' => 'string',
			  'junior_high_school_graduation_year' => 'int',
			  'years_speaking_english' => 'string',
			  'other_languages' => 'string',
			  'years_speaking_other_languages' => 'string',
			  'favorite_subject' => 'string',
			  'dream' => 'string',
			  'father_full_name' => 'string',
			  'father_education' => 'string',
			  'father_occupation' => 'string',
			  'father_job_title' => 'string',
			  'father_office_name' => 'string',
			  'father_office_address_street' => 'string',
			  'father_office_address_city' => 'string',
			  'father_office_address_province' => 'string',
			  'father_office_address_postcode' => 'string',
			  'father_office_phone_areacode' => 'string',
			  'father_office_phone_number' => 'string',
			  'father_office_fax_areacode' => 'string',
			  'father_office_fax_number' => 'string',
			  'father_office_mobilephone' => 'string',
			  'father_office_email' => 'string',
			  'guardian_full_name' => 'string',
			  'guardian_relationship_to_applicant' => 'string',
			  'guardian_address_street' => 'string',
			  'guardian_address_city' => 'string',
			  'guardian_address_province' => 'string',
			  'guardian_address_postcode' => 'string',
			  'guardian_phone_areacode' => 'string',
			  'guardian_phone_number' => 'string',
			  'guardian_fax_areacode' => 'string',
			  'guardian_fax_number' => 'string',
			  'guardian_mobilephone' => 'string',
			  'guardian_email' => 'string',
			  'guardian_education' => 'string',
			  'guardian_occupation' => 'string',
			  'guardian_job_title' => 'string',
			  'guardian_office_name' => 'string',
			  'guardian_office_address_street' => 'string',
			  'guardian_office_address_city' => 'string',
			  'guardian_office_address_province' => 'string',
			  'guardian_office_address_postcode' => 'string',
			  'guardian_office_phone_areacode' => 'string',
			  'guardian_office_phone_number' => 'string',
			  'guardian_office_fax_areacode' => 'string',
			  'guardian_office_fax_number' => 'string',
			  'guardian_office_mobilephone' => 'string',
			  'guardian_office_email' => 'string',
			  'high_school_name' => 'string',
			  'high_school_graduation_month' => 'int',
			  'high_school_graduation_year' => 'int',
			  'high_school_admission_year' => 'int',
			  'high_school_headmaster_name' => 'string',
			  'high_school_address_street' => 'string',
			  'high_school_address_city' => 'string',
			  'high_school_address_province' => 'string',
			  'high_school_address_postcode' => 'string',
			  'high_school_phone_areacode' => 'string',
			  'high_school_phone_number' => 'string',
			  'high_school_fax_areacode' => 'string',
			  'high_school_fax_number' => 'string',
			  'high_school_mobilephone' => 'string',
			  'high_school_email' => 'string',
			  'mother_full_name' => 'string',
			  'mother_education' => 'string',
			  'mother_occupation' => 'string',
			  'mother_job_title' => 'string',
			  'mother_office_name' => 'string',
			  'mother_office_address_street' => 'string',
			  'mother_office_address_city' => 'string',
			  'mother_office_address_province' => 'string',
			  'mother_office_address_postcode' => 'string',
			  'mother_office_phone_areacode' => 'string',
			  'mother_office_phone_number' => 'string',
			  'mother_office_fax_areacode' => 'string',
			  'mother_office_fax_number' => 'string',
			  'mother_office_mobilephone' => 'string',
			  'mother_office_email' => 'string',
			  'number_of_children_in_family' => 'int',
			  'nth_child' => 'int',
			  'first_name' => 'string',
			  'middle_name' => 'string',
			  'last_name' => 'string',
			  'sex' => 'string',
			  'body_height' => 'string',
			  'body_weight' => 'string',
			  'blood_type' => 'string',
			  'citizenship' => 'string',
			  'religion' => 'string',
			  'motivation' => 'string',
			  'hopes' => 'string',
			  'personality' => 'string',
			  'strengths_and_weaknesses' => 'string',
			  'stressful_conditions' => 'string',
			  'biggest_life_problem' => 'string',
			  'plans' => 'string',
			  'grades_y1t1_average' => 'string',
			  'grades_y1t1_subjects' => 'string',
			  'grades_y1t2_average' => 'string',
			  'grades_y1t2_subjects' => 'string',
			  'grades_y2t1_average' => 'string',
			  'grades_y2t1_subjects' => 'string',
			  'grades_y2t2_average' => 'string',
			  'grades_y2t2_subjects' => 'string',
			  'grades_y3t1_average' => 'string',
			  'grades_y3t1_subjects' => 'string',
			  'grades_y3t2_average' => 'string',
			  'grades_y3t2_subjects' => 'string',
			  'grades_y4t1_average' => 'string',
			  'grades_y4t1_subjects' => 'string',
			  'grades_y4t2_average' => 'string',
			  'grades_y4t2_subjects' => 'string',
			  'grades_y5t1_average' => 'string',
			  'grades_y5t1_subjects' => 'string',
			  'grades_y5t2_average' => 'string',
			  'grades_y5t2_subjects' => 'string',
			  'grades_y6t1_average' => 'string',
			  'grades_y6t1_subjects' => 'string',
			  'grades_y6t2_average' => 'string',
			  'grades_y6t2_subjects' => 'string',
			  'program_afs' => 'int',
			  'program_yes' => 'int',
			  'program_jenesys_year' => 'int',
			  'program_jenesys_short' => 'int',
			  'in_acceleration_class' => 'int',
			  'in_pesantren' => 'int',
			  'country_preference_1' => 'string',
			  'country_preference_2' => 'string',
			  'country_preference_3' => 'string',
			  'country_preference_4' => 'string',
			  'country_preference_5' => 'string',
			  'country_preference_6' => 'string',
			  'country_preference_7' => 'string',
			  'country_preference_8' => 'string',
			  'country_preference_9' => 'string',
			  'country_preference_10' => 'string',
			  'country_preference_other' => 'string',
			  'pref_europe_1' => 'string',
			  'pref_europe_2' => 'string',
			  'pref_europe_3' => 'string',
			  'pref_europe_4' => 'string',
			  'pref_europe_5' => 'string',
			  'pref_europe_6' => 'string',
			  'pref_europe_7' => 'string',
			  'pref_europe_8' => 'string',
			  'pref_europe_9' => 'string',
			  'pref_europe_10' => 'string',
			  'pref_americas_1' => 'string',
			  'pref_americas_2' => 'string',
			  'pref_americas_3' => 'string',
			  'pref_americas_4' => 'string',
			  'pref_americas_5' => 'string',
			  'pref_americas_6' => 'string',
			  'pref_asia_1' => 'string',
			  'pref_asia_2' => 'string',
			  'pref_asia_3' => 'string',
			  'pref_asia_4' => 'string',
			  'pref_asia_5' => 'string',
			  'pref_asia_6' => 'string',
			  'recommendations_school_name' => 'string',
			  'recommendations_school_address' => 'string',
			  'recommendations_school_occupation' => 'string',
			  'recommendations_school_work_address' => 'string',
			  'recommendations_school_relationship' => 'string',
			  'recommendations_nonschool_name' => 'string',
			  'recommendations_nonschool_address' => 'string',
			  'recommendations_nonschool_occupation' => 'string',
			  'recommendations_nonschool_work_address' => 'string',
			  'recommendations_nonschool_relationship' => 'string',
			  'recommendations_close_friend_name' => 'string',
			  'recommendations_close_friend_address' => 'string',
			  'recommendations_close_friend_relationship' => 'string',
			  'relative_returnee_name' => 'string',
			  'relative_returnee_relationship' => 'string',
			  'relative_returnee_program' => 'string',
			  'relative_returnee_program_type' => 'string',
			  'relative_returnee_destination' => 'string',
			  'relative_returnee_address_street' => 'string',
			  'relative_returnee_address_city' => 'string',
			  'relative_returnee_address_province' => 'string',
			  'relative_returnee_address_postcode' => 'string',
			  'relative_returnee_homephone_areacode' => 'string',
			  'relative_returnee_homephone_number' => 'string',
			  'relative_returnee_fax_areacode' => 'string',
			  'relative_returnee_fax_number' => 'string',
			  'relative_returnee_mobilephone' => 'string',
			  'relative_returnee_email' => 'string',
			  'past_binabud_activities' => 'string',
			  'past_binabud_activities_year' => 'string',
			  'referrer' => 'string',
			  'relative_returnee_exists' => 'int',
			  'past_binabud_has' => 'int',
			  'past_binabud_activities_who' => 'string',
			  'past_binabud_activities_relationship' => 'string',
			  'grades_y7t1_average' => 'string',
			  'grades_y7t1_subjects' => 'string',
			  'grades_y7t2_average' => 'string',
			  'grades_y7t2_subjects' => 'string',
			  'grades_y8t1_average' => 'string',
			  'grades_y8t1_subjects' => 'string',
			  'grades_y8t2_average' => 'string',
			  'grades_y8t2_subjects' => 'string',
			  'grades_y9t1_average' => 'string',
			  'grades_y9t1_subjects' => 'string',
			  'grades_y9t2_average' => 'string',
			  'grades_y9t2_subjects' => 'string',
			  'grades_y10t1_average' => 'string',
			  'grades_y10t1_subjects' => 'string',
			  'selection_1_passed' => 'int',
			  'selection_2_passed' => 'int',
			  'selection_3_passed' => 'int',
			  'short_term_travel_destination' => 'string',
			  'short_term_travel_when' => 'string',
			  'short_term_travel_purpose' => 'string',
			  'long_term_travel_destination' => 'string',
			  'long_term_travel_when' => 'string',
			  'long_term_travel_purpose' => 'string',
			  'long_term_travel_activities' => 'string',
			  'short_term_travel_has' => 'int',
			  'long_term_travel_has' => 'int',

			);
			
			$this->_mapped_vertical_partitions[] = $this->vertical_partitions;

			/* parent::map_vertical_partitions(); */
			$done = true;
		}

		$delta = microtime(true) - $start;
	}

	/**
	 * Default values
	 */
	public function before_save() {
		// Sanitized entries
		$fn = trim($this->first_name, ' -');
		$mn = trim($this->middle_name, ' -');
		$ln = trim( $this->last_name, ' -');
		if ($mn)
			$fn .= ' ' . $mn;
		if ($ln)
			$fn .= ' ' . $ln;
		$this->sanitized_full_name = $this->sanitize_name($fn);

		if ($this->applicant_address_city) {
			$city = $this->applicant_address_city;

			if ($this->applicant_address_province == 'DKI Jakarta' && $city{0} == 'J')
				$city = 'Jakarta';
		}
		else
			$city = $this->chapter->chapter_name;
		$this->sanitized_high_school_name = $this->sanitize_school($this->high_school_name, $city);

		if ($this->in_acceleration_class)
			$this->program_yes = false;

		if (!$this->finalized && !$this->test_id)
			$this->test_id = $this->generate_test_id();
	}

	/**
	 * Finalize applicant if form is valid
	 */
	public function finalize() {
		$db = Helium::db();
		$participant_count = $db->get_var("SELECT COUNT(*) FROM participants");
		if ($participant_count) {
			// Participants have been imported. No more finalization allowed.
			// 2013-04-26 Makassar Outreach workaround
			// return false;
		}
		if ($this->validate()) {
			$this->finalized = true;
			$this->assign_test_id();
			return true;
		}
		else
			return false;
	}

	/**
	 * Assign test ID
	 */
	public function assign_test_id() {
		if (!$this->local_id)
			$this->local_id = $this->generate_local_id();
		$this->test_id = $this->generate_test_id();
	}

	/**
	 * Force finalize an applicant regardless of validity
	 */
	public function force_finalize() {
		$db = Helium::db();

		$this->finalized = true;
		$this->assign_test_id();
		return true;
	}

	/**
	 * Confirm applicant re-registration if has been finalized
	 */
	public function confirm() {
		if ($this->finalized) {
			$this->confirmed = true;
			return true;
		}
		else
			return false;
	}

	/**
	 * Is applicant expired?
	 */
	public function is_expired() {
		return !$this->expires_on->later_than('now');
	}

	/**
	 * Generate local ID based on chapter
	 */
	public function generate_local_id() {
		$db = Helium::db();
		
		if ($this->local_id)
			return $this->local_id;
		
		$local_id = (int) $db->get_var("SELECT local_id FROM applicants WHERE chapter_id='{$this->chapter_id}' AND program_year='{$this->program_year}' ORDER BY local_id DESC LIMIT 0,1");
		
		return $local_id + 1;
	}

	/**
	 * Generate test ID based on chapter
	 *
	 * Generates a temporary test ID if not finalized yet.
	 */
	public function generate_test_id() {
		$chapter_code = $this->chapter->chapter_code;
		if ($this->finalized) {
			$base = "YBA/YP%s-%s/%s/%s";
			$program_year = $this->program_year;
			$start_year = $program_year - 1;
			$ycl = substr($start_year, 2);
			$ycr = substr($program_year, 2);
			return sprintf($base, $ycl, $ycr, $chapter_code, str_pad($this->local_id, 4, '0', STR_PAD_LEFT));
		}
		else
			return "XYZ" . strtoupper(substr(sha1(mt_rand()), 0, 16));
	}

	/**
	 *
	 */
	public function get_short_test_id() {
		$base = "%s/%s";
		$chapter_code = $this->chapter->chapter_code;
		$local_id = $this->finalized ? str_pad($this->local_id, 4, '0', STR_PAD_LEFT) : 'XXXX';
		return sprintf($base, $chapter_code, $local_id);
	}

	/**
	 * Sanitize name
	 */
	public static function sanitize_name($name) {
		$name = trim($name);
		$name = strtolower($name);
		$name = ucwords($name);

		foreach (array('-', ' \'', 'O\'', '(') as $delimiter) {
	    	if (strpos($name, $delimiter)!==false) {
	    		$name = implode($delimiter, array_map('ucfirst', explode($delimiter, $name)));
	    	}
	    }

		return $name;
	}

	/**
	 * Sanitize school name
	 */
	public static function sanitize_school($school, $city = '') {
		// sanitize school name
		// use last school if multiple schools were used
		if ($slash = strpos($school, '/'))
			$school = substr($school, $slash + 1);
		// trim
		$school = trim($school);

		// sanitize the casing
		$school = self::sanitize_name($school);
		
		// sanitize misspellings
		$mispell = array(
			'/Mhammadiyah/' => 'Muhammadiyah',
			'/Nen?gri|Neg\./i' => 'Negeri',
			'/\(?\s?Sampoerna\s?Aca?d?e?m?y?\)?/i' => '(Sampoerna Academy)',
			'/Pelembang/i' => 'Palembang',
			// '/Alfa Centaury/i' => 'Alfa Centauri',
			'/\s+/' => ' ',
			'/[.,]/' => '',
		);
		
		$chapters = Helium::db()->get_results('SELECT * FROM chapters');
		foreach ($chapters as $chapter) {
			$pattern = "/ {$chapter->chapter_code}/i";
			$mispell[$pattern] = $chapter->chapter_name;
		}

		// do replacing here as it will affect subsequent patterns
		$school = preg_replace(array_keys($mispell), $mispell, $school);
		
		$spaces = array(
			'/\s+/' => ' ',
			'/([0-9])([a-z])/i' => '$1 $2',
			'/([a-z])([0-9])/i' => '$1 $2',
			'/([a-z0-9])\(/i' => '$1 (',
			'/\)([a-z0-9])/i' => ') $1',
			'/\s0*([0-9])/' => ' $1',
		);
		$school = preg_replace(array_keys($spaces), $spaces, $school);

		// sanitize SMAN -> SMA Negeri, etc.
		$uniform = array(
			'/^Sekolah Menengah Atas/i' => 'SMA',
			'/^R-([a-zA-Z])-BI/i' => '$1',
			'/^SMAS/i' => 'SMA',
			'/^MAS/i' => 'MA',
			'/^Sekolah Menengah Kejuruan/i' => 'SMK',
			'/^Madrasah Aliyah\s/' => 'MA ',
			'/^Madrasah Tsanawiyah\s/' => 'MTs ',
			'/^SM(A|K|P) ?N\s/i' => 'SM$1 Negeri ',
			'/^SM(A|P) ?K\s?([0-9]+)\s?BPK\s?Penabur/i' => 'SM$1 Kristen $2 BPK Penabur',
			'/^SM(A|P) ?I\s/i' => 'SM$1 Islam ',
			'/^SM(A|P) ?T\s/i' => 'SM$1 Terpadu ',
			'/^M(A|Ts) ?N\s/i' => 'M$1 Negeri ',
			'/^SMA ([0-9]+) ([a-zA-Z][aeiouAEIOU][a-zA-Z]*)(\s|$)/i' => 'SMA Negeri $1 $2',
			'/^UPT SMA/' => 'SMA',
			'/(^|\s)IT($|\s)/i' => '$1Islam Terpadu$1',
			'/BBS/i' => 'Bilingual Boarding School',
			'/\sKota\s/i' => ' ',
			'/\sSwasta\s/i' => ' ',
			'/Kab\s/i' => 'Kabupaten ',
			'/\(?rsbi\)?/i' => '',
			'/Jakarta\s(Utara|Selatan|Barat|Timur|Pusat)/i' => 'Jakarta',
		);
		$school = preg_replace(array_keys($uniform), $uniform, $school);

		// sanitize specific school names
		$specific = array(
			// '/^SM(A|P) Taruna Bakti.*/i' => 'SM$1 Taruna Bakti Bandung',
			// '/^SM(A|P).*Yahya$/i' => 'SM$1 Kristen Yahya Bandung',
			// '/^SM(A|P).*Yahya Bandung$/i' => 'SM$1 Kristen Yahya Bandung',
			// '/^SMA.*Alfa Centauri$/i' => 'SMA Alfa Centauri Bandung',
			// '/Sukamanah$/' => 'Sukamanah Tasikmalaya',
			// '/^.*Darul Arqam.+$/i' => 'MA Darul Arqam Muhammadiyah Daerah Garut',
			// '/^.*Pribadi.+$/i' => 'Pribadi Bilingual Boarding School Bandung',
			// '/^.*Muthahhari$/i' => 'SMA Plus Muthahhari Bandung',
			// '/^(.*Margahayu)( Bandung)?$/i' => '$1',
			// '/^SMA Terpadu Baiturrahman$/i' => 'SMA Terpadu Baiturrahman Kabupaten Bandung'
			'/.*Madania.*/i' => 'SMA Madania Bogor',
			'/.*Dwi ?Warna.*/i' => 'SMA Dwiwarna Bogor',
			'/.*Pribadi.*(Depok|Bandung).*/i' => 'SMA Pribadi $1',
			'/.*Taruna Bakti.*/i' => 'SMA Taruna Bakti Bandung',
			'/.*Lazuardi GIS.*/i' => 'SMA Lazuardi GIS Depok',
			'/.*Immim ?Pute?ra.*/i' => 'Pesantren IMMIM Putra Makassar',
			'/.*(Ruhul Islam Anak Bangsa|RIAB).*/i' => 'MA Ruhul Islam Anak Bangsa Aceh Besar',
			'/.*Labschool.*(Kebayoran|Rawamangun).*/i' => 'SMA Labschool $1 Jakarta',
			'/.*Sampoerna Academy.*Palembang/i' => 'SMA Negeri Sumatera Selatan (Sampoerna Academy) Palembang',
			'/al( -)?azhar/' => 'Al-Azhar'
		);

		if ($city) {
			$specific['/([0-9])$/'] = '$1 ' . $city;
		}
		
		$school = preg_replace(array_keys($specific), $specific, $school);
		
		$doubles = array('Negeri');
		foreach ($doubles as $double) {
			$pattern = "/$double\s$double/i";
			$replace = $double;
			$school = preg_replace($pattern, $replace, $school);
		}

		// $school = preg_replace(array_keys($spaces), $spaces, $school);

		// revert abbreviations
		$abbreviations = array(	'SMA', 'SMK', 'SMP', 'SD',
								'SMAK', // SMA Kristen/Katolik
								'MA', 'MTs', 'MI',
								'GIS', // Global Islamic School
								'PGII', // Persatuan Guru Islam Indonesia
								'PGRI', // Persatuan Guru Republik Indonesia
								'BPK', // (Yayasan)
								'BPI', // (Yayasan)
								'IBS', // Islamic Boarding School
								'ITUS', // Islam Terpadu Umar Sjarifuddin
								'PMT', // Pesantren Modern Terpadu
								'MBI',
								'BBPT',
								'RSBI',
								'ISWCS',
								'PSKD',
								'IMMIM',
								'UPI',
								'LTI',
								'II', 'III', 'IV', 'VI', 'VII',
								);
		foreach ($abbreviations as $abbr) {
			$pattern = "/(^| |-)$abbr(\$| |-)/i";
			$replace = "\\1$abbr\\2";
			$school = preg_replace($pattern, $replace, $school);
		}

		return $school;
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

		$required = array('first_name', 'place_of_birth', 'applicant_email', 'applicant_address_street',
			'sex', 'body_height', 'body_weight', 'blood_type', 'citizenship', 'religion',
			'father_full_name', 'mother_full_name', 'number_of_children_in_family', 'nth_child',
			'high_school_name', 'high_school_admission_year', 'high_school_graduation_year',
			'junior_high_school_name', 'junior_high_school_graduation_year', 'elementary_school_name',
			'elementary_school_graduation_year', 'years_speaking_english', 'favorite_subject', 'dream',
			'arts_hobby', 'sports_hobby', 'motivation', 'hopes', 'recommendations_school_name',
			'recommendations_school_address', 'recommendations_school_occupation',
			'recommendations_school_work_address', 'recommendations_school_relationship',
			'recommendations_nonschool_name', 'recommendations_nonschool_address',
			'recommendations_nonschool_occupation', /* 'recommendations_nonschool_work_address', */
			'recommendations_nonschool_relationship', 'recommendations_close_friend_name',
			'recommendations_close_friend_address', 'recommendations_close_friend_relationship',
			'personality', 'strengths_and_weaknesses', 'stressful_conditions', 'biggest_life_problem', 'plans');
		
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

		// Country preferences
		// This partners array should be moved somewhere...
		$partners = array(
			'americas' => array(
				'BRA' => 'Brazil',
				'CAN' => 'Kanada',
				'MEX' => 'Meksiko',
				'USA' => 'Amerika Serikat',
			),
			'europe' => array(
				'NED' => 'Belanda',
				'BFL' => 'Belgia (Flanders)',
				'BFR' => 'Belgia (Wallonia)',
				'CZE' => 'Republik Ceko',
				'FIN' => 'Finlandia',
				'FRA' => 'Perancis',
				'GER' => 'Jerman',
				'ISL' => 'Islandia',
				'ITA' => 'Italia',
				'NOR' => 'Norwegia',
				'SUI' => 'Swiss',
				'SWE' => 'Swedia',
				'TUR' => 'Turki',
			),
			'asia' => array(
				'CHN' => 'Cina',
				'JPN' => 'Jepang',
				'PHI' => 'Filipina',
				'THA' => 'Thailand',
			)
		);
		foreach ($partners as $c => $continent) {
			for ($i = 1; $i <= count($continent); $i++) {
				$required[] = 'pref_' . $c . '_' . $i;
			}
		}


		foreach ($required as $f) {
			$try = trim($this->$f, "- \t\n\r\0\x0B");
			if (!$try) {
				$check['incomplete'] = false;
				$this->incomplete_fields[] = $f;
			}
		}

		$check['picture'] = (bool) $this->picture;

		// list($a, $y, $j) = array($this->program_afs, $this->program_yes, $this->program_jenesys);
		// if (!$a && !$y && !$j) {
		// 	$check['program'] = false;
		// }

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
}
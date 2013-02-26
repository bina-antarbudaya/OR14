<?php

class HomeController extends AppController {

	public function firstrun_check() {
		$check = User::find()->count_all();
		if (!$check) {
			// First Run!
			$chapter = new Chapter;
			$chapter->chapter_code = 'INA';
			$chapter->chapter_name = 'Kantor Nasional';
			$chapter->chapter_timezone = 'Asia/Jakarta';
			$chapter->save();

			$user = new User;
			$user->username = 'admin';
			$user->set_password('admin');
			$user->role = 5;
			$user->chapter_id = $chapter->id;
			$user->save();

			$chapters = array (
				'DPS' => 'Bali',
				'BPP' => 'Balikpapan',
				'BNA' => 'Banda Aceh',
				'BDG' => 'Bandung',
				'BMS' => 'Banjarmasin',
				'BGR' => 'Bogor',
				'JKT' => 'Jakarta',
				'KRW' => 'Karawang',
				'MKS' => 'Makassar',
				'MLG' => 'Malang',
				'MTR' => 'Mataram',
				'MDN' => 'Medan',
				'PDG' => 'Padang',
				'PLM' => 'Palembang',
				'SMD' => 'Samarinda',
				'SMG' => 'Semarang',
				'SUB' => 'Surabaya',
				'JOG' => 'Yogyakarta',
				'PNK' => 'Pontianak'
			);
			
			$area = array (
				'DPS' => 'Bali',
				'BPP' => 'Kalimantan Timur',
				'BNA' => 'Aceh',
				'BDG' => 'Jawa Barat',
				'BMS' => 'Kalimantan Selatan',
				'BGR' => 'Jawa Barat',
				'JKT' => 'DKI Jakarta',
				'KRW' => 'Jawa Barat',
				'MKS' => 'Sulawesi Selatan',
				'MLG' => 'Jawa Timur',
				'MDN' => 'Sumatera Utara',
				'PDG' => 'Sumatera Barat',
				'PLM' => 'Sumatera Selatan',
				'SMD' => 'Kalimantan Timur',
				'SMG' => 'Jawa Tengah',
				'SUB' => 'Jawa Timur',
				'JOG' => 'DI Yogyakarta',
				'PNK' => 'Kalimantan Barat'
			);
			
			$timezones = array (
				'DPS' => 'Asia/Ujung_Pandang',
				'BPP' => 'Asia/Ujung_Pandang',
				'BNA' => 'Asia/Jakarta',
				'BDG' => 'Asia/Jakarta',
				'BMS' => 'Asia/Ujung_Pandang',
				'BGR' => 'Asia/Jakarta',
				'JKT' => 'Asia/Jakarta',
				'KRW' => 'Asia/Jakarta',
				'MKS' => 'Asia/Ujung_Pandang',
				'MLG' => 'Asia/Jakarta',
				'MTR' => 'Asia/Ujung_Pandang',
				'MDN' => 'Asia/Jakarta',
				'PDG' => 'Asia/Jakarta',
				'PLM' => 'Asia/Jakarta',
				'SMD' => 'Asia/Ujung_Pandang',
				'SMG' => 'Asia/Jakarta',
				'SUB' => 'Asia/Jakarta',
				'JOG' => 'Asia/Jakarta',
				'PNK' => 'Asia/Jakarta'
			);

			foreach ($chapters as $code => $name) {
				$chapter = new Chapter;
				$chapter->chapter_code = $code;
				$chapter->chapter_name = $name;
				$chapter->chapter_timezone = $timezones[$code];
				$chapter->chapter_area = $area[$code];
				$chapter->save();
				
				$user = new User;
				$user->username = 'chapter_' . strtolower(str_replace(' ', '_', $name));
				$user->set_password('antarbudaya');
				$user->role = 4;
				$user->chapter_id = $chapter->id;
				$user->save();
			}
		}
	}

	public function index() {
		// $this->firstrun_check();
		$this['chapters'] = Chapter::find('id != 1');
		$this['chapters']->set_order_by('chapter_name');
		$this['chapter_count'] = $this['chapters']->count_all();
		$this['this_year'] = Helium::conf('program_year') - 2;
	}

	public function phpinfo() {
		$this->render = false;
//		phpinfo();

		// For the moment, this function generates a merge query for convenience.

		$tables = array('applicant_activities', 'applicant_contact_info', 'applicant_education', 'applicant_fathers', 'applicant_guardians', 'applicant_high_schools', 'applicant_mothers', 'applicant_family', 'applicant_personal_details', 'applicant_personality', 'applicant_primary_school_grade_history', 'applicant_program_choices', 'applicant_recommendations', 'applicant_referral', 'applicant_secondary_school_grade_history', 'applicant_selection_progress', 'applicant_travel_history');
		
		$db = Helium::db();
		
		echo '<pre>';
		
		$alpha = ord('b');
		$colmap = array(); // key: field, value: table
		$colmap2 = array();
		foreach ($tables as $table) {
			// curr elmt
			$letter = chr($alpha);
			$cols = $db->get_col('SHOW COLUMNS IN ' . $table);
			$colmap2[$table] = array();
			
			foreach ($cols as $col) {
				if ($col != 'id' && $col != 'applicant_id') {
					$colmap[$col] = $table;
					$colmap2[$table][] = $col;
				}
			}
			
			// next elmt
			$alpha++;
		}
		
		echo '$this->_vertical_partition_table_map = ';
		echo var_export($colmap2);
		echo ';';
		
		echo "\n\n";
		
		echo '$this->_vertical_partition_column_map = ';
		echo var_export($colmap);
		echo ';';
		
		$query = $db->get_results("SHOW COLUMNS FROM better_applicants");

		// Exclude primary keys for partitions
		$exclude = array();
		if ($table != $this->_table_name) {
			$exclude = array('id', $this->_vertical_partition_foreign_key);
		}

		$columns = array();

		foreach ($query as $row) {
			$field = $row->Field;
			$type = $row->Type;

			if (!in_array($field, $exclude)) {

				$pos = strpos($type, '(');
				if ($pos > 0)
					$type = substr($type, 0, $pos);
			
				$type = strtolower($type);
				switch ($type) {
					case 'bit':
						$type = 'bool';
						break;
					case 'tinyint':
						if ($length == 1) {
							$type = 'bool';
							break;
						}
					case 'smallint':
					case 'int':
					case 'mediumint':
					case 'bigint':
						$type = 'int';
						break;
					case 'float':
					case 'double':
					case 'decimal':
						$type = 'float';
						break;
					case 'date':
					case 'time':
					case 'datetime':
					case 'timestamp':
					case 'year':
						$type = 'datetime';
						break;
					// to do: support the other column types (BLOB, etc)
					default:
						$type = 'string';
				}

				$columns[$field] = $type;
			}
		}

		echo "\n\n\n";
		
		echo '$this->_vertical_partition_column_types = ';
		echo var_export($columns);
		echo ';';
		
		echo "\n\n\n";
		
		exit;
		
		echo "CREATE VIEW better_applicants AS SELECT applicants.*";
		foreach ($colmap as $f => $t) {
			echo ",\n$t.$f";
		}
		echo "\nFROM applicants";
		foreach ($tables as $table) {
			echo "\nINNER JOIN $table ON $table.applicant_id=applicants.id";
		}

		exit;
	}
}
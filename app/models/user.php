<?php

/**
 * User
 *
 * @author Andhika Nugraha <andhika.nugraha@gmail.com>
 * @package auth
 */
class User extends HeliumRecord {

	public $id;
	public $username;
	public $password_hash;
	public $email;
	public $role;	// now an integer
	public $chapter_id;
	public $email_verified;

	public function init() {
		$this->belongs_to('chapter');
	}

	public function rebuild() {
		if ($this->role == 1) {
			$this->has_one('applicant');
		}
	}

	public function capable_of($role, $chapter_id = 0) {
		switch ($role) {
			case 'applicant':
				$min = 1;
				break;
			case 'volunteer':
				$min = 2;
				break;
			case 'chapter_staff':
			case 'chapter':	
				$min = 3;
				break;
			case 'chapter_admin':
			case 'chadmin':
				$min = 4;
				break;
			case 'national_admin':
			case 'nadmin':
			case 'admin':
			default:
				$min = 5;
		}

		if ($chapter_id) {
			if ($this->role >= 5)
				return true;
			else
				return $this->role >= $min && $this->chapter_id = $chapter_id;
		}
		else
			return $this->role >= $min;
	}

	/**
	 * Check whether user has access to a record
	 *
	 * This method returns true if the user has access to a chapter-related object,
	 * such as a Chapter or Applicant. A user is said to have access to the object
	 * if the user is either a national admin or belongs to the same chapter
	 * as the object.
	 *
	 * @param HeliumRecord $object The object to look up.
	 * @return bool True if the user has access to the object, false otherwise.
	 */
	public function has_access_to(HeliumRecord $object) {
		if ($this->capable_of('national_admin'))
			return true;
		elseif ($this->capable_of('chapter_admin')) {
			switch (get_class($object)) {
				case 'Chapter':
					return ($this->chapter_id == $object->id);
				case 'Applicant':
				default:
					return ($this->chapter_id == $object->chapter_id);
			}
		}
		elseif ($this->capable_of('applicant') && is_object($this->applicant)) {
			switch (get_class($object)) {
				default:
					return ($this->applicant->id == $object->applicant_id);
			}
		}
	}

	/**
	 *
	 */
	public function get_landing_page() {
		switch ($this->role) {
			case 1:
				if ($this->applicant->confirmed)
					$land = array('controller' => 'applicant', 'action' => 'confirmed');
				elseif ($this->applicant->finalized)
					$land = array('controller' => 'applicant', 'action' => 'finalized');
				elseif ($this->applicant->is_expired())
					$land = array('controller' => 'applicant', 'action' => 'reactivate');
				else
					$land = array('controller' => 'applicant', 'action' => 'form');

				break;
			case 2:
			case 3:
			case 4:
				$land = array('controller' => 'chapter', 'action' => 'dashboard');
				break;
			case 5:
				$land = array('controller' => 'chapter', 'action' => 'view', 'id' => 1);
				break;
		}

		return $land;
	}

	public static function hash_password($unhashed_password) {
		return sha1($unhashed_password);
	}

	public static function find_by_username_and_password($username, $password) {
		$password_hash = self::hash_password($password);

		$find = self::find(compact('username', 'password_hash'));

		if ($find)
			return $find->first();
		else
			return false;
	}

	public static function find_by_username_and_password_hash($username, $password_hash) {
		$find = self::find(compact($username, $password_hash));

		if ($find)
			return $find->first();
		else
			return false;
	}

	public static function find_by_username($username) {
		$find = self::find(compact('username'));

		if ($find)
			return $find->first();
		else
			return false;
	}

	public function get_email_address() {
		switch ($this->role) {
			case 1:
				return $this->applicant->applicant_email;
			case 3:
			case 4:
				return $this->chapter->get_email();
			case 5:
			default:
				return $this->email;
		}
	}
	
	public function get_nice_name() {
		switch ($this->role) {
			case 1:
				return $this->applicant->sanitized_full_name;
			case 3:
			case 4:
			case 5:
				return $this->chapter->get_title();
		}
	}
	
	public function set_password($unhashed_password) {
		$this->password_hash = $this->hash_password($unhashed_password);
	}
}
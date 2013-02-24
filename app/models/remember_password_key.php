<?php

class RememberPasswordKey extends HeliumRecord {
	public $user_id;
	public $token;
	public $expires_on; 
	
	public $lifetime = '3 days';
	
	public function init() {
		$this->belongs_to('user');
	}
	
	public function defaults() {
		$this->token = $this->generate_token();
		$this->expires_on = new HeliumDateTime('now');

		$this->expires_on->modify('+' . $this->lifetime);
	}
	
	public static function find_by_token($token) {
		$find = RememberPasswordKey::find(compact('token'));
		return $find->first();
	}

	public function is_expired() {
		return !$this->expires_on->later_than('now');
	}
	
	public function set_new_password($new_unhashed_password) {
		$this->user->set_password($new_unhashed_password);
		$this->user->save();
		$this->destroy();
	}
	
	public static function generate_token() {
		return sha1(mt_rand());
	}
}
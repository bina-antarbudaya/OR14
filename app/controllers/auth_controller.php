<?php

class AuthController extends AppController {

	public $default_action = 'login';

	public function login() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$username = trim($_POST['username']);
			$username = substr($username, 0, 255);
			$password = $_POST['password'];

			$try = $this->auth->process_login($username, $password);

			if ($try) {
				if ($_POST['remember'])
					$this->sessions->make_persistent();

				$this->session['just_logged_in'] = true;
				$this->auth->land();
			}
			else {
				$this->session['username'] = $username;
				$this['mode'] = 'fail';
			}
		}
		$dates = Helium::conf('dates');
		$reg_deadline = $dates['registration_deadline'];
		$now = new HeliumDateTime;
		$this['can_register'] = $now->earlier_than($reg_deadline);

		$lp = $this->session['last_params'];
		$destination_name = $lp['controller'] . '::' . $lp['action'];
		$this['destination_name'] = $destination_name;
		$this['destination_controller'] = $lp['controller'];
	}

	public function logout() {
		$this->render = false;
		$this->auth->process_logout();
		Gatotkaca::redirect('/');
	}
	
	public function recover() {
		$token = $_POST['token'] ? $_POST['token'] : $this->params['token'];
		
		$key = RememberPasswordKey::find_by_token($token);
		
		if (!$key)
			$error = 'not_found';
		if (!$error && $key->is_expired())
			$error = 'expired';
			
		if (!$error && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$new_unhashed_password = $_POST['password'];
			$retype_password = $_POST['retype_password'];
			
			if (!$new_unhashed_password)
				$error = 'incomplete';
			elseif ($new_unhashed_password != $retype_password)
				$error = 'mismatch';
				
			if (!$error) {
				$key->set_new_password($new_unhashed_password);
				$this->http_redirect(array('controller' => 'auth', 'action' => 'login', 'recovered' => 1));
			}
		}
		
		if (!$error)
			$user = $this['user'] = $key->user;
			
		$this['key'] = $key;
		$this['error'] = $error;
	}
	
	public function forgot() {
		$this['recaptcha'] = $recaptcha = new RECAPTCHA;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$identifier = $_POST['identifier'];

			if (!$recaptcha->check_answer())
				$error = 'recaptcha';

			if ($a = Applicant::find_by_test_id($identifier))
				$u = $a->user;
			if (!$u)
				$u = User::find_by_username($identifier);
				
			if (!$u)
				$error = 'not_found';
				
			if (!$error && !$u->get_email_address())
				$error = 'no_email';
			
			if (!$error) {
				$email = $u->get_email_address();
				$key = new RememberPasswordKey;
				$key->user_id = $u->id;
				$key->save();
				
				$token = $key->token;
				$expires_on = $key->expires_on;
				$try = $this->send_password_recovery_email($u, $key);
				
				if (!$try)
					$error = 'send_fail';
				else
					$this->http_redirect(array('controller' => 'auth', 'action' => 'forgot', 'success' => 1));
			}
		}
		
		$this['error'] = $error;
		$this['success'] = $this->params['success'];
	}
	
	public function send_password_recovery_email($u, $key) {
		$username = $u->username;
		$email = $u->get_email_address();
		$name = $u->get_nice_name();
		$pronoun = $u->capable_of('chapter_admin') ? 'Kakak' : 'Adik';
		$recover_url = PathsComponent::build_url(array('controller' => 'auth', 'action' => 'recover', 'token' => $key->token));

		$expires_on = $key->expires_on->format('j F Y');

		// load Swift and php-liquid
		require_once HELIUM_APP_PATH . '/includes/swift/swift_required.php';
		new Liquid;

		$tpl_plain = <<<EOF
Halo {{ name }},

{{ pronoun }} menerima surel ini karena {{ pronoun }} meminta pergantian password untuk akun {{ pronoun }}.

Username {{ pronoun }} adalah {{ username }}.

Untuk mengubah password {{ pronoun }}, silakan buka tautan di bawah ini:
{{ recover_url }}

Abaikan surel ini jika tidak pernah merasa meminta penggantian password.

EOF;

		$tpl_html = <<<EOF
<p>Halo {{ name }},</p>

<p>{{ pronoun }} menerima surel ini karena {{ pronoun }} meminta pergantian password untuk akun {{ pronoun }}.</p>

<p>Username {{ pronoun }} adalah <strong>{{ username }}</strong>.</p>

<p>Untuk mengubah password {{ pronoun }}, silakan buka <strong><a href="{{ recover_url }}">tautan ini</a></strong>.</p>

<p>Abaikan surel ini jika tidak pernah merasa meminta penggantian password.</p>

EOF;

		$vars = compact('pronoun', 'username', 'recover_url', 'name');
		$tpl = new LiquidTemplate;
		$body_plain = $tpl->parse($tpl_plain)->render($vars);
		$body_html = $tpl->parse($tpl_html)->render($vars);
		
		// Create the message
		$message = Swift_Message::newInstance()
		  ->setSubject('Penggantian password Seleksi Bina Antarbudaya')
		  ->setFrom(array('noreply@seleksi.bina-antarbudaya.info' => 'Bina Antarbudaya'))
		  ->setTo(array($email => $name))
		  ->setBody($body_plain)
		  ->addPart($body_html, 'text/html');

		// $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
		//   ->setUsername('national@binabudbdg.org')
		//   ->setPassword('#tcE^(F_=3s8');
		$transport = Swift_MailTransport::newInstance();

		try {
			$mailer = Swift_Mailer::newInstance($transport);
			$mailer->send($message);
		}
		catch (Exception $e) {
			$e->output();
			return false;
		}
		
		return true;
	}
}
<?php
namespace WilokeListGoFunctionality\Submit;

class User{
	public static $wilokeSubmissionRole = 'wiloke_submission';
	public static $userRecurringPaymentInfoKey = 'wiloke_submission_user_recurring_paypal_info';
	private static $saveCardKey = 'wiloke_save_card';

	public function __construct() {
		add_action('wp_ajax_nopriv_wiloke_verify_email', array($this, 'verifyEmailViaAjax'));
		add_action('wp_ajax_nopriv_wiloke_signin', array($this, 'AjaxSignIn'));
		add_action('wp_ajax_nopriv_wiloke_signup', array($this, 'AjaxSignUp'));
		add_action('wp_ajax_nopriv_wiloke_resetpassword', array($this, 'AjaxResetPassWord'));
		add_action('wp_enqueue_scripts', array($this, 'myaccountScripts'));
		add_filter('show_admin_bar', array($this, 'hideAdminBar'));
		add_action('admin_init', array($this, 'setDefaultRole'));
		add_action('admin_init', array($this, 'needAddRole'));
		add_action('wp_ajax_wiloke_save_card', array($this, 'prepareSaveCard'));
	}

	public static function isUserIDExists($userID){
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $userID));
		if($count == 1){
			return true;
		}else{
			return false;
		}
	}

	public static function getSuperAdminID(){
		global $wpdb;
		$aUserAdmin = get_super_admins();
		$adminUserName = $aUserAdmin[0];
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->users WHERE user_login = %s",
				$adminUserName
			)
		);
	}

	public function needAddRole(){
		self::getSuperAdminID();
		if ( get_option('wiloke_submission_need_add_role_now') ){
			self::addRoles();
			delete_option('wiloke_submission_need_add_role_now');
		}
	}

	public function setDefaultRole(){
		if ( !get_option('wiloke_listgo_set_default_role') ){
			update_option('default_role', self::$wilokeSubmissionRole);
			update_option('wiloke_listgo_set_default_role', true);
		}
	}

	public function hideAdminBar(){
		$aUserMeta = \Wiloke::getUserMeta(get_current_user_id());
		if ( !isset($aUserMeta['role']) || ($aUserMeta['role'] === self::$wilokeSubmissionRole) ){
			return false;
		}

		return true;
	}

	public function myaccountScripts(){
		if ( is_page_template('wiloke-submission/myaccount.php') ){
			wp_enqueue_script('spectrum', plugin_dir_url(dirname(__FILE__)) . '../public/asset/spectrum/spectrum.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('spectrum', plugin_dir_url(dirname(__FILE__)) . '../public/asset/spectrum/spectrum.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('backbone');
			wp_enqueue_media();
			wp_enqueue_script('wiloke-myaccount', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/myaccount.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function addCaps(){

	}

	/**
	 * @refer level_1: Contributor https://codex.wordpress.org/Roles_and_Capabilities#User_Levels
	 * @since 1.0
	 */
	public static function addRoles(){
		if ( get_role(self::$wilokeSubmissionRole) ){
			remove_role(self::$wilokeSubmissionRole);
		}

		add_role(
			self::$wilokeSubmissionRole,
			esc_html__('Wiloke Submission', 'wiloke'),
			array(
				'read'              => true,
				'level_1'           => true,
				'upload_files'      => true,
				'edit_listings'     => true,
				'delete_listings'   => true
			)
		);

		global $WilokeListGoFunctionalityApp;
		$aRoles = array('administrator', 'editor', 'author');
		foreach ($aRoles as $role){
			$getRole = get_role($role);
			foreach ( $WilokeListGoFunctionalityApp['post_types']['listing']['capabilities'] as $capability ){
				$getRole->add_cap( $capability );
			}
		}
		update_option('users_can_register', 1);
		flush_rewrite_rules();
	}

	public function AjaxResetPassWord(){
		global $wiloke;
		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['securitycodewrong']
				)
			);
		}

		$instError = $this->retrievePassword();

		if ( is_wp_error($instError) ){
			wp_send_json_error(
				array(
					'message' => $instError->get_error_message()
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => esc_html__('We just mailed a reset link to your email. Please check your mail box / spam box and click on that link.', 'wiloke')
			)
		);
	}

	protected function retrievePassword(){
		$errors = new \WP_Error();
		$user_data = '';
		if ( empty( $_POST['user_login'] ) ) {
			$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.', 'wiloke'));
		} elseif ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( wp_unslash( $_POST['user_login'] ) ) );
			if ( empty( $user_data ) ) {
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.', 'wiloke' ) );
			}
		} else {
			$login = trim($_POST['user_login']);
			$user_data = get_user_by('login', $login);
		}

		if ( $errors->get_error_code() ){
			return $errors;
		}

		if ( !$user_data || empty($user_data) ) {
			$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.', 'wiloke'));
			return $errors;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			return $key;
		}

		$message = esc_html__('Someone has requested a password reset for the following account:', 'wiloke') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'wiloke') . "\r\n\r\n";
		$message .= esc_html__('To reset your password, visit the following address:', 'wiloke') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

		if ( is_multisite() ) {
			$blogname = get_network()->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		}

		/* translators: Password reset email subject. 1: Site name */
		$title = sprintf( __('[%s] Password Reset', 'wiloke'), $blogname );

		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ){
			$errors->add('invalidcombo', __('The email could not be sent.', 'wiloke') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.', 'wiloke'));
			return $errors;
		}

		return true;
	}

	/*
	 * Sign In Account
	 * @since 1.0
	 */
	public function AjaxSignIn(){
		global $wiloke;

		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['securitycodewrong']
				)
			);
		}

		if ( !isset($_POST['userlogin']) || empty($_POST['userlogin']) ){
			wp_send_json_error(
				array(
					'target'  => 'username',
					'message' => $wiloke->aConfigs['translation']['signupfail']
				)
			);
		}

		if ( empty($_POST['password']) ){
			wp_send_json_error(array(
				'target'  => 'password',
				'message' => $wiloke->aConfigs['translation']['passwdrequired']
			));
		}

		$creds = array();
		$creds['user_login']    = $_POST['userlogin'];
		$creds['user_password'] = $_POST['password'];
		$creds['remember']      = isset($_POST['remember']) && ($_POST['remember'] === 'yes');
		$security = is_ssl() ? true : false;
		$user = wp_signon( $creds, $security );

		if ( is_wp_error($user) ){
			wp_send_json_error( array(
				'message' => $wiloke->aConfigs['translation']['signupfail']
			) );
		}else{
			wp_send_json_success( array(
				'message' => sprintf(esc_html__('Hello %s! Nice to see you back.', 'wiloke'), $user->display_name)
			) );
		}
	}

	public function AjaxSignUp(){
		global $wiloke;

		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['securitycodewrong']
				)
			);
		}

		if ( !isset($_POST['email']) || empty($_POST['email']) || !is_email($_POST['email']) ){
			wp_send_json_error(array(
				'message' => $wiloke->aConfigs['translation']['wrongemail']
			));
		}

		if ( !isset($_POST['password']) || empty($_POST['password']) ){
			wp_send_json_error(array(
				'message' => esc_html__('We need your password.', 'wiloke')
			));
		}

		if ( email_exists($_POST['email']) ){
			wp_send_json_error(
				array(
					'message' => sprintf(esc_html__('Sorry, the email %s is already existed! Please try another one.', 'wiloke'), $_POST['email'])
				)
			);
		}

		if ( !isset($_POST['username']) || empty($_POST['username']) || username_exists($_POST['username']) ){
			wp_send_json_error(
				array(
					'message' => sprintf(esc_html__('Sorry, the username %s is already existed! Please try another one.', 'wiloke'), $_POST['username'])
				)
			);
		}

		if ( isset($wiloke->aThemeOptions['toggle_google_recaptcha']) && ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'enable') ){
			$aVerifiedreCaptcha = self::verifyGooglereCaptcha($_POST['ggrecaptcha']);
			if ( $aVerifiedreCaptcha['status'] == 'error' ){
				wp_send_json_error(
					array(
						'message' => $aVerifiedreCaptcha['message']
					)
				);
			}
		}

		$randomPassword = trim($_POST['password']);
		$userID = wp_create_user($_POST['username'], $randomPassword, $_POST['email']);

		if ( is_wp_error($userID) ) {
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['ajaxerror']
				)
			);
		}

		wp_update_user(array('ID' => $userID, 'role' => self::$wilokeSubmissionRole));
		update_user_meta($userID, 'wiloke_user_IP', \Wiloke::clientIP());
		if ( \Wiloke::$wilokePredis ){
			$aUserData = get_object_vars(get_userdata($userID));
			\WilokeUser::putUserToRedis($aUserData);
		}

		wp_new_user_notification($userID, null, 'both');
		$ssl = is_ssl() ? true : false;
		wp_signon(array(
			'user_login'    =>  $_POST['email'],
			'user_password' =>  $randomPassword,
			'remember'      =>  false
		), $ssl);

		wp_send_json_success(
			array(
				'message' => esc_html__('Congratulations! Your account has been created successfully. Please check your inbox or spam to get your password.', 'wiloke')
			)
		);
	}

	public function verifyEmailViaAjax(){
		global $wiloke;

		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		if ( !isset($_POST['email']) || empty($_POST['email']) ){
			wp_send_json_error(array(
				'message' => $wiloke->aConfigs['translation']['wrongemail']
			));
		}

		if ( !is_email($_POST['email']) ){
			wp_send_json_error(array(
				'message' => $wiloke->aConfigs['translation']['wrongemail']
			));
		}

		if ( email_exists($_POST['email']) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['emailexisted']
				)
			);
		}

		wp_send_json_success();
	}

	public static function verifyGooglereCaptcha($reCaptcha){
		global $wiloke;
		$wrongGGreCaptcha = esc_html__('Gah! CAPTCHA verification failed', 'wiloke');
		if ( empty($reCaptcha) || empty($wiloke->aThemeOptions['google_recaptcha_site_secret']) ){
			return array(
				'message' => $wrongGGreCaptcha,
				'status'  => 'error'
			);
		}

		if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$remoteIP = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}else{
			$remoteIP = $_SERVER['REMOTE_ADDR'];
		}

		$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'method'        => 'POST',
				'timeout'       => 45,
				'redirection'   => 5,
				'httpversion'   => '1.0',
				'blocking'      => true,
				'headers'       => array(
					'Content-type'=>'application/x-www-form-urlencoded'
				),
				'body' => array(
					'secret'    => $wiloke->aThemeOptions['google_recaptcha_site_secret'],
					'response'  => $reCaptcha,
					'remoteip'  => $remoteIP
				),
				'cookies' => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'message' => $response->get_error_message(),
				'status'  => 'error'
			);
		} else {
			$aAPIResponse = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( $aAPIResponse['success'] === false ){
				return array(
					'message' => $wrongGGreCaptcha,
					'status'  => 'error'
				);
			}
		}

		return array(
			'status' => 'success'
		);
	}

	public static function signOn($userName='', $passWord=''){
		global $wiloke;
		if ( empty($userName) || empty($passWord) ){
			return array(
				'success'=>false,
				'message'=>$wiloke->aConfigs['translation']['signupfail']
			);
		}

		$creds['user_login']    = $userName;
		$creds['user_password'] = $passWord;

		$security = is_ssl() ? true : false;
		$oUser = wp_signon($creds, $security);
		$userIP = get_user_meta($oUser->ID, 'wiloke_user_IP', true);

		if ( empty($userIP) ){
			update_user_meta($oUser->ID, 'wiloke_user_IP', \Wiloke::clientIP());
		}

		if ( is_wp_error($oUser) ){
			return array(
				'success'=>false,
				'message'=>$wiloke->aConfigs['translation']['signupfail']
			);
		}else{
			wp_set_current_user($oUser->ID, $userName);
			return array(
				'success'=>true,
				'message'=>$oUser->ID
			);
		}
	}

	public static function verifyEmail($email){
		if ( !is_email($email) ){
			return array(
				'success' => false,
				'message' => esc_html__('Invalid email', 'wiloke')
			);
		}

		if ( email_exists($email) || username_exists($email) ){
			return array(
				'success' => false,
				'message' => esc_html__('The email address you entered is already in use on another account', 'wiloke')
			);
		}

		return array(
			'success' => true
		);
	}

	public static function createUserByEmail($email, $password=''){
		global $wiloke;

		if ( !is_email($email) ){
			return array(
				'success' => false,
				'message' => $wiloke->aConfigs['translation']['wrongemail']
			);
		}

		if ( email_exists($email) || username_exists($email) ){
			return array(
				'success' => false,
				'message' => $wiloke->aConfigs['translation']['emailexisted']
			);
		}

		if ( empty($password) ){
			return array(
				'success' => false,
				'message' => esc_html__('We need your password', 'wiloke')
			);
		}

		$userID = wp_create_user($email, $password, $email);

		if ( is_wp_error($userID) ) {
			return array(
				'success' => false,
				'message' => $wiloke->aConfigs['translation']['ajaxerror']
			);
		}
		wp_update_user(array('ID' => $userID, 'role' => self::$wilokeSubmissionRole));
		update_user_meta($userID, 'wiloke_user_IP', \Wiloke::clientIP());

		wp_new_user_notification($userID, null, 'both');
		$ssl = is_ssl() ? true : false;
		wp_signon(array(
			'user_login'    =>  $email,
			'user_password' =>  $password,
			'remember'      =>  true
		), $ssl);

		wp_set_current_user($userID, $email);

		return array(
			'success' => true,
			'message' => $userID
		);
	}

	public static function getCard(){
		if ( !is_user_logged_in() ){
			return false;
		}

		$aDefault = array(
			'card_name'     => '',
			'cvv'           => '',
			'expMonth'      => '',
			'expYear'       => '',
			'first_name'    => '',
			'last_name'     => '',
			'card_address1' => '',
			'card_city'     => '',
			'card_number'   => '',
			'card_country'  => '',
			'card_email'    => '',
			'card_phone'    => ''
		);

		$aUserMeta = get_user_meta(get_current_user_id(), self::$saveCardKey, true);

		$aUserMeta = wp_parse_args($aUserMeta, $aDefault);

		if ( empty($aUserMeta['card_name']) ){
			$aUserMeta = apply_filters('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/cardInfo', $aUserMeta);
		}

		return $aUserMeta;
	}

	public function prepareSaveCard(){
		if ( !isset($_POST['data']) || !is_user_logged_in() ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You don\'t have permission to access this page', 'wiloke')
				)
			);
		}

		$aData = array();
		foreach ( $_POST['data'] as $aInfo ){
			$aData[$aInfo['name']] = sanitize_text_field($aInfo['value']);
		}
		self::saveCard($aData);

		wp_send_json_success(
			array(
				'msg' => esc_html__('Congratulations! Your card information have been updated.', 'wiloke')
			)
		);
	}

	public static function saveCard($aData){
		update_user_meta(get_current_user_id(), self::$saveCardKey, $aData);
	}
}

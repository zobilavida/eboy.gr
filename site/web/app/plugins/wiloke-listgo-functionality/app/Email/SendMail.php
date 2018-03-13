<?php
namespace WilokeListGoFunctionality\Email;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;

class SendMail{
	public $aHeader = array('Content-Type: text/html; charset=UTF-8');
	public $subject = null;
	public $to = null;
	public $body = '';
	public $columnWidth = 700;
	public $sayHello = '';
	public $header = '';
	public $footer = '';
	protected $oSettings;

	public function __construct() {
		$this->oSettings = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
		$this->oSettings = json_decode($this->oSettings);
	}

	public function sendMail(){
		if ( !empty($this->oSettings->email_from) ){
			$this->aHeader[] = esc_html__('From: ', 'wiloke') . $this->oSettings->brandname . ' <'.$this->oSettings->email_from.'>';
		}
		wp_mail($this->to, $this->subject, $this->body, $this->aHeader);
	}

	public function header(){
		ob_start();
		include plugin_dir_path(__FILE__) . 'header.php';
		$header = ob_get_clean();
		$header = str_replace('{{wiloke_say_hello}}', $this->sayHello, $header);
		$header = str_replace('{{wiloke_logo}}', $this->oSettings->email_logo_url, $header);
		$header = str_replace('{{wiloke_brandname}}', $this->oSettings->brandname, $header);
		$header = str_replace('{{wiloke_title}}', $this->subject, $header);
		$this->header = $header;
		return $header;
	}

	public function footer(){
		ob_start();
		include plugin_dir_path(__FILE__) . 'footer.php';
		$footer = ob_get_clean();
		$footer = str_replace('{{wiloke_signature}}', $this->oSettings->email_signature, $footer);
		$footer = str_replace('{{wiloke_social_networks}}', '', $footer);
		$this->footer = $footer;
		return $footer;
	}

	public function specialEmail(){
		$this->subject = str_replace('{{wiloke_brandname}}', $this->oSettings->brandname, $this->subject);
		$this->body = $this->header() . $this->body . $this->footer();
		$this->sendMail();
	}

	public function defaultReplace($content, $post){
		return str_replace(
			array(
				'%post_title%',
				'%post_link%',
				'%brand%',
				'{{wiloke_column_width}}',
				'[break_down]'
			),
			array(
				'<strong>'.esc_html($post->post_title).'</strong>',
				'<a href="'.esc_url(get_permalink($post->ID)).'" target="_blank">'.esc_html($post->post_title).'</a>',
				$this->oSettings->brandname,
				$this->columnWidth,
				'<br>'
			),
			$content
		);
	}

	public function notifyPayment($oPaymentInfo){
		ob_start();
		include plugin_dir_path(__FILE__) . 'content.php';
		$template = ob_get_clean();
		if ( $oPaymentInfo->status === 'completed' || $oPaymentInfo->status === 'pending' ){
			$content = $this->oSettings->mail_completed_payment;
		}elseif ($oPaymentInfo->status === 'processing'){
			$content = $this->oSettings->mail_processing_payment;
		}else{
			$content = $this->oSettings->mail_failed_payment;
		}

		$content = str_replace(
			array(
				'%invoice_number%',
				'%invoice_date%',
				'%payment_method%',
				'%package_title%',
				'[breakdown]'
			),
			array(
				$oPaymentInfo->ID,
				$oPaymentInfo->created_at,
				$oPaymentInfo->method,
				get_the_title($oPaymentInfo->package_ID),
				'<br>'
			),
			$content
		);

		$this->body = str_replace('{{wiloke_content}}', $content, $template);
		$this->specialEmail();
	}
	
	public function expired($aData){
		$oUser = get_userdata($aData['user_ID']);
		$listTitle = get_the_title($aData['listing_ID']);
		$this->to = $oUser->user_email;
		$this->subject = esc_html__('Your listing: ', 'wiloke') . $listTitle . esc_html__( ' has been expired.', 'wiloke');

		$expired = absint($aData['expired']) >= 2 ? 'days' : 'day';
		$packagePageUrl = get_permalink($this->oSettings->package);

		$this->body = str_replace(array('%customer%', '%post_title%', '%expired%', '%renew_link%'), array($oUser->display_name, $listTitle, $expired, \WilokePublic::addQueryToLink($packagePageUrl, 'listing_id='.$aData['listing_ID'])), $this->oSettings->mail_listing_expired);
		$this->specialEmail();
	}

	public function outstandingBalance($aData){
		$oUser = get_userdata($aData['user_ID']);
		$packageName = get_the_title($aData['package_ID']);
		$this->to = $oUser->user_email;
		$this->subject = esc_html__('Failed Payment Notification for Subscription Pack: ', 'wiloke') . $packageName;

		$homeUrl = '<a href="'.esc_url(home_url('/')).'">'.get_option('blogname').'</a>';
		$this->body = str_replace(array('%customer%', '%post_title%', '%homeurl%'), array($oUser->display_name, $packageName, $homeUrl), $this->oSettings->mail_listing_outbalance);
		$this->specialEmail();
	}

	public function approved($post){
		ob_start();
		include plugin_dir_path(__FILE__) . 'content.php';
		$template = ob_get_clean();
		$pattern = $this->oSettings->mail_listing_approved;
		$content = $this->defaultReplace($pattern, $post);
		$this->body = str_replace('{{wiloke_content}}', $content, $template);
		$this->specialEmail();
	}

	public function refund($userID, $aData){
		$oUser = get_userdata($userID);
		$this->to = $oUser->user_email;
		$this->subject = sprintf(esc_html__('Refund from', 'wiloke'), get_option('blogname'));
		$this->body = sprintf(esc_html__('Dear %s! We just refund %s to you. Thanks for using our service!', 'wiloke'), $oUser->display_name, $aData['amount']);
		$this->specialEmail();
	}

	public function newUserRegister($userID){
		$user = get_userdata($userID);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

		wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

		if ( empty($plaintext_pass) ){
			return;
		}

		$message  = sprintf(__('Thanks for joining with %s!'), $blogname) . "\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail($user->user_email, sprintf(__('[%s] Your username info'), $blogname), $message);

	}
}
<?php
namespace WilokeListGoFunctionality\Register;

class RegisterBadges implements RegisterInterface{
	public $slug = 'listgo-badges';
	public static $aDefaults = array();
	public $aSettings = array();
	public static $optionKey = 'wiloke_listgo_badges';

	public function __construct() {
		add_action('init', array($this, 'init'));
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	}

	public function init(){
		self::$aDefaults = array(
			'super_admin' => array(
				'label' => esc_html__('Super Admin', 'wiloke'),
				'badge' => 'fa fa-gitlab',
                'color' => '#5dc57b',
                'image' => ''
			),
			'administrator' => array(
				'label' => esc_html__('Administrator', 'wiloke'),
				'badge' => 'fa fa-gitlab',
				'color' => '#5dc57b',
				'image' => ''
			),
			'editor' => array(
				'label' => esc_html__('Editor', 'wiloke'),
				'badge' => 'fa fa-empire',
				'color' => '#e57171',
				'image' => ''
			),
			'author' => array(
				'label' => esc_html__('Author', 'wiloke'),
				'badge' => 'fa fa-pencil',
                'color' => '#337ab7',
				'image' => ''
			),
			'wiloke_submission' => array(
				'label' => esc_html__('Submission', 'wiloke'),
				'badge' => 'fa fa-asterisk',
                'color' => '#02a3f5',
				'image' => ''
			),
			'contributor' => array(
				'label' => esc_html__('Contributor', 'wiloke'),
				'badge' => 'fa fa-smile-o ',
                'color' => '#3c763d',
				'image' => ''
			),
			'subscriber' => array(
				'label' => esc_html__('Subscriber', 'wiloke'),
				'badge' => 'fa fa-user',
                'color' => '#f5af02',
				'image' => ''
			)
		);
	}

	public function register() {
		add_menu_page(esc_html__('Badges', 'wiloke'), esc_html__('Badges', 'wiloke'), 'edit_theme_options', $this->slug, array($this, 'settings'), 'dashicons-awards', 29);
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) !== false ){
			wp_enqueue_style('spectrum', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/spectrum/spectrum.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('spectrum', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/spectrum/spectrum.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('semantic-ui', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_style('listgo-badges', plugin_dir_url(dirname(__FILE__)) . '../admin/css/badges-settings.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_style('listgo-general', plugin_dir_url(dirname(__FILE__)) . '../admin/css/general.css', array(), WILOKE_LISTGO_FC_VERSION);
		}
	}

	public static function getBadgeInfo($role){
	    if ( empty($role) ){
            return self::$aDefaults['subscriber'];
        }

        $aOptions = \Wiloke::getOption(self::$optionKey, true);
        $aOptions = wp_parse_args($aOptions, self::$aDefaults);

        if ( isset($aOptions[$role]) ){
            return $aOptions[$role];
        }

        $label = ucfirst(self::cleanName($role));
        return array(
            'label' => $label,
            'badge' => 'fa fa-user',
            'color' => '#f5af02',
            'image' => ''
        );
	}

	public static function cleanName($name){
        return str_replace(array('_', '-'), array(' ',  ' '), $name);
    }

	public function adminGetBadge($role, $field){
		if ( isset($this->aSettings[$role]) ){
			return $this->aSettings[$role][$field];
		}

		return '';
	}

	public function getBadgeUrl($role, $field){
	    if ( $this->adminGetBadge($role, $field) ){
	        return $this->adminGetBadge($role, $field);
        }

        return plugin_dir_url(dirname(__FILE__)) . '../admin/css/img/upload-button.png';
    }

	private function _saveSettings(){
		if ( isset($_POST['wiloke-listgo-badge-nonce']) && isset($_POST['wiloke_listgo_badges']) && wp_verify_nonce($_POST['wiloke-listgo-badge-nonce'], 'wiloke-listgo-badge-action') ){
			if ( current_user_can('edit_theme_options') ){
				$aData = $_POST['wiloke_listgo_badges'];
				foreach ( $aData as $role => $aVal ){
					$aData[$role]['label'] = sanitize_text_field($aVal['label']);
					$aData[$role]['badge'] = sanitize_text_field($aVal['badge']);
					$aData[$role]['color'] = sanitize_text_field($aVal['color']);
					$aData[$role]['image'] = sanitize_text_field($aVal['image']);
				}

				\Wiloke::updateOption(self::$optionKey, $aData);
			}
		}
	}

	public function settings(){
		global $wp_roles;
		$aAllRoles = $wp_roles->roles;

		$this->_saveSettings();
		$this->aSettings = \Wiloke::getOption(self::$optionKey, true);
		if ( empty($this->aSettings) ){
			$this->aSettings = self::$aDefaults;
		}else{
		    $this->aSettings = wp_parse_args($this->aSettings, self::$aDefaults);
        }

		?>
		<div id="badges-wrapper" class="wrap">
			<h2 class="ui dividing header" style="margin-bottom: 20px;">
				<?php esc_html_e('Badges Settings', 'wiloke'); ?>
				<span class="anchor"></span>
			</h2>
			<form action="<?php echo esc_url(admin_url('admin.php?page='.$this->slug)); ?>" method="POST">
				<?php wp_nonce_field('wiloke-listgo-badge-action', 'wiloke-listgo-badge-nonce'); ?>
				<div class="ui info message">
					<p><?php \Wiloke::wiloke_kses_simple_html(sprintf(__('The image is top priority, so you can ignore the color setting and the icon setting if you decide to use the image badge instead.  The Font-awesome is used for creating the Badge. You can choose the icons  <a href="%s" target="_blank">here</a>', 'wiloke'), 'http://fontawesome.io/icons/'), false); ?></p>
				</div>
				<div class="ui form">
					<div class="three fields">
						<div class="field"><strong><?php esc_html_e('Role', 'wiloke'); ?></strong></div>
						<div class="field"><strong><?php esc_html_e('Label', 'wiloke'); ?></strong></div>
						<div class="field"><strong><?php esc_html_e('Icon', 'wiloke'); ?></strong></div>
						<div class="field"><strong><?php esc_html_e('Color', 'wiloke'); ?></strong></div>
						<div class="field"><strong><?php esc_html_e('Image', 'wiloke'); ?></strong></div>
					</div>
				</div>
				<?php foreach ( $aAllRoles as $role => $oRole ) :  ?>
				<div class="ui form">
					<div class="five fields">
						<div class="field">
							<input type="text" readonly="" placeholder="<?php echo esc_attr($role); ?>">
						</div>
						<div class="field">
							<input id="label-<?php echo esc_attr($role) ?>" type="text" name="wiloke_listgo_badges[<?php echo esc_attr($role) ?>][label]" value="<?php echo esc_attr($this->adminGetBadge($role, 'label')); ?>">
						</div>
						<div class="field">
							<input id="badge-<?php echo esc_attr($role) ?>" type="text" name="wiloke_listgo_badges[<?php echo esc_attr($role) ?>][badge]" value="<?php echo esc_attr($this->adminGetBadge($role, 'badge')); ?>">
						</div>
                        <div class="field">
                            <input id="badge-<?php echo esc_attr($role) ?>" class="wiloke-colorpicker" type="text" name="wiloke_listgo_badges[<?php echo esc_attr($role) ?>][color]" value="<?php echo esc_attr($this->adminGetBadge($role, 'color')); ?>">
                        </div>
                        <div class="field">
                            <div class="ui action input">
                                <input id="badge-<?php echo esc_attr($role) ?>" class="wiloke-image" type="text" name="wiloke_listgo_badges[<?php echo esc_attr($role) ?>][image]" value="<?php echo esc_attr($this->adminGetBadge($role, 'image')); ?>">
                                <button class="wiloke-js-upload-badge ui icon button" style="background-image: url(<?php echo $this->getBadgeUrl($role, 'image'); ?>)"></button>
                            </div>
                        </div>
					</div>
				</div>
				<?php endforeach; ?>
				<button class="ui button" type="submit"><?php esc_html_e('Save Settings', 'wiloke'); ?></button>
			</form>
		</div>
		<?php
	}
}
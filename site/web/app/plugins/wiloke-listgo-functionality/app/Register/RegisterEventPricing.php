<?php
namespace WilokeListGoFunctionality\Register;

class RegisterEventPricing implements RegisterInterface{
	public static $postType = 'event-pricing';
	public static $capability = 'edit_theme_options';
	public static $showEventOnCarouselKey = 'toggle_show_events_on_event_carousel';
	public static $showEventOnListKey = 'toggle_show_events_on_event_listing';

	public function __construct() {
		add_action('add_meta_boxes', array($this, 'registerMetaBoxes'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('save_post_'.self::$postType, array($this, 'saveSettings'), 10, 2);
	}

	public function enqueueScripts($hook){
		global $post;
		if (  isset($post->post_type) && ($post->post_type === self::$postType) ){
			wp_dequeue_script('sematic-selection-ui');
			wp_enqueue_style('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/semantic.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function registerMetaBoxes(){
		add_meta_box(
			'wiloke-event-shown-on-event-carousel',
			esc_html__( 'Show Event On Event Carousel Shortcode', 'wiloke' ),
			array($this, 'toggleShowEventOnCarouselShortcode'),
			self::$postType
		);

		add_meta_box(
			'wiloke-event-shown-on-event-listing',
			esc_html__( 'Show Event On Event Listing Shortcode', 'wiloke' ),
			array($this, 'toggleShowEventOnListingShortcode'),
			self::$postType
		);
	}

	public function toggleShowEventOnCarouselShortcode(){
		global $WilokeListGoFunctionalityApp, $post;
		?>
		<div id="wiloke-toggle-show-event-on-carousel" class="wrap form ui">
			<?php
			$status = get_post_meta($post->ID, self::$showEventOnCarouselKey, true);
			$aField = $WilokeListGoFunctionalityApp['settings']['show_events_on_carousel_shortcode'];
			$aField['value'] = !empty($status) ? $status : $aField['default'];
			\WilokeHtmlHelper::semantic_render_select_field($aField);
			?>
		</div>
		<?php
	}

	public function toggleShowEventOnListingShortcode(){
		global $WilokeListGoFunctionalityApp, $post;
		?>
		<div id="wiloke-toggle-show-event-on-listing" class="wrap form ui">
			<?php
			$status = get_post_meta($post->ID, self::$showEventOnListKey, true);
			$aField = $WilokeListGoFunctionalityApp['settings']['show_events_on_list_shortcode'];
			$aField['value'] = !empty($status) ? $status : $aField['default'];
			\WilokeHtmlHelper::semantic_render_select_field($aField);
			?>
		</div>
		<?php
	}

	public function saveSettings($postID){
		if (!current_user_can(self::$capability) ){
			return false;
		}
		
		if ( isset($_POST['toggle_show_events_on_event_carousel']) && !empty($_POST['toggle_show_events_on_event_carousel']) ){
			update_post_meta($postID, self::$showEventOnCarouselKey, $_POST['toggle_show_events_on_event_carousel']);
		}

		if ( isset($_POST['toggle_show_events_on_event_listing']) && !empty($_POST['toggle_show_events_on_event_listing']) ){
			update_post_meta($postID, self::$showEventOnListKey, $_POST['toggle_show_events_on_event_listing']);
		}
	}

	public function register() {
		// TODO: Implement register() method.
	}
}
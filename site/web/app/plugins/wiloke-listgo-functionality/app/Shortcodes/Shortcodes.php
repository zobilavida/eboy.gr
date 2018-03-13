<?php
namespace WilokeListGoFunctionality\Shortcodes;

use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;

class Shortcodes{
    public $aTagsAllowable = '<i><ul><li><h1><h2><h3><h4><h5><h6><a><strong><ol><blockquote><code><ins><img>';
    public $gallery_types = array();

	public function __construct() {
		add_action('init', array($this, 'registerShortcodes') );
		add_shortcode('wiloke_row', array($this, 'rowShortcodes'));
		add_shortcode('wiloke_column', array($this, 'columnShortcodes'));
		add_shortcode('accordions', array($this, 'accordionShortcodes'));
		add_shortcode('menu_prices', array($this, 'menuPricesShortCode'));
		add_shortcode('menu_item', array($this, 'menuItemShortcode'));
		add_shortcode('accordion', array($this, 'accordionItemShortcode'));

		add_shortcode('list_features', array($this, 'listFeaturesShortcode'));
		add_shortcode('list_item', array($this, 'listItemShortcode'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueShortcodeControl'));
		add_action('wp_enqueue_scripts', array($this, 'enqueueShortcodeControl'));
		add_action('wp_footer', array($this, 'addShortcodeSettingsSkeletons'));
		add_action('admin_footer', array($this, 'addShortcodeSettingsSkeletons'));

		add_shortcode('wiloke_price_table', array($this, 'renderPriceTableShortcode'));
		add_shortcode('wiloke_accordion', array($this, 'renderAccordionShortcode'));
		add_shortcode('wiloke_list_features', array($this, 'renderListFeatures'));

		add_action('init', array($this, 'addEditorStyle'));
	}


	function addEditorStyle(){
	    if ( !is_admin() ){
		    if ( !is_page_template('wiloke-submission/addlisting.php') && !is_page_template('wiloke-submission/addlisting-old.php') ){
			    return false;
		    }
        }
		add_editor_style(array(WILOKE_LISTGO_FUNC_URL.'public/source/css/placeholder-editor.css'));
	}

    public function rowShortcodes($aAtts, $content=''){
	    $aAtts = shortcode_atts(
		    array(
			    'class' => '',
		    ),
		    $aAtts
	    );
	    $content = strip_tags($content, $this->aTagsAllowable);
	    ob_start();
	    ?>
        <div class="row <?php echo esc_attr($aAtts['class']); ?>"><?php echo do_shortcode($content); ?></div>
        <?php
        return ob_get_clean();
    }

	public function columnShortcodes($aAtts, $content=''){
		$aAtts = shortcode_atts(
			array(
				'class' => 'col-md-12',
			),
			$aAtts
		);
		$content = strip_tags($content, $this->aTagsAllowable);
		ob_start();
		?>
        <div class="<?php echo esc_attr($aAtts['class']); ?>"><?php echo do_shortcode($content); ?></div>
		<?php
        return ob_get_clean();
	}

	public function menuItemShortcode($aAtts){
		$aAtts = shortcode_atts(
			array(
				'title' => 'Chicken',
                'price' => '$22',
                'description'=>'Whole breast with skin & lemon thyme seasoning'
			),
			$aAtts
		);
		ob_start();
        ?>
        <li>
            <?php if ( !empty($aAtts['title']) ) : ?>
            <h4 class="wil-menus__title"><?php echo esc_html($aAtts['title']); ?></h4>
            <?php endif; ?>
	        <?php if ( !empty($aAtts['price']) ) : ?>
                <span class="wil-menus__price"><?php echo esc_html($aAtts['price']); ?></span>
	        <?php endif; ?>
		    <?php if ( !empty($aAtts['description']) ) : ?>
            <p class="wil-menus__description"><?php \Wiloke::wiloke_kses_simple_html($aAtts['description']); ?></p>
		    <?php endif; ?>
        </li>
        <?php
        return ob_get_clean();
    }

	public function menuPricesShortCode($aAtts, $content){
		$content = strip_tags($content, '<i><a><strong>');
		ob_start();
		?>
        <div class="listgo-menu-price-wrapper">
            <ul class="wil-menus">
                <?php echo do_shortcode($content); ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

	protected function allowRenderingTarget($target){
		global $post;

		$aAuthorMeta = \Wiloke::getUserMeta($post->post_author);
		if ( $aAuthorMeta['role'] == 'wiloke_submission' ){
			$aAuthorPlan = CustomerPlan::getCustomerPlanByID($post->post_author);
			$aPackageSettings = \Wiloke::getPostMetaCaching($aAuthorPlan['packageID'], 'pricing_settings');
			if ( isset($aPackageSettings[$target]) && $aPackageSettings[$target] == 'disable' ){
				return false;
			}
		}

		return true;
	}

	public function renderPriceTableShortcode($aAtts){
		$aAtts = shortcode_atts(
			array(
			    'id' => '',
                'data-settings' => '',
                'data-title' => '',
                'class'
            ),
			$aAtts
		);

		if ( !$this->allowRenderingTarget('toggle_listing_shortcode') ){
            return '';
		}

		if ( empty($aAtts['data-settings']) ){
		    return '';
        }

        $aItems = json_decode(urldecode(base64_decode($aAtts['data-settings'])), true);

		ob_start();
		?>
        <div id="<?php echo esc_attr($aAtts['id']); ?>" class="listgo-menu-price-wrapper wiloke-menu-price-new-version">
            <?php if ( !empty($aAtts['data-title']) ) : ?>
                <h3><?php echo esc_html($aAtts['data-title']); ?></h3>
            <?php endif; ?>
            <ul class="wil-menus">
                <?php foreach ( $aItems as $aItem ) : ?>
                <li>
                    <?php if ( !empty($aItem['name']) ) : ?>
                        <h4 class="wil-menus__title"><?php echo esc_html($aItem['name']); ?></h4>
                    <?php endif; ?>
                    <?php if ( !empty($aItem['price']) ) : ?>
                        <span class="wil-menus__price"><?php echo esc_html($aItem['price']); ?></span>
                    <?php endif; ?>
                    <?php if ( !empty($aItem['description']) ) : ?>
                        <p class="wil-menus__description"><?php \Wiloke::wiloke_kses_simple_html($aItem['description']); ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
		<?php
		return ob_get_clean();
	}

	public function renderListFeatures($aAtts){
		$aAtts = shortcode_atts(
			array(
				'id' => '',
				'data-settings' => '',
				'data-title' => '',
				'class'
			),
			$aAtts
		);

        if ( !$this->allowRenderingTarget('toggle_listing_shortcode') ){
            return '';
        }

		if ( empty($aAtts['data-settings']) ){
			return '';
		}

		$aItems = json_decode(urldecode(base64_decode($aAtts['data-settings'])), true);
		ob_start();
		?>
        <?php if ( !empty($aAtts['data-title']) ) : ?>
        <h3><?php echo esc_html($aAtts['data-title']); ?></h3>
        <?php endif; ?>
        <ul id="<?php echo esc_attr($aAtts['id']); ?>" class="wil-icon-list wiloke-list-features-new-version">
			<?php
			foreach ( $aItems as $aItem ) :
				$active = !empty($aItem['unavailable']) ? 'disable' : 'enable';
            ?>
                <li class="<?php echo esc_attr($active); ?>"><i class="icon_box-checked"></i> <?php \Wiloke::wiloke_kses_simple_html($aItem['name']); ?></li>
            <?php endforeach; ?>
        </ul>
		<?php
		return ob_get_clean();
    }

	public function renderAccordionShortcode($aAtts){
		if ( !$this->allowRenderingTarget('toggle_listing_shortcode') ){
            return '';
		}

		$aAtts = shortcode_atts(
			array(
				'id' => '',
				'data-settings' => '',
				'data-title' => '',
				'class'
			),
			$aAtts
		);

		if ( empty($aAtts['data-settings']) ){
			return '';
		}

		$aItems = json_decode(urldecode(base64_decode($aAtts['data-settings'])), true);
		ob_start();
        ?>
        <?php if ( !empty($aAtts['data-title']) ) : ?>
        <h3><?php echo esc_html($aAtts['data-title']); ?></h3>
        <?php endif; ?>
        <div id="<?php echo esc_attr($aAtts['id']); ?>" class="wil_accordion wil_accordion--1 wiloke-accordion-new-version">
            <?php
            $i=0;
            foreach ( $aItems as $aItem ) :
                $active = $i == 0 ? 'active' : 'notactive';
            ?>
            <h3 class="wil_accordion__header <?php echo esc_attr($active); ?>"><a href="#<?php echo esc_attr($aAtts['id'].$i); ?>"><?php echo esc_html($aItem['title']); ?></a></h3>
            <div id="<?php echo esc_attr($aAtts['id'].$i); ?>" class="wil_accordion__content <?php echo esc_attr($active); ?>"><?php echo wpautop($aItem['description']); ?></div>
            <?php $i++; endforeach; ?>
        </div>
        <?php
		return ob_get_clean();
    }

	public function listItemShortcode($aAtts){
		$aAtts = shortcode_atts(
			array(
				'status' => 'checked',
				'content' => 'Wifi'
			),
			$aAtts
		);
		$status = $aAtts['status'] === 'unchecked' ? 'disable' : 'enable';
		ob_start();
		?>
        <li class="<?php echo esc_attr($status); ?>"><i class="icon_box-checked"></i> <?php \Wiloke::wiloke_kses_simple_html($aAtts['content']); ?></li>
		<?php
		return ob_get_clean();
    }

	public function listFeaturesShortcode($atts, $content){
		$content = strip_tags($content, '<i><a><strong>');
		return '<ul class="wil-icon-list">'.do_shortcode($content).'</ul>';
    }

	public function accordionShortcodes($atts, $content){
	    $content = strip_tags($content, $this->aTagsAllowable);
        return '<div class="wil_accordion wil_accordion--1">'.do_shortcode($content).'</div>';
    }

    public function accordionItemShortcode($aAtts){
	    $aAtts = shortcode_atts(
            array(
                'default_expanded' => 'no',
                'question' => 'How to keep balance in my life?',
                'answer' => 'Life is like riding a bicycle. To keep your balance you must keep moving.'
            ),
		    $aAtts
        );
	    $active = $aAtts['default_expanded'] === 'no' ? 'notactive' : 'active';
	    $id = uniqid('wiloke_');
	    ob_start();
        ?>
        <h3 class="wil_accordion__header <?php echo esc_attr($active); ?>"><a href="#<?php echo esc_attr($id); ?>"><?php echo esc_html($aAtts['question']); ?></a></h3>
    <div id="<?php echo esc_attr($id); ?>" class="wil_accordion__content <?php echo esc_attr($active); ?>"><?php echo wpautop($aAtts['answer']); ?></div>
        <?php
        return ob_get_clean();
    }

    /*
     * Jetpack
     */
    public function enqueueJetpackToWPMedia(){
        /**
         * This only happens if we're not in Jetpack, but on WPCOM instead.
         * This is the correct path for WPCOM.
         */
        wp_enqueue_script( 'jetpack-gallery-settings', WP_PLUGIN_URL . '/gallery-settings/gallery-settings.js', array( 'media-views' ), WILOKE_LISTGO_FC_VERSION, true );
    }

	function printJetpackTemplate() {
		/**
		 * Filter the default gallery type.
		 *
		 * @module tiled-gallery
		 *
		 * @since 2.5.1
		 *
		 * @param string $value A string of the gallery type. Default is ‘default’.
		 *
		 */
		$default_gallery_type = apply_filters( 'jetpack_default_gallery_type', 'default' );

		?>
        <script type="text/html" id="tmpl-jetpack-gallery-settings">
            <label class="setting">
                <span><?php _e( 'Type', 'jetpack' ); ?></span>
                <select class="type" name="type" data-setting="type">
					<?php foreach ( $this->gallery_types as $value => $caption ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $default_gallery_type ); ?>><?php echo esc_html( $caption ); ?></option>
					<?php endforeach; ?>
                </select>
            </label>
        </script>
		<?php
	}

	public function enqueueShortcodeControl(){
		if ( !is_admin() ){
			global $post;
			if ( !isset($post->ID) || (!is_page_template('wiloke-submission/addlisting.php') && !is_page_template('wiloke-submission/addlisting-old.php')) ){
				return false;
			}
        }

        wp_enqueue_script('backbone');
		wp_enqueue_script( 'mce-view' );
		wp_enqueue_script( 'image-edit' );
		wp_enqueue_script( 'media-views' );

		if ( class_exists('Jetpack_Gallery_Settings') ){
			$this->gallery_types = apply_filters( 'jetpack_gallery_types', array( 'default' => __( 'Thumbnail Grid', 'jetpack' ) ) );
			if ( count( $this->gallery_types ) > 1 ) {
				$this->enqueueJetpackToWPMedia();
				add_action( 'print_media_templates', array( $this, 'printJetpackTemplate' ) );
			}
        }

		wp_enqueue_style('wiloke-shortcode-popup', get_template_directory_uri() . '/css/popup-css.css', array(), WILOKE_LISTGO_FC_VERSION);
        wp_localize_script('jquery-migrate', 'WILOKE_LISTGO_SC_TRANSLATION', array(
            'menu_price_btn' => esc_html__('Menu Prices', 'wiloke'),
            'list_features_btn' => esc_html__('List Features', 'wiloke'),
            'accordion_btn' => esc_html__('Accordion', 'wiloke'),
            'edit'   => esc_html__('Edit', 'wiloke'),
            'remove' => esc_html__('Remove', 'wiloke'),
            'title'  => esc_html__('Title', 'wiloke'),
            'price_name' => esc_html__('Name', 'wiloke'),
            'price_cost' => esc_html__('5$', 'wiloke'),
            'price_desc' => esc_html__('Write something to describe about this item', 'wiloke'),
            'accordion_title' => esc_html__('Einstein', 'wiloke'),
            'accordion_desc' => esc_html__('Life is like riding a bicycle. To keep your balance you must keep moving.', 'wiloke'),
            'needupdate' => esc_html__('Please update to higher plan to use this feature', 'wiloke')
        ));
	}

	public function registerShortcodes(){
		add_filter( 'mce_external_plugins', array($this, 'registerShortcodeJs') );
		add_filter( 'mce_buttons_4', array($this, 'registerShortcodeButtons') );

		add_filter('mce_external_plugins', array($this, 'RegisterTableJs'));
		add_filter('mce_buttons_2', array($this, 'addTableBtnToEditor') );
    }

    public function RegisterTableJs($aJS){
	    global $tinymce_version;
	    if ( version_compare( $tinymce_version, '4100', '<' ) ) {
		    $aJS['table'] = WILOKE_LISTGO_FUNC_URL . 'public/source/js/tinymce4-table/plugin.min.js';
	    } else {
		    $aJS['table'] = WILOKE_LISTGO_FUNC_URL . 'public/source/js/tinymce41-table/plugin.min.js';
	    }

	    return $aJS;
    }

    public function addTableBtnToEditor($aBtn){
	    $aBtn[] = 'table';
	    return $aBtn;
    }

    public function registerShortcodeButtons($aButtons){
	    array_push($aButtons, 'listgo_new_accordions', 'listgo_new_list_features', 'listgo_new_menu_prices');
	    return $aButtons;
    }

    public function addShortcodeSettingsSkeletons(){
        include plugin_dir_path(__FILE__) . 'templates/price-title.tpl.php';
        include plugin_dir_path(__FILE__) . 'templates/price-item.tpl.php';
        include plugin_dir_path(__FILE__) . 'templates/price-settings-skeleton.tpl.php';

	    include plugin_dir_path(__FILE__) . 'templates/accordion-title.tpl.php';
	    include plugin_dir_path(__FILE__) . 'templates/accordion-item.tpl.php';
	    include plugin_dir_path(__FILE__) . 'templates/accordion-settings-skeleton.tpl.php';

	    include plugin_dir_path(__FILE__) . 'templates/list-features-title.tpl.php';
	    include plugin_dir_path(__FILE__) . 'templates/list-feature-item.tpl.php';
	    include plugin_dir_path(__FILE__) . 'templates/list-features-settings-skeleton.tpl.php';
    }

    public function registerShortcodeJs($aJs){
	    if ( !is_admin() ){
		    global $post;
		    if ( !isset($post->ID) || (!is_page_template('wiloke-submission/addlisting.php') && !is_page_template('wiloke-submission/addlisting-old.php')) ){
			    return $aJs;
		    }
	    }

	    $aJs['listgo_new_shortcodes'] = plugin_dir_url(dirname(__FILE__)) . '../public/source/js/new-shortcodes.js';
	    return $aJs;
    }
}
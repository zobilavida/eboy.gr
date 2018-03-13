<?php
use WilokeWidget\Supports\Helpers;
use \WilokeListGoFunctionality\AlterTable\AlterTableReviews;

class WilokeListOfTerms extends WP_Widget
{
	public $aDef = array('title'=>'', 'taxonomy' => 'listing_location', 'number_of_posts'=>4, 'orderby'=>'count', 'description'=>'article|articles');
	public function __construct()
	{
		parent::__construct('wiloke_termslisting', WILOKE_WIDGET_PREFIX . esc_html__( 'List of Terms', 'wiloke'), array('classname'=>'widget_termslisting widget_listings'));
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField( esc_html__('Number of posts', 'wiloke'), $this->get_field_id('number_of_posts'), $this->get_field_name('number_of_posts'), $aInstance['number_of_posts']);
		Helpers::selectField( esc_html__('Order By', 'wiloke'), $this->get_field_id('orderby'), $this->get_field_name('orderby'), array('count'=>esc_html__('Term Count', 'wiloke'), 'name'=>esc_html__('Term Name', 'wiloke')), $aInstance['orderby']);
		Helpers::selectField( esc_html__('Get Term By', 'wiloke'), $this->get_field_id('taxonomy'), $this->get_field_name('taxonomy'), array('listing_location'=>esc_html__('Listing Location', 'wiloke'), 'listing_cat'=>esc_html__('Listing Category', 'wiloke')), $aInstance['taxonomy']);
		Helpers::textField( esc_html__('Post Count Prefix', 'wiloke'), $this->get_field_id('description'), $this->get_field_name('description'), $aInstance['description']);
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			if ( $key == 'number_of_posts' )
			{
				$aInstance[$key] = (int)$val;
			}else{
				$aInstance[$key] = strip_tags($val);
			}
		}
		return $aInstance;
	}

	public function widget($atts, $aInstance) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		$aTerms = wiloke_listgo_widget_get_cache($atts);

		if ( !empty($aTerms) ){
			$aTerms = json_decode($aTerms);
		}else{
			$aTerms = get_terms(array(
				'taxonomy'   => $aInstance['taxonomy'],
				'orderby'    => $aInstance['orderby'],
				'hide_empty' => true,
				'number'     => $aInstance['number_of_posts']
			));

			wiloke_listgo_widget_set_cache($atts, $aTerms);
		}

		if ( empty($aTerms) && !is_wp_error($aTerms) ) {
			return;
		}

		if ( empty($aInstance['description']) ){
            $singular   = __('article', 'wiloke');
            $plural     = __('articles', 'wiloke');
        }else{
			$aDescription = explode('|', $aInstance['description']);
			$plural = $aDescription[1];
			$singular = $aDescription[0];
        }


		echo $atts['before_widget'];
		if ( !empty($aInstance['title']) ) {
			echo $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}
		echo '<ul>';
			foreach ( $aTerms as $oTerm ) :
				$aSettings = get_option('_wiloke_cat_settings_'.$oTerm->term_id);
				?>
					<li>
						<a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>">
							<?php if ( isset($aSettings['featured_image']) ) : ?>
								<img class="lazy" data-src="<?php echo esc_url(wp_get_attachment_image_url($aSettings['featured_image'], 'thumbnail')); ?>" alt="<?php echo esc_attr($oTerm->name); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
							<?php endif; ?>
							<div class="overflow-hidden">
								<h4><?php echo esc_html($oTerm->name); ?></h4>
                                <?php

                                ?>
								<p><?php echo esc_html($oTerm->count) . ( ($oTerm->count > 1) ? esc_html($plural) : esc_html($singular) ); ?></p>
							</div>
						</a>
					</li>
				<?php
			endforeach;
		echo '</ul>';
		echo $atts['after_widget'];
	}
}
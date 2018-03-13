<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeAuthorInformation extends WP_Widget
{
	public $aDef = array('url' => '', 'description'=>'', 'name'=>'Wiloke');
	public function __construct()
	{
		parent::__construct('wiloke_author_information', WILOKE_WIDGET_PREFIX . ' Author Information', array('classname'=>'widget_about widget_author widget_author_information widget_text', 'description'=>esc_html__('This widget is only available for author page.', 'wiloke')) );
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		do_action('wiloke-widgets/app/Widgets/WilokeListingInformation', $aInstance);
	}

	public function update($aNewinstance, $aOldinstance) {}

	public function widget($atts, $aInstance)
	{
		if ( !is_author() ){
			return false;
		}

		$authorID  = get_queried_object()->ID;
		$aUserInfo  = WilokePublic::getUserMeta($authorID);

		echo $atts['before_widget'];
		?>
		<div class="widget_author__header">
            <a href="<?php echo esc_url(get_author_posts_url($authorID)); ?>">
                <?php if ( isset($aUserInfo['meta']['wiloke_profile_picture']) ) : ?>
                    <div class="widget_author__avatar">
                        <?php
                        $img = wp_get_attachment_image($aUserInfo['meta']['wiloke_profile_picture'], array(128, 128));
                        if(!empty($img)) {
                            print $img;
                        } else {
                            $firstCharacter = substr($aUserInfo['display_name'], 0, 1);
                            echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div class="overflow-hidden">
                    <h4 class="widget_author__name"><?php echo esc_html($aUserInfo['display_name']); ?></h4>
                    <span class="widget_author__role">
                        <?php WilokePublic::renderBadge($aUserInfo['role']); ?>
                    </span>
                </div>
            </a>
		</div>

		<div class="widget_author__content">

			<ul class="widget_author__address">
				<?php if ( isset($aUserInfo['meta']['wiloke_address']) && !empty($aUserInfo['meta']['wiloke_address']) ) : ?>
					<li>
						<i class="fa fa-map-marker"></i>
						<?php echo Helpers::kses_html($aUserInfo['meta']['wiloke_address']); ?>
					</li>
				<?php endif; ?>

				<?php if ( isset($aUserInfo['meta']['wiloke_phone']) && !empty($aUserInfo['meta']['wiloke_phone']) ) : ?>
					<li>
						<i class="fa fa-phone"></i>
                        <a href="tel:<?php echo Helpers::kses_html($aUserInfo['meta']['wiloke_phone']); ?>"><?php echo Helpers::kses_html($aUserInfo['meta']['wiloke_phone']); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( isset($aUserInfo['user_url']) && !empty($aUserInfo['user_url']) ) : ?>
					<li>
						<i class="fa fa-globe"></i>
						<a href="<?php echo esc_url($aUserInfo['user_url']); ?>" target="_blank"><?php echo esc_html($aUserInfo['user_url']); ?></a>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ( isset($aUserInfo['meta']['wiloke_user_socials']) && !empty($aUserInfo['meta']['wiloke_user_socials']) ) : ?>
				<div class="widget_author__social">
					<?php
					foreach ( $aUserInfo['meta']['wiloke_user_socials'] as $socialKey => $url ) :
						if ( !empty($url) ) :
							?>
							<a href="<?php echo esc_url($url); ?>"><i class="fa fa-<?php echo esc_attr($socialKey); ?>"></i></a>
							<?php
						endif;
					endforeach;
					?>
				</div>
			<?php endif; ?>
			<?php if ( isset($aUserInfo['user_url']) && !empty($aUserInfo['user_url']) ) : ?>
				<div class="widget_author__link">
					<a href="<?php echo esc_url($aUserInfo['user_url']); ?>" target="_blank"><?php esc_html_e('Visit Website', 'wiloke'); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}

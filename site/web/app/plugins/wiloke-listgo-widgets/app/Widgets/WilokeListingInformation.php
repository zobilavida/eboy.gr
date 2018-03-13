<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeListingInformation extends WP_Widget
{
    public $aDef = array('url' => '', 'description'=>'', 'name'=>'Wiloke');
    public function __construct()
    {
        parent::__construct('wiloke_about', WILOKE_WIDGET_PREFIX . ' (OSP) Information', array('classname'=>'widget_about widget_author widget_text', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);
	    do_action('wiloke-widgets/app/Widgets/WilokeListingInformation', $aInstance);
    }

    public function update($aNewinstance, $aOldinstance) {}

    public function widget($atts, $aInstance)
    {
        global $post, $wiloke;
        $aInstance = wp_parse_args($aInstance, $this->aDef);
        if ( !is_singular('listing') ){
            return false;
        }
        $aUserInfo = WilokePublic::getUserMeta($post->post_author);
        $aGeneralSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
        $aSocialMedia = Wiloke::getPostMetaCaching($post->ID, 'listing_social_media');

	    if ( has_action('wiloke-widgets/app/Widgets/WilokeListingInformation') ) :
		    do_action('wiloke-widgets/app/Widgets/WilokeListingInformation', $atts, $aInstance, $aUserInfo);
        else:
            echo $atts['before_widget'];
        ?>
            <div class="widget_author__header">
                <a href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>">
                    <?php if ( isset($aUserInfo['meta']['wiloke_profile_picture']) ) : ?>
                    <div class="widget_author__avatar">
                        <?php
                            $img = wp_get_attachment_image($aUserInfo['meta']['wiloke_profile_picture'], array(128, 128));
                            if(!empty($img)) {
                                print $img;
                            } else {
                                $firstCharacter = strtoupper(substr($aUserInfo['display_name'], 0, 1));
                                echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                            }
                        ?>
                    </div>
                    <?php endif; ?>

                    <div class="overflow-hidden">
                        <h4 class="widget_author__name"><?php echo esc_html($aUserInfo['display_name']); ?></h4>
                        <span class="widget_author__role"><?php WilokePublic::renderBadge($aUserInfo['role']); ?></span>
                    </div>
                </a>
            </div>

            <div class="account-subscribe">
                <?php
                $profileUrl = WilokePublic::getPaymentField('myaccount', true);
                $followingUrl = !empty($profileUrl) ? WilokePublic::addQueryToLink($profileUrl, 'mode=following&user='.$post->post_author) : '#';
                $followersUrl = !empty($profileUrl) ? WilokePublic::addQueryToLink($profileUrl, 'mode=followers&user='.$post->post_author) : '#';
                ?>
                <span class="followers"><a href="<?php echo esc_url($followersUrl) ?>"><span class="count"><?php echo esc_html(WilokePublic::getNumberOfFollowers($post->post_author)); ?></span> <?php esc_html_e('Followers', 'listgo'); ?></a></span>
                <span class="following"><a href="<?php echo esc_url($followingUrl) ?>"><span class="count"><?php echo esc_html(WilokePublic::getNumberOfFollowing($post->post_author)); ?></span> <?php esc_html_e('Following', 'listgo'); ?></a></span>
	            <?php
	            if ( empty(WilokePublic::$oUserInfo) || ( !empty(WilokePublic::$oUserInfo) && ($post->post_author != WilokePublic::$oUserInfo->ID) ) ) :
		            if ( $wiloke->aThemeOptions['listing_toggle_following_author'] === 'enable' ) :
			            $followStatus = esc_html__('Follow', 'wiloke');
			            if ( !empty(WilokePublic::$oUserInfo) ){
				            $status = WilokePublic::isFollowing($post);
				            if ( $status ){
					            $followStatus = esc_html__('Following', 'wiloke');
				            }
			            }
			            ?>
                        <a href="#" class="js_subscribe widget_author__follow listgo-btn listgo-btn--sm" data-authorid="<?php echo esc_attr($post->post_author); ?>"><?php echo esc_html($followStatus); ?> <i class="fa fa-rss"></i></a>
                <?php endif; endif; ?>
            </div>

            <div class="widget_author__content">

                <ul class="widget_author__address">
                    <?php if ( isset($aGeneralSettings['map']['location']) && !empty($aGeneralSettings['map']['location']) ) : ?>
                    <li>
                        <i class="fa fa-map-marker"></i>
                        <?php echo Helpers::kses_html($aGeneralSettings['map']['location']); ?>
                    </li>
                    <?php endif; ?>

	                <?php if ( isset($aGeneralSettings['phone_number']) && !empty($aGeneralSettings['phone_number']) ) : ?>
                    <li>
                        <i class="fa fa-phone"></i>
                        <a href="tel:<?php echo esc_attr($aGeneralSettings['phone_number']); ?>"><?php echo Helpers::kses_html($aGeneralSettings['phone_number']); ?></a>
                    </li>
	                <?php elseif ( !empty($aUserInfo['meta']['wiloke_phone']) ) : ?>
                        <li>
                            <i class="fa fa-phone"></i>
                            <a href="tel:<?php echo esc_attr($aUserInfo['meta']['wiloke_phone']); ?>"><?php echo Helpers::kses_html($aUserInfo['meta']['wiloke_phone']); ?></a>
                        </li>
	                <?php endif; ?>

	                <?php if ( isset($aGeneralSettings['website']) && !empty($aGeneralSettings['website']) ) : ?>
                    <li>
                        <i class="fa fa-globe"></i>
                        <a href="<?php echo esc_url($aGeneralSettings['website']); ?>" target="_blank"><?php echo esc_html($aGeneralSettings['website']); ?></a>
                    </li>
	                <?php elseif ( !empty($aUserInfo['user_url']) ) : ?>
                    <li>
                        <i class="fa fa-globe"></i>
                        <a href="<?php echo esc_url($aUserInfo['user_url']); ?>" target="_blank"><?php echo esc_html($aUserInfo['user_url']); ?></a>
                    </li>
	                <?php endif; ?>
                </ul>

                <?php if ( !empty($aSocialMedia) || !empty($aUserInfo['meta']['wiloke_user_socials']) ) : ?>
                <div class="widget_author__social">
                    <?php
                    foreach ( WilokeSocialNetworks::$aSocialNetworks as $socialKey ) :
                        if ( !isset($aSocialMedia[$socialKey]) || empty($aSocialMedia[$socialKey]) ){
                            $url = isset($aUserInfo['meta']['wiloke_user_socials'][$socialKey]) ? $aUserInfo['meta']['wiloke_user_socials'][$socialKey] : '';
                        }else{
                            $url = $aSocialMedia[$socialKey];
                        }
                        if ( !empty($url) ) :
	                        $socialIcon = 'fa fa-'.str_replace('_', '-', $socialKey);
                    ?>
                        <a href="<?php echo esc_url($url); ?>"><i class="<?php echo esc_attr($socialIcon); ?>"></i></a>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                <?php endif; ?>

	            <?php if ( isset($aGeneralSettings['website']) && !empty($aGeneralSettings['website']) ) : ?>
                <div class="widget_author__link">
                    <a href="<?php echo esc_url($aGeneralSettings['website']); ?>"><?php esc_html_e('Visit Website', 'wiloke'); ?></a>
                </div>
                <?php elseif ( !empty($aUserInfo['user_url']) ) : ?>
                <div class="widget_author__link">
                    <a href="<?php echo esc_url($aUserInfo['user_url']); ?>"><?php esc_html_e('Visit Website', 'wiloke'); ?></a>
                </div>
                <?php endif; ?>

            </div>
        <?php
            echo $atts['after_widget'];
        endif;
    }
}

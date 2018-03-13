<?php

class piInstagram extends piWilokeWidgets
{
    public $aDef = array( 'title' =>'Instagram', 'user_id'=>'', 'username'=>'', 'number_of_photos' => 3, 'access_token' => '', 'cache_interval'=>'');
    public function __construct()
    {
        $args = array('classname'=>'widget_instagram widget_wiloke_instagram widget_photo', 'description'=>'');
        parent::__construct("wiloke_instagram", parent::PI_PREFIX . 'Instagram Feed ', $args);
    }

    public function form($aInstance)
    {
        $aInstance            = wp_parse_args( $aInstance, $this->aDef );
        $aInstagramSettings   = get_option('_pi_instagram_settings');
        if (empty($aInstance['cache_interval'])) {
            if (!empty($aInstagramSettings['cache_interval'])) {
                $aInstance['cache_interval'] = $aInstagramSettings['cache_interval'];
            }
            else {
                $aInstance['cache_interval'] = $this->aDef['cache_interval'];
            }

        }
        if ( isset($aInstagramSettings['access_token']) && !empty($aInstagramSettings['access_token']) )
        {
            $this->pi_text_field( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
            $this->pi_text_field( esc_html__('Number Of Photos', 'wiloke'), $this->get_field_id('number_of_photos'), $this->get_field_name('number_of_photos'), $aInstance['number_of_photos']);
            $this->pi_text_field( esc_html__('Cache interval', 'wiloke'), $this->get_field_id('cache_interval'), $this->get_field_name('cache_interval'), $aInstance['cache_interval']);

            echo '<p>';
                echo '<code class="wiloke-help">'.esc_html__('Leave empty to clear cache', 'wiloke').'</code>';
            echo '</p>';

        }else{
            echo '<p>';
                echo '<code class="wiloke-help">'.esc_html__('Instagram Access Token is required. Please ', 'kratos').'<a target="_blank" href="'.esc_url(admin_url('options-general.php?page=wiloke-instagram')).'">'.esc_html__('click me to supply this.', 'kratos').'</a></code>';
            echo '</p>';
        }

    }

    public function update($aNewinstance, $aOldinstance)
    {
        $aInstance = $aOldinstance;
        foreach ( $aNewinstance as $key => $val )
        {
            if ( $key == 'number_of_photos' )
            {
                $aInstance[$key] = (int)$val;
            }else{
                $aInstance[$key] = strip_tags($val);
            }
        }

        return $aInstance;
    }

    public function widget( $atts, $aInstance )
    {
        $aInstance                  = wp_parse_args($aInstance, $this->aDef);
        $aInstagramSettings         = get_option('_pi_instagram_settings');
        $aInstance['access_token']  = isset($aInstagramSettings['access_token']) ? $aInstagramSettings['access_token'] : '';
        $cacheInstagram = null;
        
        print $atts['before_widget'];

        if ( !empty($aInstance['title']) )
        {
            print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
        }
        ?>
        <div class='widgetinstagram' data-col="3" data-vertical="10" data-horizontal="10">
            <div class="instagram-content">
                <?php
                if ( empty($aInstance['access_token']) )
                {
                    if ( current_user_can('edit_theme_options') )
                    {
                        esc_html_e('Please config your instagram', 'wiloke');
                    }
                }else{
                    if ( !empty($aInstance['username']) )
                    {
                        $type = 'username';
                        $info = $aInstance['username'];
                    }else{
                        $type = 'self';
                        $info = $aInstagramSettings['userid'];
                    }

                    if ( !empty($aInstance['cache_interval']) )
                    {
                        $cacheInstagram = get_transient('wiloke_cache_instagram_'.$info);
                    }

                    if ( !empty($cacheInstagram) )
                    {
                        print $cacheInstagram;
                    }else{
                        $content = $this->pi_handle_instagram_feed($info, $aInstance['access_token'], $aInstance['number_of_photos'], $type);
                        print $content;

                        if ( !empty($aInstance['cache_interval']) )
                        {
                            set_transient('wiloke_cache_instagram_'.$info, $content, absint($aInstance['cache_interval']));
                        }   
                    }
                }
                ?>
            </div>
        </div>
    <?php
    //endif;
        print $atts['after_widget'];
    }

    public function pi_get_instagram_userid($info, $accessToken, $args)
    {
        $url = 'https://api.instagram.com/v1/users/search?q='.$info.'&access_token='.$accessToken;
        $oSearchProfile = wp_remote_get( esc_url_raw( $url ), $args);

        if ( !empty($oSearchProfile) && !is_wp_error($oSearchProfile) )
        {
            $oSearchProfile = wp_remote_retrieve_body($oSearchProfile);
            $oSearchProfile = json_decode($oSearchProfile);

            if ( $oSearchProfile->meta->code === 200 )
            {
               foreach ( $oSearchProfile->data as $oInfo )
               {
                    if ( $oInfo->username === $info )
                    {
                        return $oInfo->id;
                    }
               }
            }
        }

        return;
    }

    public function pi_handle_instagram_feed($info, $accessToken, $count=6, $type='self')
    {
        $args = array( 'decompress' => false, 'timeout' => 30, 'sslverify'   => true );
        if ( $type == 'self' )
        {
            return $this->pi_get_instagram_images($info, $accessToken, $count, $args);
        }else{
            $userID = $this->pi_get_instagram_userid($info, $accessToken, $args);

            if ( !empty($userID) )
            {
                return $this->pi_get_instagram_images($userID, $accessToken, $count, $args);
            }
        }
        
    }

    public function pi_get_instagram_images($info, $accessToken, $count, $args)
    {
        $url   = 'https://api.instagram.com/v1/users/'.$info.'/media/recent?access_token='.$accessToken.'&count='.$count;

        $getInstagram = wp_remote_get( esc_url_raw( $url ), $args);
        if ( !is_wp_error($getInstagram) )
        {
            $getInstagram = wp_remote_retrieve_body($getInstagram);
            $getInstagram = json_decode($getInstagram);

            if ( $getInstagram->meta->code === 200 )
            {
                $out = '';
                $out .= '<div class="widget__photo widget__photo--grid widget-grid">';
                    for ( $i=0; $i<$count; $i++ )
                    {
                        $caption = isset($getInstagram->data[$i]->caption->text) ? $getInstagram->data[$i]->caption->text : 'Instagram';
                        $out .= '<div class="item"><a href="'.esc_url($getInstagram->data[$i]->link).'" class="img bg-scroll" target="_blank" style="background-image: url('. esc_url($getInstagram->data[$i]->images->thumbnail->url) .')"><img src="'.esc_url($getInstagram->data[$i]->images->thumbnail->url).'" alt="'.esc_attr($caption).'" /></a></div>';
                    }
                $out .= '</div>';
                return $out;
            }
        }

        return;  
    }
}


?>
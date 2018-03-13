<?php

/**
 * Created by ninhle - wiloke team
 * @since 1.0
 */

class piTWitterFeed extends piWilokeWidgets
{
    public $aDef = array('title'=>'Recent Tweets', 'username'=>'wilokethemes', 'limit'=>2, 'consumer_key'=>'', 'consumer_secret'=>'', 'access_token'=>'', 'access_token_secret'=>'', 'cache_interval'=>0);
    public function __construct()
    {
        parent::__construct('wiloke_twitterfeed', parent::PI_PREFIX.'Twitter Feed', array('classname'=>'widget_twitter'));
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);

        $aTwitter = get_option('_wiloke_twitter_settings');

        if ( isset($aTwitter['consumer_key']) && isset($aTwitter['consumer_secret']) && isset($aTwitter['access_token']) && isset($aTwitter['access_token_secret']) )
        {
            piWilokeWidgets::pi_text_field('Title', $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
            piWilokeWidgets::pi_text_field('Username', $this->get_field_id('username'), $this->get_field_name('username'), $aInstance['username']);
            piWilokeWidgets::pi_text_field('Limit', $this->get_field_id('limit'), $this->get_field_name('limit'), $aInstance['limit']);
            piWilokeWidgets::pi_text_field('Cache Interval', $this->get_field_id('cache_interval'), $this->get_field_name('cache_interval'), $aInstance['cache_interval']);

        }else{
            echo '<p>';
            echo '<code>Please <a href="'.esc_url(admin_url('options-general.php?page=wiloke-twitter')).'" target="_blank">'.esc_html__('click  me', 'wiloke') .'</a> to enter your twitter api.</code>';
            echo '</p>';
        }
    }

    public function update($aNewinstance, $aOldinstance)
    {
        $aInstance = $aOldinstance;
        foreach ( $aNewinstance as $key => $val )
        {
            $aInstance[$key] = strip_tags($val);
        }
        return $aInstance;
    }

    public function widget($atts, $aInstance)
    {
        $aTwitter  = get_option('_wiloke_twitter_settings');
        $aInstance = wp_parse_args($aInstance, $this->aDef);
        $aInstance = wp_parse_args($aTwitter, $aInstance);

        print $atts['before_widget'];
            if ( !empty($aInstance['title']) )
            {
                print $atts['before_title'] . $aInstance['title'] . $atts['after_title'];
            }

            if ( empty($aInstance['consumer_key']) || empty($aInstance['access_token']) || empty($aInstance['access_token_secret']) || empty($aInstance['access_token']) )
            {
                esc_html_e('You haven\'t configured your twitter api', 'wiloke');
            }else{
                require_once plugin_dir_path(__FILE__).'twitter/twitteroauth.php';

                $initTWitter = new TwitterOAuth($aInstance['consumer_key'], $aInstance['consumer_secret'], $aInstance['access_token'], $aInstance['access_token_secret'], $aInstance['cache_interval']);
                $initTWitter->ssl_verifypeer = true;

                $tweets = $initTWitter->get('statuses/user_timeline', array('screen_name' => $aInstance['username'], 'include_rts' => 'false', 'count' => $aInstance['limit']));

                if ( !empty($tweets) )
                {
                    $tweets = json_decode($tweets);

                    if( is_array($tweets) )
                    {
                        echo '<ul class="widgettwitter widget-slider">';
                        foreach($tweets as $control)
                        {
                            echo '<li>';
                                echo '<span class="icon"><i class="fa fa-twitter"></i></span>';
                                $status =  preg_replace('/http?s:\/\/([^\s]+)/i', '<a href="http://$1" target="_blank">$1</a>', $control->text);
                                print '<p>' . $status . '</p>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    }

                }else{
                    esc_html_e('There are no tweets yet.', 'wiloke');
                }
            }
        print $atts['after_widget'];
    }
}

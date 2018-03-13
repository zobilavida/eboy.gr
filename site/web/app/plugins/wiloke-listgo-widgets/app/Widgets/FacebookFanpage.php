<?php
class piFacebookLikeBox extends piWilokeWidgets
{
    public $aDef = array( 'title' =>'Fanpage', 'page_url'=>'', 'appid'=>'');
    public function __construct()
    {
        $args = array('classname'=>'pi_facebook_likebox', 'description'=>'');
        parent::__construct("pi_facebook_likebox", parent::PI_PREFIX . 'Facebook', $args);
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args(  $aInstance, $this->aDef );

        $this->pi_text_field( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
        $this->pi_text_field( esc_html__('App ID', 'wiloke'), $this->get_field_id('appid'), $this->get_field_name('appid'), $aInstance['appid']);
        ?>
        <p>
            <code><a target="_blank" href="https://developers.facebook.com/docs/plugins/page-plugin" target="_blank"><?php esc_html_e('Find my App Id', 'wiloke'); ?></a></code>
        </p>
        <?php
        $this->pi_link_field( esc_html__('Facebook Page URL:', 'wiloke'), $this->get_field_id('page_url'), $this->get_field_name('page_url'), $aInstance['page_url'], 'EG. http://www.facebook.com/envato');
    }

    public  function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        foreach ( $new_instance as $key => $val )
        {
            $instance[$key] = strip_tags($val);
        }
        return $instance;
    }

    public function widget( $atts, $aInstance )
    {
        print $atts['before_widget'];

        if( !empty($aInstance['title']) )
        {
            print $atts['before_title'].esc_html($aInstance['title']).$atts['after_title'];
        }
        echo '<div class="box-content">';

        ?>
        <div class="fb-page" data-href="<?php echo esc_url($aInstance['page_url']); ?>" data-hide-cover="false" data-show-facepile="false" data-show-posts="false"></div>
        <div id="fb-root"></div>
        <script>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5&appId=<?php echo esc_attr($aInstance['appid']); ?>";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <?php
        echo '</div>';

        print $atts['after_widget'];
    }
}

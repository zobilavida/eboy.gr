<?php

class piAds extends piWilokeWidgets
{
    public $aDef  =  array('title'=>'Content Box', 'description'=>'', 'image'=>'', 'link'=>'', 'icon'=>'');
    public function __construct()
    {
        $args = array('classname'=>'wiloke_banner widget_banner',  'description'=>esc_html__('Wiloke Content Box', 'wiloke'));
        parent::__construct("wiloke_content_box", esc_html__( parent::PI_PREFIX . 'Content Box', 'wiloke' ),  $args);
    }

    /**
    * Outputs the content of the widget
    * param array $args
    * param array $instance
    */
    public function widget( $atts, $aInstance )
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);
        $arr_attributes = array(
            'target="_blank"'
        );
        $before_banner = '';
        $after_banner = '';

        print $atts['before_widget'];
            if( isset($aInstance['title']) && !empty($aInstance['title']))
            {
                print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
            }

        if (!empty($aInstance['link'])) {
            $arr_attributes[] = 'href="'.esc_url($aInstance['link']).'"';
            $before_banner = '<a '.implode(' ', $arr_attributes).'>';
            $after_banner = '</a>';
        }
        ?>
        <div class="widget_banner-content">
            <?php echo $before_banner;?>
           <img src="<?php echo esc_url($aInstance['image'])?>" alt="<?php echo esc_attr($aInstance['title']);?>">
            <?php echo $after_banner;?>
        </div>
        <?php
        print $atts['after_widget'];
    }


    /**
    * Outputs the options form on admin
    * param array $instance The widget options
    */
    public function form( $aInstance )
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);

        $this->pi_upload_field('Icon', $this->get_field_id('icon'), $this->get_field_id('icon'), $this->get_field_name('icon'), false, $aInstance['icon']);
        $this->pi_text_field('Title', $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
        $this->pi_upload_field('Background Image', $this->get_field_id('bg_img'), $this->get_field_id('bg_img'), $this->get_field_name('bg_img'), false, $aInstance['bg_img']);
        $this->pi_link_field('Link', $this->get_field_id('link'), $this->get_field_name('link'), $aInstance['link']);
    }

    /**
     * Processing widget options on save
     * param array $new_instance The new options
     * param array $old_instance The previous options
     */
    public function update( $aNewInstance, $aOldInstance )
    {
        $aInstance = $aOldInstance;

        foreach ( $aNewInstance as $key => $val )
        {
            if ( $key =='image' || $key == 'link' )
            {
                $aInstance[$key] = esc_url($val);
            }else{
                $aInstance[$key] = strip_tags($val);
            }
        }

        return $aInstance;
    }
}
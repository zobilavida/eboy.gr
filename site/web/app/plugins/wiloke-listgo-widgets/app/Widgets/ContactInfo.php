<?php
/**
 * Create by Minh Minh
 * Team Wiloke
 * URI: wiloke.net
 */

class piContactInfo extends piWilokeWidgets
{
    public $aDef = array('title'=>'Contact Us', 'description'=>'', 'location'=>'', 'phone'=>'', 'email'=>'');
    public function __construct() {
        parent::__construct('wiloke_contactinfo', parent::PI_PREFIX . 'Contact Info', array('class'=>'pi_contactinfo'));
    }

    public function form($aInstance) {
        $aInstance = wp_parse_args($aInstance, $this->aDef);

        $this->pi_text_field('Title', $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
        $this->pi_textarea_field('Description', $this->get_field_id('description'), $this->get_field_name('description'), $aInstance['description']);
        $this->pi_text_field('Location', $this->get_field_id('location'), $this->get_field_name('location'), $aInstance['location']);
        $this->pi_text_field('Email', $this->get_field_id('email'), $this->get_field_name('email'), $aInstance['email']);
        $this->pi_text_field('Phone', $this->get_field_id('phone'), $this->get_field_name('phone'), $aInstance['phone']);
    }

    public function update($aNewinstance, $aOldinstance) {
        $aInstance = $aOldinstance;
        foreach ( $aNewinstance as $key => $val ) {
            if ( $key == 'email' ) {
                $aInstance[$key] = sanitize_email($val);
            }
            else {
                $aInstance[$key] = strip_tags($val);
            }
        }
        return $aInstance;
    }

    public function widget($atts, $aInstance) {
        $aInstance = wp_parse_args($aInstance, $this->aDef);

        print $atts['before_widget'];
            if (!empty($aInstance['title'])) {
                print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
            }
            if (!empty($aInstance['description'])) {
                echo '<div class="text-italic">';
                print '<p>'.wp_unslash($aInstance['description']).'</p>';
                echo '</div>';
            }
            if (!empty($aInstance['location'])) {
                echo '<div class="item-icon-left">';
                    echo '<i class="fa fa-map-marker"></i>';
                    print '<p>'.wp_unslash($aInstance['location']).'</p>';
                echo '</div>';
            }
            if (!empty($aInstance['email'])) {
                echo '<div class="item-icon-left">';
                    echo '<i class="fa fa-envelope"></i>';
                    print '<p><a href="mailto:'.esc_attr($aInstance['email']).'">'.wp_unslash($aInstance['email']).'</a></p>';
                echo '</div>';
            }
            if (!empty($aInstance['phone'])){
                echo '<div class="item-icon-left">';
                echo '<i class="fa fa-phone"></i>';
                print '<p>'.wp_unslash($aInstance['phone']).'</p>';
                echo '</div>';
            }
        print $atts['after_widget'];
    }
}
?>
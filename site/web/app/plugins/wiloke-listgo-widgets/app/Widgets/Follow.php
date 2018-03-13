<?php
/**
 * Create by Minh Minh
 * Team Wiloke
 * URI: wiloke.net
 */


class piFollow extends piWilokeWidgets
{
  public $aDef = array('title'=>'', 'description'=>'');
  public $follow = array(
    'facebook'      =>'https://www.facebook.com/wilokewp',
    'twitter'       =>'https://twitter.com/wilokethemes',
    'pinterest'     =>'',
    'dribbble'      =>'',
    'google_plus'   =>'',
    'instagram'     =>'',
    'vk'            =>'',
    'behance'       =>'',
    'vimeo'         =>'',
    'youtube'       =>'',
    'behance'       =>'',
    'bloglovin'     =>'',
    'linkedin'      =>'',
    'tumblr'        =>'',
    'digg'          =>'',
    'reddit'        =>'',
    'deviantart'    =>''
  );
  public $rest_follow = array(
    'facebook'      => array('fa fa-facebook', 'Facebook'),
    'twitter'       => array('fa fa-twitter', 'Twitter'),
    'google_plus'   => array('fa fa-google-plus', 'Google+'),
    'instagram'     => array('fa fa-instagram', 'Instagram'),
    'vk'            => array('fa fa-vk', 'Vk'),
    'youtube'       => array('fa fa-youtube-play', 'Youtube'),
    'vimeo'         => array('fa fa-vimeo-square', 'Vimeo'),
    'pinterest'     => array('fa fa-pinterest', 'Pinterest'),
    'dribbble'      => array('fa fa-dribbble', 'Dribbble'),
    'behance'       => array('fa fa-behance', 'Behance'),
    'bloglovin'     => array('fa fa-heart', 'Bloglovin'),
    'linkedin'      => array('fa fa-linkedin', 'Linkedin'),
    'tumblr'        => array('fa fa-tumblr', 'Tumblr'),
    'reddit'        => array('fa fa-reddit', 'Digg'),
    'deviantart'    => array('fa fa-deviantart', 'Deviantart'),
  );
  public function __construct()
  {
    parent::__construct('wiloke_follow', parent::PI_PREFIX . 'Follow', array('class'=>'pi_follow wiloke_follow'));
  }

  public function form($aInstance)
  {


    $this->aDef = array_merge($this->aDef, $this->follow);
    $aInstance  = wp_parse_args($aInstance, $this->aDef);
    $this->pi_text_field( 'Title', $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
    $this->pi_textarea_field( 'Description', $this->get_field_id('description'), $this->get_field_name('description'), $aInstance['description']);

    foreach ( $this->rest_follow as $key => $aInfo ) {
      $this->pi_text_field( $aInfo[1], $this->get_field_id($key), $this->get_field_name($key), $aInstance[$key], '', true);
    }
  }

  public function update($aNewinstance, $aOldinstance)
  {
    $aInstance = $aOldinstance;
    foreach ( $aNewinstance as $key => $val )
    {
      if ( $key == 'title' || $key == 'description' )
      {
        $aInstance[$key] = strip_tags($val);
      }else{
        $aInstance[$key] = esc_url($val);
      }
    }

    return $aInstance;
  }

  public function widget($atts, $aInstance)
  {
    $this->aDef = array_merge($this->aDef, $this->follow);
    $aInstance  = wp_parse_args($aInstance, $this->aDef);

    print $atts['before_widget'];
    if ( !empty($aInstance['title']) )
    {
      print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
    }

    if ( !empty($aInstance['description']) )
    {
      echo '<div class="text-italic">';
      print '<p>'.wp_unslash($aInstance['description']).'</p>';
      echo '</div>';
    }
    echo '<div class="pi-social-square">';
    foreach ( $this->rest_follow as $key => $aInfo )
    {
      if ( !empty($aInstance[$key]) )
      {
        echo '<a target="_blank" href="'.esc_url($aInstance[$key]).'"><i class="'.esc_attr($aInfo[0]).'"></i></a>';
      }
    }
    echo '</div>';
    print $atts['after_widget'];
  }
}
?>
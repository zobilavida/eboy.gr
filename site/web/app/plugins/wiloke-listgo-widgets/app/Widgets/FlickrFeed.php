<?php
/**
 * Create by Minh Minh
 * Team Wiloke
 * URI: wiloke.net
 */

class piFlickrFeed extends piWilokeWidgets
{
    public $aDef = array( 'title' =>'Flickr', 'flickr_id'=>'104472278@N03', 'number_of_photos' => 6, 'flickr_display' => 'latest' );
    public function __construct()
    {
        $args = array('classname'=>'pi_flickr_feed widget_flickr widget_instagram widget_wiloke_instagram widget_photo', 'description'=>'');
        parent::__construct("wiloke_flickrfeed", parent::PI_PREFIX . 'Flickr Feed ', $args);
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args( $aInstance, $this->aDef );

        $this->pi_text_field( __('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
        $this->pi_text_field( __('Flickr ID (*)', 'wiloke'), $this->get_field_id('flickr_id'), $this->get_field_name('flickr_id'), $aInstance['flickr_id'], ' Find Your ID at( <a target="_blank" href="http://www.idgettr.com">idGettr</a> )');
        $this->pi_text_field( __('Number Of Photos', 'wiloke'), $this->get_field_id('number_of_photos'), $this->get_field_name('number_of_photos'), $aInstance['number_of_photos']);
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

    public function widget( $atts, $aInstance ) {

        $aInstance = wp_parse_args($aInstance, $this->aDef);

        print $atts['before_widget'];

        if ( !empty($aInstance['title']) ) {
            print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
        } ?>

        <div class="widget__photo widget__photo--grid widget-grid">
            <?php if ( empty($aInstance['flickr_id']) ) {
                esc_html_e('Please config your flick id', 'pi');
            } else {
                print ($this->pi_parse_flickr_feed($aInstance['flickr_id'], $aInstance['number_of_photos']));
            } ?>

        </div>

        <?php print $atts['after_widget'];
    }

    public function pi_parse_atts($s,$attrname)
    {
        preg_match_all('#\s*('.$attrname.')\s*=\s*["|\']([^"\']*)["|\']\s*#i', $s, $x);
        if (count($x)>=3)
        {
            return $x[2][0];
        }else{
            return '';
        }
    }

    public function pi_parse_flickr_feed($id,$n) {

        $url = "https://api.flickr.com/services/feeds/photos_public.gne?id={$id}&lang=it-it&format=rss_200&amp;set=".$n;
        
        $response = wp_remote_get( esc_url_raw( $url ),  array( 'decompress' => false ) );

        if ( !is_wp_error( $response ) ) {

            $response = wp_remote_retrieve_body($response);

            preg_match_all('#<item>(.*)</item>#Us', $response, $items);

            $output = '';

            for($i=0;$i<count($items[1]);$i++) {

                if( $i>=$n ) { 
                    return $output;
                }

                $item = $items[1][$i];
                preg_match_all('#<link>(.*)</link>#Us', $item, $temp);
                $link = $temp[1][0];
                preg_match_all('#<media:thumbnail([^>]*)>#Us', $item, $temp);
                $thumb = $this->pi_parse_atts($temp[0][0],"url");

                $output.='<div class="item">';
                $output .='<a href="'.esc_url($link).'" class="img bg-scroll" target="_blank" title="Flickr Feed" style="background-image: url('. esc_url($thumb) .')"><img src="'.esc_url($thumb).'" alt="'.esc_html__('Flick', 'pi').'" /></a>';
                $output .='</div>';
            }

        } else {

            return esc_html__('Please try with another flickr id', 'pi');
        }

        return $output;
    }
}

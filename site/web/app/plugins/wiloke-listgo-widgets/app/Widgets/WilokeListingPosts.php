<?php
use WilokeWidget\Supports\Helpers;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;

class WilokeListingPosts extends WP_Widget
{
    public $aDef = array('title'=>'', 'number_of_posts'=>4, 'orderby'=>'post_date', 'cache'=>0, 'image_size'=>'thumbnail');
    public $defFeatureImg = '';
    public function __construct()
    {
        parent::__construct('wiloke_postslisting', WILOKE_WIDGET_PREFIX . esc_html__( 'List of Listings', 'wiloke'), array('classname'=>'widget_postslisting widget_listings'));
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);
        Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
	    Helpers::textField( esc_html__('Number of posts', 'wiloke'), $this->get_field_id('number_of_posts'), $this->get_field_name('number_of_posts'), $aInstance['number_of_posts']);
	    Helpers::selectField( esc_html__('Order By', 'wiloke'), $this->get_field_id('orderby'), $this->get_field_name('orderby'), array('post_date'=>esc_html__('Latest Listings', 'wiloke'), 'review_count'=>esc_html__('Review Count', 'wiloke'), 'rating_score'=>esc_html__('Rating Score', 'wiloke'), 'menu_order' => esc_html__('Featured Listings', 'wiloke'), 'rand'=>esc_html__('Random', 'wiloke')), $aInstance['orderby']);
	    Helpers::textField( esc_html__('Image Size', 'wiloke'), $this->get_field_id('image_size'), $this->get_field_name('image_size'), $aInstance['image_size']);
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
        global $wpdb, $wiloke;
        $tblName = $wpdb->prefix . 'posts';

        if ( !class_exists('WilokeListGoFunctionality\AlterTable\AlterTableReviews') ) {
            return false;
        }

	    $tblReview = $wpdb->prefix . AlterTableReviews::$tblName;

	    if ( $aInstance['orderby'] !== 'rand' ){
		    $aListings = wiloke_listgo_widget_get_cache($atts);
        }

        if ( !empty($aListings) ){
	        $aListings = json_decode($aListings);
        }else{
	        switch ($aInstance['orderby']) {
		        case 'review_count':
			        $sql = $wpdb->prepare(
				        "SELECT $tblName.ID, $tblName.post_author, $tblName.post_date, $tblName.post_type, $tblName.post_title, $tblName.comment_count, COALESCE(AVG($tblReview.rating), 0) as average_rating_score, COUNT($tblReview.rating) AS review_count  FROM $tblName LEFT JOIN $tblReview ON ($tblName.ID=$tblReview.post_ID)  WHERE $tblName.post_type=%s AND $tblName.post_status=%s GROUP BY $tblName.ID ORDER BY review_count DESC LIMIT {$aInstance['number_of_posts']}",
				        'listing', 'publish'
			        );
			        break;

		        case 'rating_score':
			        $sql = $wpdb->prepare(
				        "SELECT $tblName.ID, $tblName.post_author, $tblName.post_date, $tblName.post_type, $tblName.post_title, $tblName.comment_count, COALESCE(AVG($tblReview.rating), 0) as average_rating_score FROM $tblName LEFT JOIN $tblReview ON ($tblName.ID=$tblReview.post_ID)  WHERE $tblName.post_type=%s AND $tblName.post_status=%s GROUP BY $tblName.ID ORDER BY average_rating_score DESC LIMIT {$aInstance['number_of_posts']}",
				        'listing', 'publish'
			        );
			        break;

		        case 'rand':
			        $sql = $wpdb->prepare(
				        "SELECT $tblName.ID, $tblName.post_author, $tblName.post_date, $tblName.post_type, $tblName.post_title, $tblName.comment_count, COALESCE(AVG($tblReview.rating), 0) as average_rating_score FROM $tblName LEFT JOIN $tblReview ON ($tblName.ID=$tblReview.post_ID)  WHERE $tblName.post_type=%s AND $tblName.post_status=%s GROUP BY $tblName.ID ORDER BY rand() DESC LIMIT {$aInstance['number_of_posts']}",
				        'listing', 'publish'
			        );
			        break;

		        default:
			        $sql = $wpdb->prepare(
				        "SELECT $tblName.ID, $tblName.menu_order, $tblName.post_author, $tblName.post_date, $tblName.post_type, $tblName.post_title, $tblName.comment_count, COALESCE(AVG($tblReview.rating), 0) as average_rating_score FROM $tblName LEFT JOIN $tblReview ON ($tblName.ID=$tblReview.post_ID) WHERE post_type=%s AND post_status=%s GROUP BY $tblName.ID ORDER BY $tblName.{$aInstance['orderby']} DESC LIMIT {$aInstance['number_of_posts']}",
				        'listing', 'publish'
			        );
			        break;
	        }

	        $aListings = $wpdb->get_results($sql);
	        wiloke_listgo_widget_set_cache($atts, $aListings);
        }

        if ( empty($aListings) ) {
            return '';
        }

        echo $atts['before_widget'];
            if ( !empty($aInstance['title']) ) {
                print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
            }
            echo '<ul>';
                foreach ( $aListings as $oListing ) :
                    $aLocation = Wiloke::getPostTerms($oListing, 'listing_location');
                ?>
                    <li>
                        <a href="<?php echo esc_url(get_permalink($oListing->ID)); ?>">
                            <img class="lazy" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-src="<?php echo esc_url($this->getListingFeatureImage($oListing, $aInstance)); ?>" alt="<?php echo esc_attr($oListing->post_title); ?>">
                            <div class="overflow-hidden">
                                <?php  if ( !empty($aLocation) ) : ?>
                                <span class="cat"><?php echo esc_html($aLocation[0]->name); ?></span>
                                <?php endif; ?>

                                <h4><?php echo esc_html($oListing->post_title); ?></h4>
                                <span class="rating__star">
                                    <?php
                                        for ( $i = 1; $i <=5; $i++ ) :
                                    ?>
                                            <i class="<?php echo esc_attr(WilokePublic::getStarClass($oListing->average_rating_score, $i)); ?>"></i>
                                    <?php endfor; ?>
                                </span>
                            </div>
                        </a>
                    </li>
                <?php
                endforeach;
            echo '</ul>';
	    echo $atts['after_widget'];
    }

    public function getListingFeatureImage($oListing, $aInstance){
        global $wiloke;
        if ( has_post_thumbnail($oListing->ID) ) {
	        $url = get_the_post_thumbnail_url( $oListing->ID, $aInstance['image_size'] );
        }

        if ( !isset($url) || !$url ){
            if ( empty($this->defFeatureImg) ){
                $this->defFeatureImg = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], $aInstance['image_size']);
            }
            return $this->defFeatureImg;
        }else{
            return $url;
        }
    }
}
<?php
/**
 * Register Taxonomy
 * @since 1.0
 */
namespace WilokeListGoFunctionality\Register;

class RegisterTaxonomy implements RegisterInterface{
    public function __construct()
    {
        add_action('init', array($this, 'register'));
    }

    public function register()
    {
        global $WilokeListGoFunctionalityApp, $wiloke;
        if ( isset($WilokeListGoFunctionalityApp['taxonomies']) ){
            foreach ( $WilokeListGoFunctionalityApp['taxonomies'] as $tax => $aArgs ){
            	$optionName = 'custom_'.$tax.'_slug';
	            if ( isset($wiloke->aThemeOptions[$optionName]) && !empty($wiloke->aThemeOptions[$optionName]) ){
		            $aArgs['rewrite']['slug'] = trim(stripslashes($wiloke->aThemeOptions[$optionName]));
	            }

	            switch ($tax){
		            case 'listing_location':
			            $aArgs['labels'] = array(
				            'name'              => _x( 'Listing Locations', 'taxonomy general name', 'wiloke' ),
				            'singular_name'     => _x( 'Listing Location', 'taxonomy singular name', 'wiloke' ),
				            'search_items'      => esc_html__( 'Search Listing Locations', 'wiloke' ),
				            'all_items'         => esc_html__( 'All Listing Locations', 'wiloke' ),
				            'parent_item'       => esc_html__( 'Parent Listing Location', 'wiloke' ),
				            'parent_item_colon' => esc_html__( 'Parent Listing Location:', 'wiloke' ),
				            'edit_item'         => esc_html__( 'Edit Listing Location', 'wiloke' ),
				            'update_item'       => esc_html__( 'Update Listing Location', 'wiloke' ),
				            'add_new_item'      => esc_html__( 'Add New Listing Location', 'wiloke' ),
				            'new_item_name'     => esc_html__( 'New Listing Location Name', 'wiloke' ),
				            'menu_name'         => esc_html__( 'Listing Locations', 'wiloke' )
			            );
		            	break;
		            case  'listing_cat':
		            	$aArgs['labels'] = array(
				            'name'              => _x( 'Listing Categories', 'taxonomy general name', 'wiloke' ),
				            'singular_name'     => _x( 'Listing Category', 'taxonomy singular name', 'wiloke' ),
				            'search_items'      => esc_html__( 'Search Listing Categories', 'wiloke' ),
				            'all_items'         => esc_html__( 'All Listing Categories', 'wiloke' ),
				            'parent_item'       => esc_html__( 'Parent Listing Category', 'wiloke' ),
				            'parent_item_colon' => esc_html__( 'Parent Listing Category:', 'wiloke' ),
				            'edit_item'         => esc_html__( 'Edit Listing Category', 'wiloke' ),
				            'update_item'       => esc_html__( 'Update Listing Category', 'wiloke' ),
				            'add_new_item'      => esc_html__( 'Add New Listing Category', 'wiloke' ),
				            'new_item_name'     => esc_html__( 'New Listing Category Name', 'wiloke' ),
				            'menu_name'         => esc_html__( 'Listing Categories', 'wiloke' ),
			            );
		            	break;
		            case  'listing_tag':
			            $aArgs['labels'] = array(
				            'name'              => _x( 'Listing Tags', 'taxonomy general name', 'wiloke' ),
				            'singular_name'     => _x( 'Listing Tag', 'taxonomy singular name', 'wiloke' ),
				            'search_items'      => esc_html__( 'Search Listing Tags', 'wiloke' ),
				            'all_items'         => esc_html__( 'All Listing Tags', 'wiloke' ),
				            'parent_item'       => esc_html__( 'Parent Listing Tag', 'wiloke' ),
				            'parent_item_colon' => esc_html__( 'Parent Listing Tag:', 'wiloke' ),
				            'edit_item'         => esc_html__( 'Edit Listing Tag', 'wiloke' ),
				            'update_item'       => esc_html__( 'Update Listing Tag', 'wiloke' ),
				            'add_new_item'      => esc_html__( 'Add New Listing Tag', 'wiloke' ),
				            'new_item_name'     => esc_html__( 'New Listing Tag Name', 'wiloke' ),
				            'menu_name'         => esc_html__( 'Listing Tags', 'wiloke' )
			            );
			            break;
	            }

                register_taxonomy($tax, $aArgs['post_types'], $aArgs);
            }
        }
    }
}
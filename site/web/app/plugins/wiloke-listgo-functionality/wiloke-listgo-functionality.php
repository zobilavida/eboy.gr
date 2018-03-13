<?php
/*
 * Plugin Name: Wiloke Listgo Functionality
 * Plugin URI: https://wiloke.com
 * Author: Wiloke
 * Author URI: https://wiloke.com
 * Version: 1.3.4
 * Description: This plugin is required with List Go
 * Text Domain: wiloke
 * Domain Path: /languages/
 */

if ( !defined('ABSPATH') ){
    die();
}

define('WILOKE_LISTGO_FC_VERSION', '1.3.4');
define('WILOKE_LISTGO_FUNC_PATH', plugin_dir_path(__FILE__));
define('WILOKE_LISTGO_FUNC_URL', plugin_dir_url(__FILE__));

add_action( 'plugins_loaded', 'wiloke_listgo_functionality_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wiloke_listgo_functionality_load_textdomain() {
	load_plugin_textdomain( 'wiloke', false, basename(dirname(__FILE__)) . '/languages' );
}

add_action('wp_enqueue_scripts', 'wilokeListgoFunctionalityScripts');
add_action('admin_enqueue_scripts', 'wilokeListgoFunctionalityScripts');

function wilokeListgoFunctionalityScripts(){
	$url = plugin_dir_url(__FILE__);
	wp_localize_script('jquery-migrate', 'WILOKE_LISTGO_FUNCTIONALITY', array('url'=>$url));
}

function wiloke_error_while_adding_listing($line, $file){
	if ( current_user_can('publish_posts') ){
		wp_die( sprintf(__('We found an error on the line %s in the %s of wiloke-listgo-functionality plugin. Please contact us at sale@wiloke.com or piratesmorefun@gmail.com to report this issue', 'wiloke'), $line, $file) );
	}else{
		wp_die( esc_html__('Something went wrong', 'wiloke') );
	}
}

$oTheme = wp_get_theme();
$themeName = strtolower($oTheme->name);
if ( strpos($themeName, 'listgo') === false ){
	return false;
}

require 'vendor/autoload.php';
$GLOBALS['WilokeListGoFunctionalityApp'] = require 'config/app.php';

use WilokeListGoFunctionality\Register\RegisterInterface;
use WilokeListGoFunctionality\Register\RegisterPostType;
use WilokeListGoFunctionality\Register\RegisterTaxonomy;
use WilokeListGoFunctionality\Register\RegisterNavMenu;
use WilokeListGoFunctionality\Register\RegisterReport;
use WilokeListGoFunctionality\Register\RegisterFollow;
use WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Register\RegisterBadges;
use WilokeListGoFunctionality\Register\RegisterWelcome;
use WilokeListGoFunctionality\Register\RegisterClaim;
use WilokeListGoFunctionality\Register\RegisterEventPricing;

register_activation_hook( __FILE__, array('WilokeListGoFunctionality\Register\RegisterWilokeSubmission', 'setDefaultSubmissionPages') );

use WilokeListGoFunctionality\AlterTable\AlterTableInterface;
use WilokeListGoFunctionality\AlterTable\AlterTableComments;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;
use WilokeListGoFunctionality\AlterTable\AlterTableFavirote;
use WilokeListGoFunctionality\AlterTable\AlterTableReport;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentEventRelationship;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTableNotifications;
use WilokeListGoFunctionality\AlterTable\AlterTableBusinessHours;
use WilokeListGoFunctionality\AlterTable\AlterTablePriceSegment;
use WilokeListGoFunctionality\AlterTable\AlterTableGeoPosition;
use WilokeListGoFunctionality\AlterTable\AlterTablePayPalErrorLog;

use WilokeListGoFunctionality\Submit\AddListing;
use WilokeListGoFunctionality\Submit\User;

use WilokeListGoFunctionality\Frontend\Notification as WilokeFrontendNotification;
use WilokeListGoFunctionality\Frontend\FrontendBusinessHours as WilokeBusinessHours;
use WilokeListGoFunctionality\Frontend\FrontendPriceSegment as WilokePriceSegment;
use WilokeListGoFunctionality\Frontend\FrontendRating as WilokeFrontendRating;
use WilokeListGoFunctionality\Frontend\FrontendFollow as WilokeFrontendFollow;
use WilokeListGoFunctionality\Frontend\FrontendFavorites as WilokeFrontendFavorites;
use WilokeListGoFunctionality\Frontend\FrontendEvents as WilokeFrontendEvents;
use WilokeListGoFunctionality\Frontend\FrontendClaimListing as WilokeFrontendClaim;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
use WilokeListGoFunctionality\Frontend\FrontendManageSingleListing as WilokeFrontendManageSingleListing;
use WilokeListGoFunctionality\Frontend\FrontendTwoCheckout as WilokeFrontendTwoCheckout;

use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;

/*
 * Model
 */
use WilokeListGoFunctionality\Model\GeoPosition;
use WilokeListGoFunctionality\Model\Listing as WilokeModelListing;
use WilokeListGoFunctionality\Model\Review as WilokeReview;

/*
 * Payment system
 * @since 1.0
 */
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Payment\PayPal as WilokePayPal;
use WilokeListGoFunctionality\Payment\TwoCheckout as WilokeTwoCheckout;
use WilokeListGoFunctionality\Payment\CheckPayment;
use WilokeListGoFunctionality\Payment\CheckListingsStatus;
use WilokeListGoFunctionality\Payment\CheckEventStatus;
use WilokeListGoFunctionality\Payment\EventPayment as WilokeEventPayment;

/**
 * Add Rating Column To Comment Table
 *
 * @since 1.0
 * @author Wiloke
 */
new AlterTableComments;
new AlterTableReviews;
new AlterTableFavirote;
new AlterTableReport;
new AltertableFollowing;
new AlterTablePaymentHistory;
new AlterTablePaymentRelationships;
new AlterTablePaymentEventRelationship;
new AlterTablePackageStatus;
new AlterTableNotifications;
new AlterTableBusinessHours;
new AlterTablePriceSegment;
new AlterTablePayPalErrorLog;
new AlterTableGeoPosition;

new AddListing;
register_activation_hook( __FILE__, array('WilokeListGoFunctionality\Submit\AddListing', 'afterPluginActivation') );

new User;
register_activation_hook( __FILE__, array('WilokeListGoFunctionality\Submit\User', 'addRoles') );
$checkListingStatus = new CheckListingsStatus();
register_activation_hook(__FILE__, array($checkListingStatus, 'checkingListingScheduled'));
register_activation_hook(__FILE__, array($checkListingStatus, 'deactivateCheckingListingScheduled'));
new WilokeEventPayment;

/**
 * Register Post Type and Register Taxonomy
 *
 * @since 1.0
 * @author Wiloke
 */
new RegisterPostType;
new RegisterTaxonomy;
new RegisterReport;
new RegisterFollow;
new RegisterPricingSettings;
new RegisterWilokeSubmission;
new RegisterBadges;
new RegisterWelcome;
new RegisterClaim;
new RegisterEventPricing;
//new RegisterNavMenu;
/**
 * Public
 *
 * @since 1.0
 * @author Wiloke
 */
//new HandleFrontEnd;
new WilokePayment;
new WilokePayPal;
$GLOBALS['WilokeTwoCheckout']  = new WilokeTwoCheckout;

new WilokeFrontendNotification;
$oBusinessHours     = new WilokeBusinessHours;
$oPriceSegment      = new WilokePriceSegment;
$oFrontPageRating   = new WilokeFrontendRating;
new WilokeFrontendClaim;
new WilokeFrontendFavorites();
new WilokeFrontendListingManagement();
new WilokeFrontendManageSingleListing();
new WilokeFrontendTwoCheckout();
new WilokeFrontendEvents;
new CheckEventStatus;
new WilokeCustomerPlan;
/*
 * Shortcodes
 * @since 1.0
 */
use WilokeListGoFunctionality\Shortcodes\Shortcodes as ListGoShortcodes;
new ListGoShortcodes;

new GeoPosition;
new WilokeReview;
$modelListing = new WilokeModelListing;
$modelListing->init();

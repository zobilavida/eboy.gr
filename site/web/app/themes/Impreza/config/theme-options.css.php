<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs theme options' generated styleshets
 *
 * @action Before the template: us_before_template:config/theme-options.css
 * @action After the template: us_after_template:config/theme-options.css
 */

global $us_template_directory_uri;
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$prefixes = array( 'heading', 'body', 'menu' );
$font_families = array();
$default_font_weights = array_fill_keys( $prefixes, 400 );
foreach ( $prefixes as $prefix ) {
	$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
	if ( $font[0] == 'none' ) {
		// Use the default font
		$font_families[ $prefix ] = '';
	} elseif ( strpos( $font[0], ',' ) === FALSE ) {
		// Use some specific font from Google Fonts
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		// The first active font-weight will be used for "normal" weight
		$default_font_weights[ $prefix ] = intval( $font[1] );
		$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
		$font_families[ $prefix ] = 'font-family: "' . $font[0] . '", ' . $fallback_font_family . ";\n";
	} else {
		// Web-safe font combination
		$font_families[ $prefix ] = 'font-family: ' . $font[0] . ";\n";
	}
}

?>

/* CSS paths need to be absolute
   =============================================================================================================================== */
@font-face {
	font-family: 'FontAwesome';
	src: url('<?php echo $us_template_directory_uri ?>/framework/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),
	url('<?php echo $us_template_directory_uri ?>/framework/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff');
	font-weight: normal;
	font-style: normal;
	}
.style_phone6-1 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-black-real.png);
	}
.style_phone6-2 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-white-real.png);
	}
.style_phone6-3 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-black-flat.png);
	}
.style_phone6-4 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-white-flat.png);
	}
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.wc-credit-card-form-card-number.visa {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/visa.svg);
	}
.wc-credit-card-form-card-number.mastercard {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/mastercard.svg);
	}
.wc-credit-card-form-card-number.discover {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/discover.svg);
	}
.wc-credit-card-form-card-number.amex {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/amex.svg);
	}
.wc-credit-card-form-card-number.maestro {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/maestro.svg);
	}
.wc-credit-card-form-card-number.jcb {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/jcb.svg);
	}
.wc-credit-card-form-card-number.dinersclub {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/diners.svg);
	}
<?php } ?>



/* Typography
   =============================================================================================================================== */
html,
.w-nav .widget {
	<?php echo $font_families['body'] ?>
	font-size: <?php echo us_get_option( 'body_fontsize' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight' ) ?>px;
	font-weight: <?php echo $default_font_weights['body'] ?>;
	}

.w-text.font_main_menu,
.w-nav-list.level_1 {
	<?php echo $font_families['menu'] ?>
	font-weight: <?php echo $default_font_weights['menu'] ?>;
	}

h1, h2, h3, h4, h5, h6,
.w-text.font_heading,
.w-blog-post.format-quote blockquote,
.w-counter-number,
.w-pricing-item-price,
.w-tabs-item-title,
.stats-block .stats-desc .stats-number {
	<?php echo $font_families['heading'] ?>
	font-weight: <?php echo $default_font_weights['heading'] ?>;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h1_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h1_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h1_transform' ) ) AND in_array( 'italic', us_get_option( 'h1_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h1_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h1_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h2_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h2_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h2_transform' ) ) AND in_array( 'italic', us_get_option( 'h2_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h2_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h2_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h3_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h3_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h3_transform' ) ) AND in_array( 'italic', us_get_option( 'h3_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h3_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h3_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h4,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2,
<?php } ?>
.widgettitle,
.comment-reply-title {
	font-size: <?php echo us_get_option( 'h4_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h4_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h4_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h4_transform' ) ) AND in_array( 'italic', us_get_option( 'h4_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h4_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h4_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h5 {
	font-size: <?php echo us_get_option( 'h5_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h5_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h5_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h5_transform' ) ) AND in_array( 'italic', us_get_option( 'h5_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h5_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h5_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h6_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h6_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h6_transform' ) ) AND in_array( 'italic', us_get_option( 'h6_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h6_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h6_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
@media (max-width: 767px) {
html {
	font-size: <?php echo us_get_option( 'body_fontsize_mobile' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight_mobile' ) ?>px;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize_mobile' ) ?>px;
	}
h1.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h1_fontsize_mobile' ) ?>px !important;
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize_mobile' ) ?>px;
	}
h2.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h2_fontsize_mobile' ) ?>px !important;
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize_mobile' ) ?>px;
	}
h3.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h3_fontsize_mobile' ) ?>px !important;
	}
h4,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2,
<?php } ?>
.widgettitle,
.comment-reply-title {
	font-size: <?php echo us_get_option( 'h4_fontsize_mobile' ) ?>px;
	}
h4.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h4_fontsize_mobile' ) ?>px !important;
	}
h5 {
	font-size: <?php echo us_get_option( 'h5_fontsize_mobile' ) ?>px;
	}
h5.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h5_fontsize_mobile' ) ?>px !important;
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize_mobile' ) ?>px;
	}
h6.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h6_fontsize_mobile' ) ?>px !important;
	}
}



/* Layout
   =============================================================================================================================== */
<?php if ( us_get_option( 'body_bg_image' ) AND $body_bg_image = usof_get_image_src( us_get_option( 'body_bg_image' ) ) ): ?>
body {
	background-image: url(<?php echo $body_bg_image[0] ?>);
	background-attachment: <?php echo ( us_get_option( 'body_bg_image_attachment' ) ) ? 'scroll' : 'fixed'; ?>;
	background-position: <?php echo us_get_option( 'body_bg_image_position' ) ?>;
	background-repeat: <?php echo us_get_option( 'body_bg_image_repeat' ) ?>;
	background-size: <?php echo us_get_option( 'body_bg_image_size' ) ?>;
}
<?php endif; ?>
body,
.header_hor .l-header.pos_fixed {
	min-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.l-canvas.type_boxed,
.l-canvas.type_boxed .l-subheader,
.l-canvas.type_boxed .l-section.type_sticky,
.l-canvas.type_boxed ~ .l-footer {
	max-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.header_hor .l-subheader-h,
.l-titlebar-h,
.l-main-h,
.l-section-h,
.w-tabs-section-content-h,
.w-blog-post-body {
	max-width: <?php echo us_get_option( 'site_content_width' ) ?>px;
	}
	
/* Hide carousel arrows before they cut by screen edges */
@media (max-width: <?php echo us_get_option( 'site_content_width' ) + 150 ?>px) {
.l-section:not(.width_full) .owl-nav {
	display: none;
	}
}
@media (max-width: <?php echo us_get_option( 'site_content_width' ) + 200 ?>px) {
.l-section:not(.width_full) .w-blog .owl-nav {
	display: none;
	}
}

.l-sidebar {
	width: <?php echo us_get_option( 'sidebar_width' ) ?>%;
	}
.l-content {
	width: <?php echo us_get_option( 'content_width' ) ?>%;
	}
	
/* Columns Stacking Width */
@media (max-width: <?php echo us_get_option( 'columns_stacking_width' ) - 1 ?>px) {
.g-cols > div:not([class*=" vc_col-"]) {
	clear: both;
	float: none;
	width: 100%;
	margin: 0 0 2rem;
	}
.g-cols.type_boxes > div,
.g-cols > div:last-child,
.g-cols > div.has-fill {
	margin-bottom: 0;
	}
.vc_wp_custommenu.layout_hor,
.align_center_xs,
.align_center_xs .w-socials {
	text-align: center;
	}
}

/* Portfolio Responsive Behavior */
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_1_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_1_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_1_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_1_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_1_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_2_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_2_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_2_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_2_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_2_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_3_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_3_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_3_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_3_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_3_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}

/* Blog Responsive Behavior */
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_1_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_1_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_1_cols' ) ?>%;
	}
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_2_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_2_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_2_cols' ) ?>%;
	}
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_3_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_3_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_3_cols' ) ?>%;
	}
<?php } ?>
}



/* Buttons
   =============================================================================================================================== */
.w-btn,
.button,
.l-body .cl-btn,
.l-body .ubtn,
.l-body .ultb3-btn,
.l-body .btn-modal,
.l-body .flip-box-wrap .flip_link a,
.rev_slider a.w-btn, /* fix buttons shadow in Rev Slider */
.tribe-events-button,
input[type="button"],
input[type="submit"] {
	<?php if ( us_get_option( 'button_font' ) == 'heading' ) { echo $font_families['heading']; } ?>
	<?php if ( us_get_option( 'button_font' ) == 'menu' ) { echo $font_families['menu']; } ?>
	<?php if ( is_array( us_get_option( 'button_text_style' ) ) AND in_array( 'italic', us_get_option( 'button_text_style' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'button_text_style' ) ) AND in_array( 'uppercase', us_get_option( 'button_text_style' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	font-size: <?php echo us_get_option( 'button_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'button_fontweight' ) ?>;
	line-height: <?php echo us_get_option( 'button_height' ) ?>;
    padding: 0 <?php echo us_get_option( 'button_width' ) ?>em;
	border-radius: <?php echo us_get_option( 'button_border_radius' ) ?>em;
	letter-spacing: <?php echo us_get_option( 'button_letterspacing' ) ?>em;
	box-shadow: 0 <?php echo us_get_option( 'button_shadow' ) / 2 ?>em <?php echo us_get_option( 'button_shadow' ) ?>em rgba(0,0,0,0.18);
	}
.w-btn.icon_atleft i {
	left: <?php echo us_get_option( 'button_width' ) ?>em;
	}
.w-btn.icon_atright i {
	right: <?php echo us_get_option( 'button_width' ) ?>em;
	}
<?php if ( us_get_option( 'button_shadow_hover' ) != 0 ): ?>
.no-touch .w-btn:hover,
.no-touch .button:hover,
.no-touch .cl-btn:hover,
.no-touch .ubtn:hover,
.no-touch .ultb3-btn:hover,
.no-touch .btn-modal:hover,
.no-touch .flip-box-wrap .flip_link a:hover,
.no-touch .rev_slider a.w-btn, /* fix buttons shadow in Rev Slider */
.no-touch .tribe-events-button:hover,
.no-touch input[type="button"]:hover,
.no-touch input[type="submit"]:hover {
	box-shadow: 0 <?php echo us_get_option( 'button_shadow_hover' ) / 2 ?>em <?php echo us_get_option( 'button_shadow_hover' ) ?>em rgba(0,0,0,0.2);
	}
<?php endif; ?>
	
/* Back to top Button */
.w-header-show,
.w-toplink {
	background-color: <?php echo us_get_option( 'back_to_top_color' ) ?>;
	}



/* Colors
   =============================================================================================================================== */

body {
	background-color: <?php echo us_get_option( 'color_body_bg' ) ?>;
	-webkit-tap-highlight-color: <?php echo us_hex2rgba( us_get_option( 'color_content_primary' ), 0.2 ) ?>;
	}

/*************************** Header Colors ***************************/

/* Top Header Colors */
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown-list,
.l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_top_bg' ) ?>;
	}
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown.active,
.l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_top_text' ) ?>;
	}
.no-touch .l-subheader.at_top a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_top .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_top_text_hover' ) ?>;
	}

/* Middle Header Colors */
.header_ver .l-header,
.header_hor .l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown-list,
.l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_middle_bg' ) ?>;
	}
.l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown.active,
.l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_middle_text' ) ?>;
	}
.no-touch .l-subheader.at_middle a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_middle .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_middle_text_hover' ) ?>;
	}

/* Bottom Header Colors */
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown-list,
.l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_bottom_bg' ) ?>;
	}
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown.active,
.l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_bottom_text' ) ?>;
	}
.no-touch .l-subheader.at_bottom a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_bottom .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_bottom_text_hover' ) ?>;
	}

/* Transparent Header Colors */
.l-header.bg_transparent:not(.sticky) .l-subheader {
	color: <?php echo us_get_option( 'color_header_transparent_text' ) ?>;
	}
.no-touch .l-header.bg_transparent:not(.sticky) .w-text a:hover,
.no-touch .l-header.bg_transparent:not(.sticky) .w-html a:hover,
.no-touch .l-header.bg_transparent:not(.sticky) .w-dropdown a:hover,
.no-touch .l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1:hover > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
.l-header.bg_transparent:not(.sticky) .w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
	
/* Search Colors */
.w-search-form {
	background-color: <?php echo us_get_option( 'color_header_search_bg' ) ?>;
	color: <?php echo us_get_option( 'color_header_search_text' ) ?>;
	}

/*************************** Header Menu Colors ***************************/

/* Menu Hover Colors */
.no-touch .menu-item.level_1:hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}
.w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}

/* Menu Active Colors */
.menu-item.level_1.current-menu-item > .w-nav-anchor,
.menu-item.level_1.current-menu-parent > .w-nav-anchor,
.menu-item.level_1.current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_active_text' ) ?>;
	}

/* Transparent Menu Active Text Color */
.l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1.current-menu-item > .w-nav-anchor,
.l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1.current-menu-ancestor > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_menu_transparent_active_text' ) ?>;
	}

/* Dropdown Colors */
.w-nav-list:not(.level_1) {
	background-color: <?php echo us_get_option( 'color_drop_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_text' ) ?>;
	}

/* Dropdown Hover Colors */
.no-touch .menu-item:not(.level_1):hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_hover_text' ) ?>;
	}

/* Dropdown Active Colors */
.menu-item:not(.level_1).current-menu-item > .w-nav-anchor,
.menu-item:not(.level_1).current-menu-parent > .w-nav-anchor,
.menu-item:not(.level_1).current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_active_text' ) ?>;
	}

/* Header Button */
.w-cart-quantity,
.btn.w-menu-item,
.btn.menu-item.level_1 > a,
.l-footer .vc_wp_custommenu.layout_hor .btn > a {
	background-color: <?php echo us_get_option( 'color_menu_button_bg' ) ?> !important;
	color: <?php echo us_get_option( 'color_menu_button_text' ) ?> !important;
	}
.no-touch .btn.w-menu-item:hover,
.no-touch .btn.menu-item.level_1 > a:hover,
.no-touch .l-footer .vc_wp_custommenu.layout_hor .btn > a:hover {
	background-color: <?php echo us_get_option( 'color_menu_button_hover_bg' ) ?> !important;
	color: <?php echo us_get_option( 'color_menu_button_hover_text' ) ?> !important;
	}


/*************************** Content Colors ***************************/

/* Background Color */
body.us_iframe,
.l-preloader,
.l-canvas,
.l-footer,
.l-popup-box-content,
.w-blog.layout_flat .w-blog-post-h,
.w-blog.layout_cards .w-blog-post-h,
.g-filters.style_1 .g-filters-item.active,
.no-touch .g-filters-item.active:hover,
.w-portfolio-item-anchor,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.no-touch .w-tabs.layout_default .w-tabs-item.active:hover,
.no-touch .w-tabs.layout_ver .w-tabs-item.active:hover,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.w-cart-dropdown,
.us-woo-shop_modern .product-h,
.us-woo-shop_modern .product-meta,
.no-touch .us-woo-shop_trendy .product:hover .product-h,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce .shipping-calculator-form,
.woocommerce #payment .payment_box,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
#bbp-user-navigation li.current,
<?php } ?>
<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
.chosen-search input,
.chosen-choices li.search-choice,
<?php } ?>
.wpml-ls-statics-footer,
.select2-selection__choice,
.select2-search input {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce #payment .payment_methods li > input:checked + label,
.woocommerce .blockUI.blockOverlay {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?> !important;
	}
<?php } ?>
.w-tabs.layout_modern .w-tabs-item:after {
	border-bottom-color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.tribe-events-calendar thead th {
	color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
.w-btn.color_contrast.style_solid,
.no-touch .btn_hov_fade .w-btn.color_contrast.style_outlined:hover,
.no-touch .btn_hov_slide .w-btn.color_contrast.style_outlined:hover,
.no-touch .btn_hov_reverse .w-btn.color_contrast.style_outlined:hover {
	color: <?php echo us_get_option( 'color_content_bg' ) ?> !important;
	}

/* Alternate Background Color */
input,
textarea,
select,
.l-section.for_blogpost .w-blog-post-preview,
.w-actionbox.color_light,
.g-filters.style_1,
.g-filters.style_2 .g-filters-item.active,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.g-loadmore-btn,
.w-pricing-item-header,
.w-progbar-bar,
.w-progbar.style_3 .w-progbar-bar:before,
.w-progbar.style_3 .w-progbar-bar-count,
.w-socials.style_solid .w-socials-item-link,
.w-tabs.layout_default .w-tabs-list,
.w-tabs.layout_ver .w-tabs-list,
.w-testimonials.style_4 .w-testimonial-h:before,
.w-testimonials.style_6 .w-testimonial-text,
.no-touch .l-main .widget_nav_menu a:hover,
.wp-caption-text,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.us-woo-shop_trendy .products .product-category > a,
.woocommerce .quantity .plus,
.woocommerce .quantity .minus,
.woocommerce-tabs .tabs,
.woocommerce .cart_totals,
.woocommerce-checkout #order_review,
.woocommerce ul.order_details,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
#subscription-toggle,
#favorite-toggle,
#bbp-user-navigation,
<?php } ?>
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-bar-views-list,
.tribe-events-day-time-slot h5,
.tribe-events-present,
.tribe-events-single-section,
<?php } ?>
<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
.ginput_container_creditcard,
.chosen-single,
.chosen-drop,
.chosen-choices,
<?php } ?>
.smile-icon-timeline-wrap .timeline-wrapper .timeline-block,
.smile-icon-timeline-wrap .timeline-feature-item.feat-item,
.wpml-ls-legacy-dropdown a,
.wpml-ls-legacy-dropdown-click a,
.tablepress .row-hover tr:hover td,
.select2-selection,
.select2-dropdown {
	background-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}
.timeline-wrapper .timeline-post-right .ult-timeline-arrow l,
.timeline-wrapper .timeline-post-left .ult-timeline-arrow l,
.timeline-feature-item.feat-item .ult-timeline-arrow l {
	border-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}

/* Border Color */
hr,
td,
th,
.l-section,
.vc_column_container,
.vc_column-inner,
.w-author,
.w-btn.color_light,
.w-comments-list,
.w-image,
.w-pricing-item-h,
.w-profile,
.w-separator,
.w-sharing-item,
.w-tabs-list,
.w-tabs-section,
.w-tabs-section-header:before,
.w-tabs.layout_timeline.accordion .w-tabs-section-content,
.w-testimonial-h,
.widget_calendar #calendar_wrap,
.l-main .widget_nav_menu .menu,
.l-main .widget_nav_menu .menu-item a,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce .button,
.woocommerce .login,
.woocommerce .track_order,
.woocommerce .checkout_coupon,
.woocommerce .lost_reset_password,
.woocommerce .register,
.woocommerce .cart.variations_form,
.woocommerce .commentlist .comment-text,
.woocommerce .comment-respond,
.woocommerce .related,
.woocommerce .upsells,
.woocommerce .cross-sells,
.woocommerce .checkout #order_review,
.widget_price_filter .ui-slider-handle,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
#bbpress-forums fieldset,
.bbp-login-form fieldset,
#bbpress-forums .bbp-body > ul,
#bbpress-forums li.bbp-header,
.bbp-replies .bbp-body,
div.bbp-forum-header,
div.bbp-topic-header,
div.bbp-reply-header,
.bbp-pagination-links a,
.bbp-pagination-links span.current,
span.bbp-topic-pagination a.page-numbers,
.bbp-logged-in,
<?php } ?>
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-events-list-separator-month span:before,
.tribe-events-list-separator-month span:after,
.type-tribe_events + .type-tribe_events,
<?php } ?>
<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
.gform_wrapper .gsection,
.gform_wrapper .gf_page_steps,
.gform_wrapper li.gfield_creditcard_warning,
.form_saved_message,
<?php } ?>
.smile-icon-timeline-wrap .timeline-line {
	border-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
.w-separator,
.w-iconbox.color_light .w-iconbox-icon {
	color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
.w-btn.color_light.style_solid,
.w-btn.color_light.style_outlined:before,
.no-touch .btn_hov_reverse .w-btn.color_light.style_outlined:hover,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.no-touch .g-loadmore-btn:hover,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce .button,
.no-touch .woocommerce .quantity .plus:hover,
.no-touch .woocommerce .quantity .minus:hover,
.no-touch .woocommerce #payment .payment_methods li > label:hover,
.widget_price_filter .ui-slider:before,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
.bbpress .button,
<?php } ?>
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
#tribe-bar-collapse-toggle,
<?php } ?>
<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
.gform_wrapper .gform_page_footer .gform_previous_button,
<?php } ?>
.no-touch .wpml-ls-sub-menu a:hover {
	background-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
.w-iconbox.style_outlined.color_light .w-iconbox-icon,
.w-person-links-item,
.w-socials.style_outlined .w-socials-item-link,
.pagination .page-numbers {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_border' ) ?> inset;
	}
.w-tabs.layout_trendy .w-tabs-list {
	box-shadow: 0 -1px 0 <?php echo us_get_option( 'color_content_border' ) ?> inset;
	}

/* Heading Color */
h1, h2, h3, h4, h5, h6,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce .product .price,
<?php } ?>
.w-counter.color_heading .w-counter-number {
	color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}
.w-progbar.color_heading .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}

/* Text Color */
input,
textarea,
select,
.l-canvas,
.l-footer,
.l-popup-box-content,
.w-blog.layout_flat .w-blog-post-h,
.w-blog.layout_cards .w-blog-post-h,
.w-form-row-field:before,
.w-iconbox.color_light.style_circle .w-iconbox-icon,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.bbpress .button,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.w-cart-dropdown,
.woocommerce .button,
<?php } ?>
.select2-dropdown {
	color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
.w-btn.color_contrast.style_outlined,
.no-touch .btn_hov_reverse .w-btn.color_contrast.style_solid:hover {
	color: <?php echo us_get_option( 'color_content_text' ) ?> !important;
	}
.w-btn.color_contrast.style_solid,
.w-btn.color_contrast.style_outlined:before,
.no-touch .btn_hov_reverse .w-btn.color_contrast.style_outlined:hover,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.w-progbar.color_text .w-progbar-bar-h,
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-mobile #tribe-events-footer a,
.tribe-events-calendar thead th,
<?php } ?>
.w-scroller-dot span {
	background-color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-events-calendar thead th,
<?php } ?>
.w-btn.color_contrast {
	border-color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
.w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_text' ) ?> inset;
	}
.w-scroller-dot span {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_text' ) ?>;
	}

/* Link Color */
a {
	color: <?php echo us_get_option( 'color_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch a:hover,
.no-touch a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .tablepress .sorting:hover,
.no-touch .w-blog-post-title a:hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?>;
	}
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.no-touch .w-cart-dropdown a:not(.button):hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?> !important;
	}
<?php } ?>

/* Primary Color */
.highlight_primary,
.g-preloader,
.l-main .w-contacts-item:before,
.w-counter.color_primary .w-counter-number,
.g-filters-item.active,
.no-touch .g-filters.style_1 .g-filters-item.active:hover,
.no-touch .g-filters.style_2 .g-filters-item.active:hover,
.w-form-row.focused .w-form-row-field:before,
.w-iconbox.color_primary .w-iconbox-icon,
.w-separator.color_primary,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_primary .w-sharing-item:hover .w-sharing-icon,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_trendy .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.w-tabs-section.active .w-tabs-section-header,
.w-testimonials.style_2 .w-testimonial-h:before,
.tablepress .sorting_asc,
.tablepress .sorting_desc,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce .star-rating span:before,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce #payment .payment_methods li > input:checked + label,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
#subscription-toggle span.is-subscribed:before,
#favorite-toggle span.is-favorite:before,
<?php } ?>
.no-touch .owl-prev:hover,
.no-touch .owl-next:hover {
	color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.w-btn.color_primary.style_outlined,
.no-touch .btn_hov_reverse .w-btn.color_primary.style_solid:hover {
	color: <?php echo us_get_option( 'color_content_primary' ) ?> !important;
	}
.l-section.color_primary,
.l-titlebar.color_primary,
.no-touch .l-navigation-item:hover .l-navigation-item-arrow,
.highlight_primary_bg,
.w-actionbox.color_primary,
.w-blog-post-preview-icon,
.w-blog.layout_cards .format-quote .w-blog-post-h,
input[type="button"],
input[type="submit"],
.w-btn.color_primary.style_solid,
.w-btn.color_primary.style_outlined:before,
.no-touch .btn_hov_reverse .w-btn.color_primary.style_outlined:hover,
.no-touch .g-filters-item:hover,
.w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .w-iconbox.style_outlined .w-iconbox-icon:before,
.no-touch .w-person-links-item:before,
.w-pricing-item.type_featured .w-pricing-item-header,
.w-progbar.color_primary .w-progbar-bar-h,
.w-sharing.type_solid.color_primary .w-sharing-item,
.w-sharing.type_fixed.color_primary .w-sharing-item,
.w-sharing.type_outlined.color_primary .w-sharing-item:before,
.w-socials-item-link-hover,
.w-tabs.layout_modern .w-tabs-list,
.w-tabs.layout_trendy .w-tabs-item:after,
.w-tabs.layout_timeline .w-tabs-item:before,
.w-tabs.layout_timeline .w-tabs-section-header-h:before,
.no-touch .w-testimonials.style_6 .w-testimonial-h:hover .w-testimonial-text,
.no-touch .w-header-show:hover,
.no-touch .w-toplink.active:hover,
.no-touch .pagination .page-numbers:before,
.pagination .page-numbers.current,
.l-main .widget_nav_menu .menu-item.current-menu-item > a,
.rsThumb.rsNavSelected,
.no-touch .tp-leftarrow.custom:before,
.no-touch .tp-rightarrow.custom:before,
.smile-icon-timeline-wrap .timeline-separator-text .sep-text,
.smile-icon-timeline-wrap .timeline-wrapper .timeline-dot,
.smile-icon-timeline-wrap .timeline-feature-item .timeline-dot,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
p.demo_store,
.woocommerce .button.alt,
.woocommerce .button.checkout,
.woocommerce .button.add_to_cart_button,
.woocommerce .onsale,
.widget_price_filter .ui-slider-range,
.widget_layered_nav_filters ul li a,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
.no-touch .bbp-pagination-links a:hover,
.bbp-pagination-links span.current,
.no-touch span.bbp-topic-pagination a.page-numbers:hover,
<?php } ?>
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-events-calendar td.mobile-active,
.tribe-events-button,
.datepicker td.day.active,
.datepicker td span.active,
<?php } ?>
<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) { ?>
.gform_page_footer .gform_next_button,
.gf_progressbar_percentage,
.chosen-results li.highlighted,
<?php } ?>
.select2-results__option--highlighted,
.l-body .cl-btn {
	background-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
blockquote,
.w-btn.color_primary,
.g-filters.style_3 .g-filters-item.active,
.no-touch .owl-prev:hover,
.no-touch .owl-next:hover,
.no-touch .w-logos.style_1 .w-logos-item:hover,
.w-separator.color_primary,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.no-touch .w-testimonials.style_1 .w-testimonial-h:hover,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.woocommerce .button.alt,
.woocommerce .button.checkout,
.woocommerce .button.add_to_cart_button,
.woocommerce-product-gallery li img,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
.bbp-pagination-links span.current,
.no-touch #bbpress-forums .bbp-pagination-links a:hover,
.no-touch #bbpress-forums .bbp-topic-pagination a:hover,
#bbp-user-navigation li.current,
<?php } ?>
.owl-dot.active span,
.rsBullet.rsNavSelected span,
.tp-bullets.custom .tp-bullet {
	border-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.l-main .w-contacts-item:before,
.w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_primary' ) ?> inset;
	}
input:focus,
textarea:focus,
select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_primary' ) ?>;
	}

/* Secondary Color */
.no-touch .w-blognav-item:hover .w-blognav-title,
.w-counter.color_secondary .w-counter-number,
.w-iconbox.color_secondary .w-iconbox-icon,
.w-separator.color_secondary,
.w-sharing.type_outlined.color_secondary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_secondary .w-sharing-item:hover .w-sharing-icon,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.no-touch .woocommerce .stars:hover a,
.no-touch .woocommerce .stars a:hover,
<?php } ?>
.highlight_secondary {
	color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.w-btn.color_secondary.style_outlined,
.no-touch .btn_hov_reverse .w-btn.color_secondary.style_solid:hover {
	color: <?php echo us_get_option( 'color_content_secondary' ) ?> !important;
	}
.l-section.color_secondary,
.l-titlebar.color_secondary,
.no-touch .w-blog.layout_cards .w-blog-post-meta-category a:hover,
.no-touch .w-blog.layout_tiles .w-blog-post-meta-category a:hover,
.no-touch .l-section.preview_trendy .w-blog-post-meta-category a:hover,
.no-touch body:not(.btn_hov_none) .button:hover,
.no-touch body:not(.btn_hov_none) input[type="button"]:hover,
.no-touch body:not(.btn_hov_none) input[type="submit"]:hover,
.w-btn.color_secondary.style_solid,
.w-btn.color_secondary.style_outlined:before,
.no-touch .btn_hov_reverse .w-btn.color_secondary.style_outlined:hover,
.w-actionbox.color_secondary,
.w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.w-progbar.color_secondary .w-progbar-bar-h,
.w-sharing.type_solid.color_secondary .w-sharing-item,
.w-sharing.type_fixed.color_secondary .w-sharing-item,
.w-sharing.type_outlined.color_secondary .w-sharing-item:before,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.no-touch .widget_layered_nav_filters ul li a:hover,
<?php } ?>
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.no-touch .btn_hov_slide .tribe-events-button:hover,
<?php } ?>
.highlight_secondary_bg {
	background-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.w-btn.color_secondary,
.w-separator.color_secondary {
	border-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.w-iconbox.color_secondary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_secondary .w-sharing-item {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_secondary' ) ?> inset;
	}

/* Fade Elements Color */
.l-main .w-author-url,
.l-main .w-blog-post-meta > *,
.l-main .w-profile-link.for_logout,
.l-main .w-testimonial-author-role,
.l-main .w-testimonials.style_4 .w-testimonial-h:before,
.l-main .widget_tag_cloud,
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.l-main .widget_product_tag_cloud,
.woocommerce-breadcrumb,
<?php } ?>
<?php if ( is_plugin_active( 'bbpress/bbpress.php' ) ) { ?>
p.bbp-topic-meta,
<?php } ?>
.highlight_faded {
	color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
.w-blog.layout_latest .w-blog-post-meta-date {
	border-color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
<?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
.tribe-events-cost,
.tribe-events-list .tribe-events-event-cost {
	background-color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
<?php } ?>

/*************************** Alternate Content Colors ***************************/

/* Background Color */
.l-section.color_alternate,
.l-titlebar.color_alternate,
.color_alternate .g-filters.style_1 .g-filters-item.active,
.no-touch .color_alternate .g-filters-item.active:hover,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_default .w-tabs-item.active:hover,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_ver .w-tabs-item.active:hover,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	background-color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}
.color_alternate .w-btn.color_contrast.style_solid,
.no-touch .btn_hov_fade .color_alternate .w-btn.color_contrast.style_outlined:hover,
.no-touch .btn_hov_slide .color_alternate .w-btn.color_contrast.style_outlined:hover,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_contrast.style_outlined:hover {
	color: <?php echo us_get_option( 'color_alt_content_bg' ) ?> !important;
	}
.color_alternate .w-tabs.layout_modern .w-tabs-item:after {
	border-bottom-color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}

/* Alternate Background Color */
.color_alternate input,
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-blog-post-preview-icon,
.color_alternate .w-blog.layout_flat .w-blog-post-h,
.color_alternate .w-blog.layout_cards .w-blog-post-h,
.color_alternate .g-filters.style_1,
.color_alternate .g-filters.style_2 .g-filters-item.active,
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon,
.color_alternate .g-loadmore-btn,
.color_alternate .w-pricing-item-header,
.color_alternate .w-progbar-bar,
.color_alternate .w-socials.style_solid .w-socials-item-link,
.color_alternate .w-tabs.layout_default .w-tabs-list,
.color_alternate .w-testimonials.style_4 .w-testimonial-h:before,
.color_alternate .w-testimonials.style_6 .w-testimonial-text,
.color_alternate .wp-caption-text,
.color_alternate .ginput_container_creditcard {
	background-color: <?php echo us_get_option( 'color_alt_content_bg_alt' ) ?>;
	}

/* Border Color */
.l-section.color_alternate,
.l-section.color_alternate *,
.l-section.color_alternate .w-btn.color_light {
	border-color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-separator,
.color_alternate .w-iconbox.color_light .w-iconbox-icon {
	color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-btn.color_light.style_solid,
.color_alternate .w-btn.color_light.style_outlined:before,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_light.style_outlined:hover,
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon,
.no-touch .color_alternate .g-loadmore-btn:hover {
	background-color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-iconbox.style_outlined.color_light .w-iconbox-icon,
.color_alternate .w-person-links-item,
.color_alternate .w-socials.style_outlined .w-socials-item-link,
.color_alternate .pagination .page-numbers {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_border' ) ?> inset;
	}
.color_alternate .w-tabs.layout_trendy .w-tabs-list {
	box-shadow: 0 -1px 0 <?php echo us_get_option( 'color_alt_content_border' ) ?> inset;
	}

/* Heading Color */
.l-titlebar.color_alternate h1,
.l-section.color_alternate h1,
.l-section.color_alternate h2,
.l-section.color_alternate h3,
.l-section.color_alternate h4,
.l-section.color_alternate h5,
.l-section.color_alternate h6,
.l-section.color_alternate .w-counter-number {
	color: <?php echo us_get_option( 'color_alt_content_heading' ) ?>;
	}
.color_alternate .w-progbar.color_contrast .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_alt_content_heading' ) ?>;
	}

/* Text Color */
.l-titlebar.color_alternate,
.l-section.color_alternate,
.color_alternate input,
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-iconbox.color_contrast .w-iconbox-icon,
.color_alternate .w-iconbox.color_light.style_circle .w-iconbox-icon,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	color: <?php echo us_get_option( 'color_alt_content_text' ) ?>;
	}
.color_alternate .w-btn.color_contrast.style_outlined,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_contrast.style_solid:hover {
	color: <?php echo us_get_option( 'color_alt_content_text' ) ?> !important;
	}
.color_alternate .w-btn.color_contrast.style_solid,
.color_alternate .w-btn.color_contrast.style_outlined:before,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_contrast.style_outlined:hover,
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	background-color: <?php echo us_get_option( 'color_alt_content_text' ) ?>;
	}
.color_alternate .w-btn.color_contrast {
	border-color: <?php echo us_get_option( 'color_alt_content_text' ) ?>;
	}
.color_alternate .w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_text' ) ?> inset;
	}
	
/* Link Color */
.color_alternate a {
	color: <?php echo us_get_option( 'color_alt_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_alternate a:hover,
.no-touch .color_alternate a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .color_alternate .w-blog-post-title a:hover {
	color: <?php echo us_get_option( 'color_alt_content_link_hover' ) ?>;
	}

/* Primary Color */
.color_alternate .highlight_primary,
.l-main .color_alternate .w-contacts-item:before,
.color_alternate .w-counter.color_primary .w-counter-number,
.color_alternate .g-filters-item.active,
.no-touch .color_alternate .g-filters-item.active:hover,
.color_alternate .w-form-row.focused .w-form-row-field:before,
.color_alternate .w-iconbox.color_primary .w-iconbox-icon,
.no-touch .color_alternate .owl-prev:hover,
.no-touch .color_alternate .owl-next:hover,
.color_alternate .w-separator.color_primary,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.color_alternate .w-tabs.layout_trendy .w-tabs-item.active,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.color_alternate .w-tabs-section.active .w-tabs-section-header,
.color_alternate .w-testimonials.style_2 .w-testimonial-h:before {
	color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.color_alternate .w-btn.color_primary.style_outlined,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_primary.style_solid:hover {
	color: <?php echo us_get_option( 'color_alt_content_primary' ) ?> !important;
	}
.color_alternate .highlight_primary_bg,
.color_alternate .w-actionbox.color_primary,
.color_alternate .w-blog-post-preview-icon,
.color_alternate .w-blog.layout_cards .format-quote .w-blog-post-h,
.color_alternate input[type="button"],
.color_alternate input[type="submit"],
.color_alternate .w-btn.color_primary.style_solid,
.color_alternate .w-btn.color_primary.style_outlined:before,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_primary.style_outlined:hover,
.no-touch .color_alternate .g-filters-item:hover,
.color_alternate .w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .color_alternate .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .color_alternate .w-iconbox.style_outlined .w-iconbox-icon:before,
.color_alternate .w-pricing-item.type_featured .w-pricing-item-header,
.color_alternate .w-progbar.color_primary .w-progbar-bar-h,
.color_alternate .w-tabs.layout_modern .w-tabs-list,
.color_alternate .w-tabs.layout_trendy .w-tabs-item:after,
.color_alternate .w-tabs.layout_timeline .w-tabs-item:before,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h:before,
.no-touch .color_alternate .pagination .page-numbers:before,
.color_alternate .pagination .page-numbers.current {
	background-color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.color_alternate .w-btn.color_primary,
.color_alternate .g-filters.style_3 .g-filters-item.active,
.color_alternate .g-preloader,
.no-touch .color_alternate .owl-prev:hover,
.no-touch .color_alternate .owl-next:hover,
.no-touch .color_alternate .w-logos.style_1 .w-logos-item:hover,
.color_alternate .w-separator.color_primary,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_default .w-tabs-item.active:hover,
.no-touch .color_alternate .w-tabs.layout_ver .w-tabs-item.active:hover,
.no-touch .color_alternate .w-testimonials.style_1 .w-testimonial-h:hover {
	border-color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.l-main .color_alternate .w-contacts-item:before,
.color_alternate .w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_primary' ) ?> inset;
	}
.color_alternate input:focus,
.color_alternate textarea:focus,
.color_alternate select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}

/* Secondary Color */
.color_alternate .highlight_secondary,
.color_alternate .w-counter.color_secondary .w-counter-number,
.color_alternate .w-iconbox.color_secondary .w-iconbox-icon,
.color_alternate .w-separator.color_secondary {
	color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .w-btn.color_secondary.style_outlined,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_secondary.style_solid:hover {
	color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?> !important;
	}
.color_alternate .highlight_secondary_bg,
.no-touch .color_alternate input[type="button"]:hover,
.no-touch .color_alternate input[type="submit"]:hover,
.color_alternate .w-btn.color_secondary.style_solid,
.color_alternate .w-btn.color_secondary.style_outlined:before,
.no-touch .btn_hov_reverse .color_alternate .w-btn.color_secondary.style_outlined:hover,
.color_alternate .w-actionbox.color_secondary,
.color_alternate .w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.color_alternate .w-progbar.color_secondary .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .w-btn.color_secondary,
.color_alternate .w-separator.color_secondary {
	border-color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .w-iconbox.color_secondary.style_outlined .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_secondary' ) ?> inset;
	}

/* Fade Elements Color */
.color_alternate .highlight_faded,
.color_alternate .w-blog-post-meta > *,
.color_alternate .w-profile-link.for_logout,
.color_alternate .w-testimonial-author-role,
.color_alternate .w-testimonials.style_4 .w-testimonial-h:before {
	color: <?php echo us_get_option( 'color_alt_content_faded' ) ?>;
	}
.color_alternate .w-blog.layout_latest .w-blog-post-meta-date {
	border-color: <?php echo us_get_option( 'color_alt_content_faded' ) ?>;
	}

/*************************** Top Footer Colors ***************************/

/* Background Color */
.color_footer-top {
	background-color: <?php echo us_get_option( 'color_subfooter_bg' ) ?>;
	}

/* Alternate Background Color */
.color_footer-top input,
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-socials.style_solid .w-socials-item-link {
	background-color: <?php echo us_get_option( 'color_subfooter_bg_alt' ) ?>;
	}

/* Border Color */
.color_footer-top,
.color_footer-top *,
.color_footer-top .w-btn.color_light {
	border-color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-btn.color_light.style_solid,
.color_footer-top .w-btn.color_light.style_outlined:before {
	background-color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-separator {
	color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-socials.style_outlined .w-socials-item-link {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_subfooter_border' ) ?> inset;
	}

/* Text Color */
.color_footer-top,
.color_footer-top input,
.color_footer-top textarea,
.color_footer-top select {
	color: <?php echo us_get_option( 'color_subfooter_text' ) ?>;
	}

/* Link Color */
.color_footer-top a {
	color: <?php echo us_get_option( 'color_subfooter_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-top a:hover,
.no-touch .color_footer-top a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .color_footer-top .w-form-row.focused .w-form-row-field:before {
	color: <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}
.color_footer-top input:focus,
.color_footer-top textarea:focus,
.color_footer-top select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}

/*************************** Bottom Footer Colors ***************************/

/* Background Color */
.color_footer-bottom {
	background-color: <?php echo us_get_option( 'color_footer_bg' ) ?>;
	}
	
/* Alternate Background Color */
.color_footer-bottom input,
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-socials.style_solid .w-socials-item-link {
	background-color: <?php echo us_get_option( 'color_footer_bg_alt' ) ?>;
	}
	
/* Border Color */
.color_footer-bottom,
.color_footer-bottom *,
.color_footer-bottom .w-btn.color_light {
	border-color: <?php echo us_get_option( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-btn.color_light.style_solid,
.color_footer-bottom .w-btn.color_light.style_outlined:before {
	background-color: <?php echo us_get_option( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-separator {
	color: <?php echo us_get_option( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-socials.style_outlined .w-socials-item-link {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_footer_border' ) ?> inset;
	}
	
/* Text Color */
.color_footer-bottom,
.color_footer-bottom input,
.color_footer-bottom textarea,
.color_footer-bottom select {
	color: <?php echo us_get_option( 'color_footer_text' ) ?>;
	}

/* Link Color */
.color_footer-bottom a {
	color: <?php echo us_get_option( 'color_footer_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-bottom a:hover,
.no-touch .color_footer-bottom a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .color_footer-bottom .w-form-row.focused .w-form-row-field:before {
	color: <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	}
.color_footer-bottom input:focus,
.color_footer-bottom textarea:focus,
.color_footer-bottom select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	}

/* Menu Dropdown Settings
   =============================================================================================================================== */
<?php
global $wpdb;
$wpdb_query = 'SELECT `id` FROM `' . $wpdb->posts . '` WHERE `post_type` = "nav_menu_item"';
$menu_items = array();
foreach ( $wpdb->get_results( $wpdb_query ) as $result ) {
	$menu_items[] = $result->id;
}
foreach ($menu_items as $menu_item_id):
	$settings = ( get_post_meta( $menu_item_id, 'us_mega_menu_settings', TRUE ) ) ? get_post_meta( $menu_item_id, 'us_mega_menu_settings', TRUE ) : array();
	if ( empty($settings) ) continue; ?>

<?php if ( $settings['columns'] != '1' AND $settings['width'] == 'full' ): ?>
.header_hor .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> {
	position: static;
}
.header_hor .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	left: 0;
	right: 0;
	width: 100%;
	transform-origin: 50% 0;
}
.header_inpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	transform-origin: 50% 100%;
}
<?php endif; ?>

<?php if ( $settings['direction'] == 1 AND ( $settings['columns'] == '1' OR ( $settings['columns'] != '1' AND $settings['width'] == 'custom' ) ) ): ?>
.header_hor:not(.rtl) .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	right: 0;
	transform-origin: 100% 0;
}
.header_hor.rtl .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	left: 0;
	transform-origin: 0 0;
	}
<?php endif; ?>

.w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	padding: <?php echo $settings['padding']; ?>px;
	background-size: <?php echo $settings['bg_image_size']; ?>;
	background-repeat: <?php echo $settings['bg_image_repeat']; ?>;
	background-position: <?php echo $settings['bg_image_position']; ?>;

<?php if ( $settings['bg_image'] AND $bg_image = usof_get_image_src( $settings['bg_image'] ) ): ?>
	background-image: url(<?php echo $bg_image[0] ?>);
<?php endif;

if ( $settings['color_bg'] != '' ): ?>
	background-color: <?php echo $settings['color_bg']; ?>;
<?php endif;

if ( $settings['color_text'] != '' ): ?>
	color: <?php echo $settings['color_text']; ?>;
<?php endif;

if ( $settings['columns'] != '1' AND $settings['width'] == 'custom' ): ?>
	width: <?php echo $settings['custom_width']; ?>px;
<?php endif; ?>

}

<?php endforeach; ?>

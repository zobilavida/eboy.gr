<?php
function crispshop_scripts() {
    $fonts = array();
    $fonts[] = get_theme_mod('crispshop_site_font', 'open_sans');
    $fonts[] = get_theme_mod('crispshop_site_hfont', 'open_sans');
    
    if (in_array("droid_sans", $fonts)) {
        wp_enqueue_style( 'crispshop-droid', 'https://fonts.googleapis.com/css?family=Droid+Sans:400,700' );
    } 

    if (in_array("open_sans", $fonts)) {
        wp_enqueue_style( 'crispshop-open', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i' );
    } 

    if (in_array("oswald", $fonts)) {
        wp_enqueue_style( 'crispshop-oswald', 'https://fonts.googleapis.com/css?family=Oswald:300,400,700' );
    } 

    if (in_array("pt_sans", $fonts)) {
        wp_enqueue_style( 'crispshop-pt', 'https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i' );
    } 

    if (in_array("lato", $fonts)) {
        wp_enqueue_style( 'crispshop-lato', 'https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,700i' );
    } 

    if (in_array("raleway", $fonts)) {
        wp_enqueue_style( 'crispshop-raleway', 'https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,700,700i' );
    } 

    if (in_array("ubuntu", $fonts)) {
        wp_enqueue_style( 'crispshop-ubuntu', 'https://fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,700,700i' );
    } 

    if (empty($fonts)) {
        wp_enqueue_style( 'crispshop-open', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i' );
    }

    wp_enqueue_style( 'crispshop-font-awesome', get_template_directory_uri() . '/css/font-awesome.css', '1.0.0' );

    if (is_front_page() || is_singular('product')) {
        wp_enqueue_style( 'crispshop-bxslider', get_template_directory_uri() . '/css/jquery.bxslider.min.css', '1.0.0' );
    }

    wp_enqueue_style( 'crispshop-style', get_template_directory_uri() . '/style.css', '1.0.0' );

    wp_enqueue_style( 'crispshop-custom', get_template_directory_uri() . '/css/crispshop-custom.css', '1.0.0' );

    if (is_front_page()) {
        wp_enqueue_script( 'crispshop-bxslider', get_template_directory_uri() . '/js/jquery.bxslider.min.js', array('jquery'), '4.2.12', true );
        wp_enqueue_script( 'crispshop-home', get_template_directory_uri() . '/js/crispshop-home.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'crispshop-home', 'crispshop_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }

    wp_dequeue_script('wc-add-to-cart');

    if (is_singular('product')) {
        wp_enqueue_script( 'crispshop-bxslider', get_template_directory_uri() . '/js/jquery.bxslider.min.js', array('jquery'), '4.2.12', true );
        wp_enqueue_script( 'crispshop-single', get_template_directory_uri() . '/js/crispshop-single.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'crispshop-single', 'crispshop_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }

    if (is_shop() || is_product_category()) {
        wp_enqueue_script( 'crispshop-archive', get_template_directory_uri() . '/js/crispshop-archive.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'crispshop-archive', 'crispshop_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }

    wp_enqueue_script( 'crispshop-scripts', get_template_directory_uri() . '/js/crispshop-script.js', array('jquery'), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'crispshop_scripts' );

if ( ! isset( $content_width ) ) {
    $content_width = 1120;
}

add_filter( 'use_default_gallery_style', '__return_false' );

function crispshop_custom_css() {
    $font_stack = array('droid_sans' => 'Droid Sans, sans-serif', 'open_sans' => 'Open Sans, sans-serif', 'oswald' => 'Oswald, sans-serif', 'pt_sans' => 'PT Sans, sans-serif', 'lato' => 'Lato, sans-serif', 'raleway' => 'Raleway, sans-serif', 'ubuntu' => 'Ubuntu, sans-serif');

    $background = get_theme_mod('crispshop_bg');
    $site_font = get_theme_mod('crispshop_site_font', 'open_sans');
    $site_size = get_theme_mod('crispshop_site_font_size', '14px');
    $site_style = get_theme_mod('crispshop_site_font_style', '400');
    $site_color = get_theme_mod('crispshop_site_font_color', '#111');
    
    $site_css = 'body {';
    if ($background) {
        $site_css .= ' background: ' . $background .'; ';
    }
    if(isset($font_stack[$site_font])){
        $site_font_face = $font_stack[$site_font];
        $site_css .= ' font-family:' . $site_font_face .'; ';
    }
    if ($site_size) {
        $site_css .= ' font-size:' . $site_size .'; ';
    }
    if ($site_style) {
        $site_css .= ' font-weight:' . $site_style .'; ';
    }
    if ($site_color) {
        $site_css .= ' color:' . $site_color .'; ';
    }
    $site_css .= '}';

    $crispshop_link_color = get_theme_mod('crispshop_link_color', '#ea3a3c');

    $site_css .= 'a {';
    $site_css .= ' color:' . $crispshop_link_color .'; ';
    $site_css .= '}';

    $site_bh_font = get_theme_mod('crispshop_site_hfont', 'open_sans');
    $site_bh_font_style = get_theme_mod('crispshop_site_hfont_style', '700');
    $site_bh_font_color = get_theme_mod('crispshop_site_hfont_color', '#111');

    $site_css .= 'h1, h2, h3, h4, h5, h6 {';
    if ($site_bh_font_style) {
        $site_css .= ' font-weight:' . $site_bh_font_style .'; ';
    }
    if(isset($font_stack[$site_bh_font])){
        $site_bh_font_face = $font_stack[$site_bh_font];
        $site_css .= ' font-family:' . $site_bh_font_face .'; ';
    }
    if ($site_bh_font_color) {
        $site_css .= ' color:' . $site_bh_font_color .'; ';
    }
    $site_css .= '}';

    $crispshop_site_hfont1_size = get_theme_mod('crispshop_site_hfont1_size', '28px');

    $site_css .= 'h1 {';
    if ($crispshop_site_hfont1_size) {
        $site_css .= ' font-size:' . $crispshop_site_hfont1_size .'; ';
    }
    $site_css .= '}';

    $crispshop_site_hfont2_size = get_theme_mod('crispshop_site_hfont2_size', '24px');

    if ($crispshop_site_hfont2_size) {
        $site_css .= 'h2 {';
        $site_css .= ' font-size:' . $crispshop_site_hfont2_size .'; ';
        $site_css .= '}';
    }

    $crispshop_site_hfont3_size = get_theme_mod('crispshop_site_hfont3_size', '22px');

    $site_css .= 'h3 {';
    if ($crispshop_site_hfont3_size) {
        $site_css .= ' font-size:' . $crispshop_site_hfont3_size .'; ';
    }
    $site_css .= '}';

    $crispshop_site_hfont4_size = get_theme_mod('crispshop_site_hfont4_size', '18px');

    $site_css .= 'h4 {';
    if ($crispshop_site_hfont4_size) {
        $site_css .= ' font-size:' . $crispshop_site_hfont4_size .'; ';
    }
    $site_css .= '}';

    $crispshop_site_hfont5_size = get_theme_mod('crispshop_site_hfont5_size', '14px');

    $site_css .= 'h5 {';
    if ($crispshop_site_hfont5_size) {
        $site_css .= ' font-size:' . $crispshop_site_hfont5_size .'; ';
    }
    $site_css .= '}';

    $crispshop_site_hfont6_size = get_theme_mod('crispshop_site_hfont6_size', '12px');

    $site_css .= 'h6 {';
    if ($crispshop_site_hfont6_size) {
        $site_css .= ' font-size:' . $crispshop_site_hfont6_size .'; ';
    }
    $site_css .= '}';

    $crispshop_top_bg = get_theme_mod('crispshop_top_bg', '#f1f1f1');
    $crispshop_top_font_color = get_theme_mod('crispshop_top_font_color', '#666');

    $site_css .= '#top-bar {';
    $site_css .= ' background-color:' . $crispshop_top_bg .'; ';
    $site_css .= ' color:' . $crispshop_top_font_color .'; ';
    $site_css .= '}';

    $site_css .= '#top-bar a {';
    $site_css .= ' color:' . $crispshop_top_font_color .'; ';
    $site_css .= '}';

    $crispshop_logo_max_width = get_theme_mod('crispshop_logo_max_width', '250px');

    $site_css .= '.site-branding img {';
    $site_css .= ' max-width:' . $crispshop_logo_max_width .'; ';
    $site_css .= '}';

    $crispshop_menu_color = get_theme_mod('crispshop_menu_color', '#444');

    $site_css .= '.main-navigation ul li a {';
    $site_css .= ' color:' . $crispshop_menu_color .'; ';
    $site_css .= '}';

    $crispshop_menu_hover_color = get_theme_mod('crispshop_menu_hover_color', '#ea3a3c');

    $site_css .= '.main-navigation ul li a:hover, .site-header .main-navigation ul li.current-menu-item a {';
    $site_css .= ' color:' . $crispshop_menu_hover_color .'; ';
    $site_css .= '}';

    $crispshop_base_color = get_theme_mod('crispshop_base_color', '#ea3a3c');

    $site_css .= '.secondary-categories > a, .secondary-search button, .secondary-cart a, .product-block h3 span, .products li .onsale, .product .onsale, .subscribe-right form input[type="submit"], .products .add_to_cart_button, .all-cats-menu, .all-cats-menu li:hover li a:hover, .button, .woocommerce-tabs .tabs a, .related h2 span, .up-sells h2 span, .comment-form input[type="submit"], input[type="submit"] .related h3 span, #top-bar .top-right .user-dropdown a:hover, .woocommerce-MyAccount-navigation a:hover, .woocommerce-MyAccount-navigation .is-active a, .icon-bars, .icon-bars::after, .icon-bars::before, .main-navigation .mobile-nav-wrap ul li.opened a {';
    $site_css .= ' background-color:' . $crispshop_base_color .'; ';
    $site_css .= '}';

    $site_css .= '.products .add_to_cart_button, .product-block h3, .bx-wrapper .bx-controls-direction a, .all-cats-menu li a, .woocommerce-tabs .tabs, .related h3, .up-sells h3, input[type="submit"] {';
    $site_css .= ' border-color:' . $crispshop_base_color .'; ';
    $site_css .= '}';

    $site_css .= '.all-cats-menu li a:hover, .all-cats-menu li:hover a, .products li a, .bx-wrapper .bx-controls-direction a, .crispshop-rating span.star.filled:before, .stars.selected span a.active:before, .stars.selected span a:not(.active):before, .product .summary .price, .woocommerce-review-link, .product-name a, product-remove a, .woocommerce-info a, .products .add_to_cart_button:hover, .site-header .main-navigation ul li.current-menu-item a, .main-navigation ul li.menu-item-has-children:hover ul li a:hover, input[type="submit"]:hover, #top-bar .top-right a:hover, #top-bar .top-right a:hover::before, .main-navigation ul li a:hover, .main-navigation ul li.current-menu-item a, .main-navigation .mobile-nav-wrap ul li.opened ul li a {';
    $site_css .= ' color:' . $crispshop_base_color .'; ';
    $site_css .= '}';

    $crispshop_footer_bg = get_theme_mod('crispshop_footer_bg', '#222');
    $crispshop_footer_text = get_theme_mod('crispshop_footer_text', '#fff');

    $site_css .= '.site-footer {';
    $site_css .= ' background-color:' . $crispshop_footer_bg .'; ';
    $site_css .= ' color:' . $crispshop_footer_text .'; ';
    $site_css .= '}';

    $crispshop_footer_logo_max_width = get_theme_mod('crispshop_footer_logo_max_width', '150px');

    $site_css .= '.footer-widget-1 img {';
    $site_css .= ' max-width:' . $crispshop_footer_logo_max_width .'; ';
    $site_css .= '}';

    $site_css .= '.footer-widget h3, .footer-widget a {';
    $site_css .= ' color:' . $crispshop_footer_text .'; ';
    $site_css .= '}';

    $site_css .= get_theme_mod('crispshop_custom_css');

    wp_add_inline_style( 'crispshop-custom', $site_css );
    
}

add_action( 'wp_enqueue_scripts', 'crispshop_custom_css' );
?>
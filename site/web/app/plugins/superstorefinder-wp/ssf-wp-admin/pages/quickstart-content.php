<?php 
include(SSF_WP_INCLUDES_PATH."/top-nav.php");
ssf_wp_move_upload_directories();
ssf_wp_initialize_variables();

# v3.0 -- What's New
if (!empty($_GET['ssf_wp_a_s_v3']) && $_GET['ssf_wp_a_s_v3'] == 1){$ssf_wp_vars['ssf_wp_a_s_check_v3'] = 'hide'; }
if ( (empty($ssf_wp_vars['ssf_wp_a_s_check_v3']) || $ssf_wp_vars['ssf_wp_a_s_check_v3'] != 'hide') && ssf_wp_data('ssf_wp_a_s_check_v3')!='hide' ) { 
//shifted to $ssf_wp_vars in v3.18, but ssf_wp_data('ssf_wp_a_s_check_v3')!='hide' left there so it won't show again to those who've already hidden it
print "<div class='wrap'>
<div class='input_section'>

	
					<div class='input_title'>
						
						<h3><span class='fa fa-bolt'>&nbsp;</span> Quick Start</h3>
						<span class='submit'>
						<a href='http://superstorefinder.net/support' id='__information' class='button button-primary' target='new'><span class='fa fa-user-md'>&nbsp;</span> Support</a>
						</span>
						<div class='clearfix'></div>
					</div>
					<div class='all_options'>
					
					<div class='option_input option_text'>
					<div class='ssf-wp-menu-alert' style='line-height: 22px;'>
<div style='padding:5px; /*background-image:url(".SSF_WP_IMAGES_BASE_ORIGINAL."/logo.small.png); background-repeat:no-repeat; padding-left:45px; background-position-y:10px;*/'><img src='".SSF_WP_IMAGES_BASE_ORIGINAL."/logo.small.png' style='vertical-align:middle'>&nbsp;<b>Super Store Finder Wordpress Plugin <span style='color:#FD8222;'>version {$ssf_wp_version}</span></b><br></div>

<div style='padding:7px;'><b>Shortcodes:</b> Use this shortcode on Wordpress Post of Page <br>
<code class='ssf-wp-menu-highlight-code'>[SUPER-STORE-FINDER]</code>
<b>Shortcodes with Category:</b> you can display your store locator with default category selected <br>
<code class='ssf-wp-menu-highlight-code'>[SUPER-STORE-FINDER CAT=Drinks]</code>
<b>Wordpress Template:</b> Additionaly, you can paste this code at wordpress php template file 
<br><code class='ssf-wp-menu-highlight-code'>&lt;?php if (function_exists('ssf_wp_template')) {print ssf_wp_template('[SUPER-STORE-FINDER]');} ?&gt;</code>
<b>Widget Search:</b> Copy paste this widget code on your Wordpress Theme Widget Area. View details <a target='new' href='http://superstorefinder.net/support/knowledgebase/widget-location-search-for-wordpress/'>here</a>.</br>

</div> 
</div>


<div class='ssf-wp-menu-alert' style='line-height: 22px;'>
<div style='padding:7px;'> <b>Important Notice</b>
<div class='ssf_wp_admin_success' style='line-height: 22px;'>Since 22 June 2016, Google Maps API key is required (<a target='new' href='http://superstorefinder.net/support/knowledgebase/new-google-maps-after-22-june-2016-will-require-google-api-key/'>Read Article</a>) - <a target='new' href='https://superstorefinder.net/support/knowledgebase/how-to-use-google-api-keys-for-super-store-finder/'>Learn More</a> | <a href='".SSF_WP_SETTINGS_PAGE."'>Set Key</a></div>
</div>
</div>
					<div class='clearfix'></div>
					
					<div style='margin-top:15px;'><iframe width='100%' height='650' src='https://www.youtube.com/embed/IZ0bxqYXgGM' frameborder='0' allowfullscreen></iframe></div>
					</div>
					
					
					
	
</div></div>";
}



include(SSF_WP_INCLUDES_PATH."/ssf-wp-footer.php");
?>
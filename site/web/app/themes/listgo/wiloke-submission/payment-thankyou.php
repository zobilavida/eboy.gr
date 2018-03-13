<?php
/*
 * Template name: Payment Thank you
 * This template displays the thank you message
 *
 * @since 1.0
 * @author Wiloke
 * @link https://wiloke.com
 * @package: Wiloke/Themes
 * @subpackage: Listgo
 */

get_header();
	if ( have_posts() ){
		while (have_posts()){
			the_post();
			$aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
			WilokePublic::singleHeaderBg($post, $aPageSettings);
			?>
			
			<div class="container">

				<?php
                if ( !isset($_REQUEST['wiloke_mode']) || $_REQUEST['wiloke_mode'] !== 'remaining' ){
	                the_content();
                }else{
                    esc_html_e('Thanks for your submitting! We will send an email to you soon.', 'listgo');
                }
                ?>
					
			</div>
	
			<?php

            /**
             * Updating Payment History
             * Warning: Don't touch this guy.
             * @hooked notifyAdminAboutNewlySubmission
             * @since 1.0
             */
			do_action( 'wiloke/payment/success' );
		}
	}
get_footer();
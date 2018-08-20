<?php

if(have_posts()) : while(have_posts()) : the_post();
$gallery = ( isset( $custom['mytheme_gallery'][0] ) && '' !== $custom['mytheme_gallery'][0] ) ? explode( ',', $custom['mytheme_gallery'][0] ) : '';
if (is_array($gallery) || is_object($gallery)) {
foreach ( $gallery as $key => $value ) {
  	$image_url = wp_get_attachment_url( $value );
  	$background = ( isset( $image_url ) && '' !== $image_url ) ? 'style="background:url( ' . esc_url( $image_url ) . ' ); -webkit-background-size: cover; background-size: cover; background-repeat: no-repeat; ' . $bg_pos . '"' : '';
var_dump($image_url);
}

}

    the_title();
    echo '<div class="entry-content"> Test';
    the_content();
    echo '</div>';
endwhile; endif;

?>

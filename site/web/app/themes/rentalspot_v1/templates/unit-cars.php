<?php
$args = array( 'post_type' => 'product', 'posts_per_page' => 100, 'facetwp' => true, );
$query = new WP_Query( $args );
while ( $query->have_posts() ) : $query->the_post();
 echo wc_get_template_part( 'content', 'single-car' );
endwhile;

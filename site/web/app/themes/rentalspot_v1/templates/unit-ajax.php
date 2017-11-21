<?php

        echo '<section class="content-block" id="slide03">';
        echo '<div class="container test">';
        echo '</div>';
        echo '<div class="container-fluid no-padding facetwp-template">';

$args = array( 'post_type' => 'product', 'posts_per_page' => 10, 'facetwp' => true, );


$query = new WP_Query( $args );

//$form = $booking_form->output();

while ( $query->have_posts() ) : $query->the_post();
  echo '<article class="'. join( ' ', get_post_class() ) .' grid-item col-lg-3" data-href='.get_permalink( $id ).'>';
//  echo wc_get_template_part( 'content', 'single-product' );
  //echo do_action( 'woocommerce_before_shop_loop_item_title' ); // image
//  echo 	do_action( 'woocommerce_shop_loop_item_title' );
  //echo 	do_action( 'woocommerce_after_shop_loop_item_title' );

//  echo do_action( 'woocommerce_single_product_summary' );
  //echo do_action( 'woocommerce_before_booking_form' );
  //$booking_form->output();
//  echo do_action( 'woocommerce_after_add_to_cart_form' );
  echo '</article>';
endwhile;

    echo '</div>';

  echo '</section>';

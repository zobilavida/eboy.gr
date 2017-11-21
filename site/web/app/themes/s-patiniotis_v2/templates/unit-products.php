<section id="products">
<div class="container-fluid">
  <div class="row">

    <div class="col-12 test">
  Facets
  <?php echo facetwp_display( 'facet', 'pickup' ); ?>
  <?php   echo facetwp_display( 'facet', 'product_categories' ); ?>
</div>
</div>
<div class="row">

  <div class="col-12 facetwp-template">
products
<div class="grid-sizer"></div>
  <div class="gutter-sizer"></div>
<?php


    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'product_cat'    => 'είδη-υγιεινής'
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;


        echo '<div class="grid_item card">';
        echo ''.the_post_thumbnail('', array('class' => 'card-img-top img-fluid')).'';

      echo  '<div class="card-block">';
      echo  '<h4 class="card-title">'.$product->get_name().'</h4>';
    //  echo  '<p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>';
      echo  '<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>';
    echo  '</div>';
    echo  '</div>';
    endwhile;

    wp_reset_query();
?>
</div>
</div>
 </section>

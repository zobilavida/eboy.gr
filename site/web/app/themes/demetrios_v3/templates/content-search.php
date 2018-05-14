<?php do_action ('demetrios_pages_header', 'demetrios_pages_header_custom_1' ) ?>
<?php
  $args_search = array(
  "post_type" => array('post', 'product'),
  "post_status" => "publish",
  "orderby" => "date",
  "order" => "DESC",
  "posts_per_page" => 10,
  "facetwp" => true
);
  $wp_query = new WP_Query( $args_search );
?>
<div class="container">
  <div class="row">
      <div class="col-12 px-5 py-5">
      <?php  echo facetwp_display( 'facet', 'search' ); ?>
      </div>
        </div>
</div>

  <div class="facetwp-template search container ">
    <div class="row">
    <div class="col-12 text-center">
      <?php echo $wp_query->found_posts; ?>
      <?php _e( 'Search Results Found', 'demetrios_3' ); ?></h1>
    </div>
    </div>
    <div class="row grid">



        <?php if ( have_posts() ) { ?>


            <?php while ( have_posts() ) { the_post(); ?>
          <?php if ($post->post_type == "post") { ?>


            <div class="card product ml-2 mb-3 grid-item w-24">
               <h3><a href="<?php echo get_permalink(); ?>">
                 <?php the_title();  ?>
               </a></h3>
               <?php  the_post_thumbnail('product-md') ?>
               <?php echo substr(get_the_excerpt(), 0,200); ?>
               <div class="h-readmore"> <a href="<?php the_permalink(); ?>">Read More</a></div>
             </div>

          <?php } ?>
          <?php if ($post->post_type == "product") { ?>


              <?php wc_get_template_part( 'content', 'product' ); ?>

          <?php } ?>






            <?php } ?>



        <?php } ?>
      </div>
    </div>

<section id="portfolio" >

<div class="container-fluid">
  <div class="row">
      <div class="col-12 text-center ">
    <?php do_action ('custom_actions', 'get_cats');?>
</div>
</div>
<div class="row portfolio">
    <div class="col-6 ajax_content portfolio_left">

</div>
<div class="col-6 ajax_content portfolio_right">
</div>
</div>
</div>

  <div class="container-fluid">
    <div class="row grid">
    <div class="grid-sizer"></div>
      <div class="gutter-sizer"></div>
  <?php

$query = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => 100, 'facetwp' => true,) );


if ( $query->have_posts() ) : ?>
<?php while ( $query->have_posts() ) : $query->the_post();
$post_categories = wp_get_post_categories( $post->ID );
$cats = array();

foreach($post_categories as $c){
    $cat = get_category( $c );
    $cats[] = array( 'name' => $cat->name, 'slug' => $cat->slug );
    //$custom_cats = $cat->name;
}

?>
  <div  <?php post_class('grid_item card col-6 col-lg-3 '); ?> data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" >
    <div class="content_small">
<?php if ( has_post_thumbnail() ) {
    $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );
     echo '<img width="100%" src="' . $image_src_thumbnail[0] . '">';

}
?>
</div>



</div>


<?php endwhile; wp_reset_postdata(); ?>
<!-- show pagination here -->
<?php else : ?>
<!-- show 404 error here -->
<?php endif; ?>
</div>
</div>
</section>

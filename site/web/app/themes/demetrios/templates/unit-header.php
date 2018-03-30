<header class="masthead">
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <?php $args = array(
       'posts_per_page' => 5,
       'tag' => 'slider'
    );
    $slider = new WP_Query($args);
    if($slider->have_posts()):
    $count = $slider->found_posts;
    ?>

    <ol class="carousel-indicators">
      <?php for($i = 0; $i < $count ;  $i++) { ?>
             <li data-target="#main-slider" data-slide-to="<?php echo $i; ?>" class="<?php echo ($i == 0) ? 'active' : ''?>"></li>
       <?php } ?>
    </ol>

    <div class="carousel-inner" role="listbox">
       <?php $i = 0; while($slider->have_posts()): $slider->the_post(); ?>
           <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''?>" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%,rgba(0,0,0,0.6) 100%), url( <?php the_post_thumbnail_url(); ?> )">

                 <div class="carousel-caption d-none d-md-block">
                     <h1 class="text-uppercase heading_1"><?php echo get_the_title(); ?></h1>
                     <?php $value = get_field( "sub_head" );

if( $value ) {
    echo '<h2>';
    echo $value;
    echo '</h2>';

} else {

}
?>
                 </div>
           </div><!--.carousel-item-->
        <?php $i++; endwhile; ?>
    </div> <!--.carouse-inner-->
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
<?php endif;  wp_reset_postdata(); ?>
  </div>
</header>

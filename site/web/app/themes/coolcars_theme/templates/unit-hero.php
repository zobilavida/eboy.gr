<div class="container hero">
  <div class="row">
    <div class="col-lg-12 col-xs-12">
      <?
    $intro = get_page_by_path('intro');
$content = $intro->post_content;
echo $content
?>

</div>
</div>
<div class="row">
  <div class="col-lg-12 col-xs-12">
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner" role="listbox">

                  <div class="carousel-item active">
                      <div class="row">
                    <?php query_posts('post_type=product&showposts=4'); ?>
                        <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;
                        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );?>

    <div class="col-6 col-lg-3">
      <img class="d-block w-100" src="<?php echo $image_src_thumbnail[0]; ?>" alt="1 slide">
      <span><?php echo the_title(); ?></span>
      <h3><?php echo the_content(); ?></h3>
    </div>





                <?php endwhile; endif; ?>
                <?php wp_reset_query(); ?>
                </div>
                  </div>


                <div class="carousel-item">
                    <div class="row">
                  <?php query_posts('post_type=product&showposts=4&offset=4'); ?>
                      <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;
                      $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );?>

                      <div class="col-6 col-lg-3">
                        <img class="d-block w-100" src="<?php echo $image_src_thumbnail[0]; ?>" alt="1 slide">
                        <span><?php echo the_title(); ?></span>
                        <h3><?php echo the_content(); ?></h3>
                      </div>



              <?php endwhile; endif; ?>
              <?php wp_reset_query(); ?>
              </div>
                </div>


      </div>

    </div>
</div>
</div>
<div class="row">
  <div class="col-12">
  <img src="<?= get_template_directory_uri(); ?>/dist/images/icon_viewcars.svg" class="icon_viewcars wp-post-image" alt="View all cars">
  </div>
</div>


</div>

<div class="container hero">
  <div class="row">
    <div class="col-lg-12 col-xs-12 hero">
      <?
    $intro = get_page_by_path('intro');
$content = $intro->post_content;
echo $content
?>

</div>
</div>
<div class="row">
  <div class="col-lg-12 col-xs-12">
    Test
    <div id="myCarousel" class="carousel" data-ride="carousel">
      <div class="carousel-inner" role="listbox">
            <?php query_posts('post_type=product&showposts=1'); ?>
                <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;?>
                  <div class="carousel-item active">


                            <span><?php echo the_title(); ?></span>
                            <h3><?php echo the_content(); ?></h3>


                  </div>
                <?php endwhile; endif; ?>
                <?php wp_reset_query(); ?>

                <?php query_posts('post_type=product&showposts=4&offset=1'); ?>

                    <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;?>
                      <div class="carousel-item">


                                <span><?php echo the_title(); ?></span>
                                <h3><?php echo the_content(); ?></h3>


                      </div>
                    <?php endwhile; endif; ?>
                    <?php wp_reset_query(); ?>


      </div>
      <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
</div>
</div>
<div class="row">
  <div class="col-12">
  <img src="<?= get_template_directory_uri(); ?>/dist/images/icon_viewcars.svg" class="icon_viewcars wp-post-image" alt="View all cars">
  </div>
</div>


</div>

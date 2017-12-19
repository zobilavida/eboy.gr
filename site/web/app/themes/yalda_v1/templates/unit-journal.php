<section class="journal">
<div class="container test">

<div class="row">
<div class="col-24">
  <div class="carousel slide" data-ride="carousel" id="postsCarousel">

    <div class="carousel-inner">
  <?php $catquery_1 = new WP_Query( 'cat=16&posts_per_page=1' );
 ?>


  <?php while($catquery_1->have_posts()) : $catquery_1->the_post();

  ?>
<div class="carousel-item active">
<a href="<?php the_permalink() ?>" rel="bookmark"><h2><?php the_title(); ?></h2></a>

<?php the_content(); ?>
</div>
  <?php endwhile;
      wp_reset_postdata();
  ?>


  <?php $catquery_2 = new WP_Query( 'cat=16&posts_per_page=1&offset=1' );
 ?>


  <?php while($catquery_2->have_posts()) : $catquery_2->the_post();

  ?>
<div class="carousel-item">
<a href="<?php the_permalink() ?>" rel="bookmark"><h2><?php the_title(); ?></h2></a>

<?php the_content(); ?>
</div>
  <?php endwhile;
      wp_reset_postdata();
  ?>

  <a class="carousel-control-prev" href="#postsCarousel" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#postsCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
</div>
  </div>
</div>

</div>

</section>

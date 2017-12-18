<section class="journal">
<div class="container test">
  <div class="row">

</div>

<div class="row">
<div class="col-24">
  <?php $catquery = new WP_Query( 'cat=16&posts_per_page=5' );
 ?>


  <?php while($catquery->have_posts()) : $catquery->the_post();

  ?>

<a href="<?php the_permalink() ?>" rel="bookmark"><h2><?php the_title(); ?></h2></a>

<?php the_content(); ?>
  </div>
  <?php endwhile;
      wp_reset_postdata();
  ?>

</div>

</div>

</section>

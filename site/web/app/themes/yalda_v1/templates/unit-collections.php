<section class="collections">
<div class="container-fluid">
  <div class="row">

    <?php $catquery_1 = new WP_Query( 'cat=210&posts_per_page=2' );
   ?>


    <?php while($catquery_1->have_posts()) : $catquery_1->the_post();

    ?>
  <div class="col-12 my-auto">
  <a href="<?php the_permalink() ?>" rel="bookmark"><h2><?php the_title(); ?></h2></a>

  <?php the_content(); ?>
  </div>
    <?php endwhile;
        wp_reset_postdata();
    ?>

</div></div>
</section>

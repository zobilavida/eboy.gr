<?php
$args = array(
      'posts_per_page' => 5
  );
  $featured = new WP_Query($args);

if ($featured->have_posts()): while($featured->have_posts()): $featured->the_post(); ?>

<div class="col-lg-4 col-12 services">
  <div class="row justify-content-center">
    <div class="col-12 justify-content-center">
<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'icon_services' ) ); ?>
</div>
<div class="col-12">
  <h3><a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></h3>
<?php the_excerpt();?>
</div>
</div>
</div>


<?php
endwhile; else:
endif;

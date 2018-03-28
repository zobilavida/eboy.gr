<?php
/**
 * Template Name: Store Finder Template
 */
?>
<?php
return array(
    'post_type' => 'stores',
    'post_status' => 'publish',
    'posts_per_page' => 15,
);?>
<div class="facetwp-template">
  <?php while ( have_posts() ): the_post(); ?>
<p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
<?php endwhile; ?>
</div>

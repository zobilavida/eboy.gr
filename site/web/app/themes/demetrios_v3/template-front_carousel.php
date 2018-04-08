<?php
/**
 * Template Name: Front page with carousel
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/unit', 'carousel_front'); ?>
  <?php get_template_part('templates/unit', 'fasa_book'); ?>
<?php endwhile; ?>

<?php
/**
 * Template Name: Front page video and composer
 */
?>

<div class="mybutton mybutton_vertical">
<a href="#" class="btn btn-info feedback" role="button">Book an Appointment</a>
</div>
  <?php //get_template_part('templates/unit', 'video_front'); ?>


  <?php while (have_posts()) : the_post(); ?>
    <?php //get_template_part('templates/page', 'header'); ?>
    <section class="bg-white">
      <div class="container-fluid">
    <?php get_template_part('templates/content', 'page'); ?>
  </div>
  </section>
  <?php endwhile; ?>

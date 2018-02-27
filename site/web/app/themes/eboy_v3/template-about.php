<?php
/**
 * Template Name: About eboy_v3 Template
 */
?>
<section class="about">
<div class="container">
  <div class="row">
    <div class="col-3">
<h1><?= get_post_field('post_title', $post->ID) ?></h1>
</div>
    <div class="col-9">
<?= get_post_field('post_content', $post->ID) ?>
</div>
</div>
<hr class="style1">
<div class="row">
  <div class="col-3">
<h1>Tools</h1>
</div>
  <div class="col-9">
<?php  $tools = get_post_meta($post->ID, 'tools_about', true); ?>
<?php echo $tools; ?>
</div>
</div>
<hr class="style1">
<div class="row">


    <?php
    $experience_title = get_post_meta($post->ID, 'past_jobs', false);
    if( count( $experience_title ) != 0 ) { ?>
      <div class="col-3">
  <h1>Experience</h1>
    </div>
    <div class="col-9 p-0">
      <?php if( have_rows('past_jobs') ): ?>



    <?php while( have_rows('past_jobs') ): the_row(); ?>

      <div class="experience-content pt-1"><?php the_sub_field('job_01'); ?></div>
        <div class="experience-content pt-4"><?php the_sub_field('job_02'); ?></div>

        <?php

        $sub_field_3 = get_sub_field('sub_field_3');

        // do something with $sub_field_3

        ?>

    <?php endwhile; ?>



<?php endif; ?>

    </div>
    <?php
    } else {
    // do nothing;
    }
    ?>

</div>
</div>
</section>

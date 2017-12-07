<?php
/**
 * Template Name: About eboy_v3 Template
 */
?>

<div class="container">
  <div class="row">
    <div class="col-3">
<?= get_post_field('post_title', $post->ID) ?>
</div>
    <div class="col-9">
<?= get_post_field('post_content', $post->ID) ?>
</div>
</div>
</div>

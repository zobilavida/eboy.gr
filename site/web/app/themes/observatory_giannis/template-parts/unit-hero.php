<?php $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' ); ?>

<section class="module hero_image" data-background="<?php echo $image_src_thumbnail[0]; ?>">
Hero
</section>

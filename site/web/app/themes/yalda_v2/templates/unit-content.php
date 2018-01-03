<?php    // The Query
   $recentposts = get_posts('numberposts=1&category=17');
   foreach ($recentposts as $post) :
       setup_postdata($post); $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'large' ); ?>
       <div class="col-lg-9 my-auto"><a href="<?php the_permalink() ?>"><h1><?php the_title(); ?></h1></a>
         <?php echo apply_filters( 'the_excerpt', $post->post_excerpt ); ?>
       </div>
       <?php
        $format = get_post_format() ? : 'standard';
        if ( $format == 'standard' ) :
           //echo $format;
           ?>

         <?php endif;?>
    <?php endforeach; ?>

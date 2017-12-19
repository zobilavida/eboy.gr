<?php    // The Query
   $recentposts = get_posts('numberposts=1&category=51');
   foreach ($recentposts as $post) :
       setup_postdata($post); $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' ); ?>
       <section class="paralax" style="background-image: url('<?php echo $image_src_thumbnail[0]; ?>')">
         <div class="container-fluid">
           <div class="row lookbook">
             <div class="col-lg-24 text-center">
        <h1> <?php echo apply_filters( 'the_title', $post->post_title ); ?> </h1>
        <p> <?php echo apply_filters( 'the_excerpt', $post->post_excerpt ); ?> </p>
      </div>
      </div>
      </div>
       </section>

         <?php endforeach; ?>

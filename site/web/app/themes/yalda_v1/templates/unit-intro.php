<section class="intro">
<div class="container-fluid test">
  <div class="row">
    <div class="col-lg-1 test">
    </div>

    <?php    // The Query
       $recentposts = get_posts('numberposts=1&category=15');
       foreach ($recentposts as $post) :
           setup_postdata($post); $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' ); ?>
           <div class="col-lg-9 my-auto"><a href="<?php the_permalink() ?>"><h1><?php the_title(); ?></h1></a>
             <?php echo apply_filters( 'the_excerpt', $post->post_excerpt ); ?>
           </div>
           <?php
            $format = get_post_format() ? : 'standard';
            if ( $format == 'standard' ) :
               //echo $format;
               ?>

         <div class="content_slider col-lg-12 my-auto">


               <div class="card">
                 <div class="card-block">
         <div id="carouselDocumentationIndicators" class="carousel slide" data-ride="carousel">
           <ol class="carousel-indicators">
           <?php
            $number = 0;
            if ( $post->post_type == 'post' && $post->post_status == 'publish' ) {
                $attachments = get_posts( array(
                  'post_type' => 'attachment',
                  'post_status' => 'inherit',
                  'post_mime_type' => 'image',
                  'numberposts' => 0,
                  'post_parent' => $post->ID
                  //'exclude'     => get_post_thumbnail_id()
                ) );
                if ( $attachments ) {
                  foreach ( $attachments as $attachment ) {
                    $datatarget = "#carouselDocumentationIndicators";
                     echo '<li data-target="' . $datatarget . '" data-slide-to="' . $number++ . '">';
                  }
                }
              }
           ?>
         </ol>
           <div class="carousel-inner out" role="listbox">
             <?php if ( $post->post_type == 'post' && $post->post_status == 'publish' ) {
                 $attachments = get_posts( array(
                   'post_type' => 'attachment',
                   'post_status' => 'inherit',
                   'post_mime_type' => 'image',
                   'numberposts' => 1,
                   'post_parent' => $post->ID
                   //'exclude'     => get_post_thumbnail_id()
                 ) );

                 if ( $attachments ) {
                   foreach ( $attachments as $attachment ) {
                     $class = "d-block img-fluid mime-" . sanitize_title( $attachment->post_mime_type );

                     $src = wp_get_attachment_image_src( $attachment->ID, "full-size");
                     echo '<div class="carousel-item active">';
                   //  echo '<img class="' . $class . '">' . $thumbimg . '</img>';
                      echo '<img class="' . $class . '" src="' . $src[0] . '" alt="">';
                     echo '</div>';
                   }

                 }
               }
             ?>
         <?php if ( $post->post_type == 'post' && $post->post_status == 'publish' ) {
             $attachments = get_posts( array(
               'post_type' => 'attachment',
               'post_status' => 'inherit',
               'post_mime_type' => '',
               'offset' => 1,
               'post_parent' => $post->ID
             ) );

             if ( $attachments ) {
               foreach ( $attachments as $attachment ) {
                 $class = "d-block img-fluid mime-" . sanitize_title( $attachment->post_mime_type );
                 $thumbimg = wp_get_attachment_link( $attachment->ID, 'thumbnail-size', true );
                 $src = wp_get_attachment_image_src( $attachment->ID, "full-size");
                 echo '<div class="carousel-item">';
               //  echo '<img class="' . $class . '">' . $thumbimg . '</img>';
                  echo '<img class="' . $class . '" src="' . $src[0] . '" alt="">';
                 echo '</div>';
               }

             }
           }
         ?>
         </div> <!-- carousel-inner END  -->
         </div> <!-- carouselDocumentationIndicators END  -->
         </div> <!-- card-block END  -->
         </div>  <!-- card END  -->
         <?php endif;?>
    <?php endforeach; ?>


</section>

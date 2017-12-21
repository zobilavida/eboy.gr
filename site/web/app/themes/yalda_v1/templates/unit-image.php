<div id="carouselDocumentationIndicators" class="carousel slide col-14" data-ride="carousel">

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

          $src = wp_get_attachment_image_src( $attachment->ID, "large");
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
      $src = wp_get_attachment_image_src( $attachment->ID, "large");
      echo '<div class="carousel-item">';
    //  echo '<img class="' . $class . '">' . $thumbimg . '</img>';
       echo '<img class="' . $class . '" src="' . $src[0] . '" alt="">';
      echo '</div>';
    }

  }
}
?>
</div> <!-- carousel-inner END  -->
</div>

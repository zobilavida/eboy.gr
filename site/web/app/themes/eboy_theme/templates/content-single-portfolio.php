
<?php while (have_posts()) : the_post(); ?>
  <article class="row">
<div class="content_slider">
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-block">
  <div id="carouselDocumentationIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#carouselDocumentationIndicators" data-slide-to="0" class="active"></li>
      <li data-target="#carouselDocumentationIndicators" data-slide-to="1"></li>
      <li data-target="#carouselDocumentationIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner out" role="listbox">
      <?php if ( $post->post_type == 'portfolio' && $post->post_status == 'publish' ) {
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
              $thumbimg = wp_get_attachment_link( $attachment->ID, 'thumbnail-size', true );
              $src = wp_get_attachment_image_src( $attachment->ID, "full-size");
              echo '<div class="carousel-item active">';
            //  echo '<img class="' . $class . '">' . $thumbimg . '</img>';
               echo '<img class="' . $class . '" src="' . $src[0] . '" alt="">';
              echo '</div>';
            }

          }
        }
      ?>
  <?php if ( $post->post_type == 'portfolio' && $post->post_status == 'publish' ) {
  		$attachments = get_posts( array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
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
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
		<div class="col-6 portfolio_info">
    <header>
      <h3 class="entry-title"><?php the_title(); ?></h3>
        <h4 class="entry-expert">
          <?php the_excerpt(); ?>
        </h4>
    </header>
    <div class="entry-meta">
      <table class="table">
        <tbody>
          <tr>
      <?php
      $portfolio_item = pods('portfolio', get_the_ID() );
      echo '<td class="tools">';
      echo '</td>';
      echo '<td>';
      echo '<h4>';
      echo $portfolio_item->display('tools');
      echo '</h4>';
      echo '</td>';
      echo '<td class="time">';
      echo '</td>';
      echo '<td>';
      echo '<h4>';
      echo $portfolio_item->display('time');
      echo '</h4>';
      echo '</td>';
      ?>
    </tr>
  </tbody>
</table>
    </div>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
	</div>

  </article>


<?php endwhile; ?>

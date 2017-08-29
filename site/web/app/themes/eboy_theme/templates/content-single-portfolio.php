
<?php while (have_posts()) : the_post(); ?>
  <article class="row">

  <?php if ( $post->post_type == 'portfolio' && $post->post_status == 'publish' ) {
  		$attachments = get_posts( array(
  			'post_type' => 'attachment',
  			'posts_per_page' => -1,
  			'post_parent' => $post->ID,
  			'exclude'     => get_post_thumbnail_id()
  		) );

  		if ( $attachments ) {
  			foreach ( $attachments as $attachment ) {
  				$class = "post-attachment mime-" . sanitize_title( $attachment->post_mime_type );
  				$thumbimg = wp_get_attachment_link( $attachment->ID, 'thumbnail-size', true );
  				echo '<li class="' . $class . ' data-design-thumbnail">' . $thumbimg . '</li>';
  			}

  		}
  	}
  ?>
</div>
		<div class="col-12 portfolio_info">
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
      echo '<h5>';
      echo $portfolio_item->display('tools');
      echo '</h5>';
      echo '</td>';
      echo '<td class="time">';
      echo '</td>';
      echo '<td>';
      echo '<h5>';
      echo $portfolio_item->display('time');
      echo '</h5>';
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

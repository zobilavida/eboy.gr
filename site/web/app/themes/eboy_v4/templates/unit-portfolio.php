<section class="portfolio">
<div class="container-fluid">
  <div class="row py-4">
    <div class="col-12">
    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
          <div class="filters btn-group mr-2 filter-button-group" role="group" aria-label="First group" id="box2">
            <ul id="menu2 p-0">
                <?php $filter_icon		= '<img class="ico svg-convert" src=" ' .get_template_directory_uri() .'/dist/images/ico_filter.svg">'; ?>

              <li class="active pl-0 filter_index" data-filter="*"><a href="javascript:;"><?php echo $filter_icon; ?></a></li>
  <?php
  $tags = get_terms( 'post_tag', array(
    'smallest' => 1, // size of least used tag
    'largest'  => 2, // size of most used tag
    'unit'     => 'rem', // unit for sizing the tags
    'number'   => 45, // displays at most 45 tags
    'orderby'  => 'count', // order tags alphabetically
    'order'    => 'DESC', // order tags by ascending order
    'show_count'=> 0, // you can even make tags for custom taxonomies
    'hide_empty' => true
) );

  if ( $tags ) :
      foreach ( $tags as $tag ) : ?>
        <li data-filter=".<?php echo esc_html( $tag->slug ); ?>">
          <a href="javascript:;" >
          <?php echo esc_html( $tag->name ); ?>
          </a>
        </li>
      <?php endforeach; ?>
  <?php endif; ?>
</ul>
</div>
</div>
</div>
</div>

    <!-- add extra container element for Masonry -->
    <div class="grid ">

      <?php

      $query = new WP_Query( array( 'post_type' => 'portfolio', 'order' => 'DESC', 'posts_per_page' => -1,) );


      if ( $query->have_posts() ) : ?>
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<div class="d-flex flex-row justify-content-between product grid-item py-4 px-0 col-12 <?php do_action( 'eboy_woocommerce_current_tags_thumb' ); ?>" data-href="<?php echo get_permalink( $post->ID ); ?>">
					<div class="p-2 grid-item-content">

<?php the_title( '<h1>', '</h1>' ); ?>
						<?php
$content = get_the_content('Read more');
print $content;
?>
				</div>
					<div class="p-2">
						<?php if ( has_post_thumbnail() ) {
			        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'large' );
			        echo '<a href="'.get_permalink( $post->ID ).'" >';
			         echo '<img width="100%" src="' . $image_src_thumbnail[0] . '" alt="eboy">';
			         echo '</a>';
			       }
			      ?>
					</div>
				</div>




<?php endwhile; wp_reset_postdata(); ?>
<?php endif; ?>


    </div>
  </div>


</div>
</section>

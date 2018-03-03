<section class="portfolio">
<div class="container-fluid">
  <div class="row py-5">
    <div class="col-12">
    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
          <div class="filters btn-group mr-2 filter-button-group" role="group" aria-label="First group">
            <ul>
                <?php $filter_icon		= '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/ico_filter.svg">'; ?>

              <li class="active pl-0" data-filter="*"><?php echo $filter_icon; ?></li>
  <?php
  $tags = get_terms( 'product_tag', array(
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
        <li data-filter=".<?php echo esc_html( $tag->slug ); ?>"><?php echo esc_html( $tag->name ); ?></li>
      <?php endforeach; ?>
  <?php endif; ?>
</ul>
</div>
</div>
</div>
</div>

    <!-- add extra container element for Masonry -->
    <div class="grid row facetwp-template">

      <?php

      $query = new WP_Query( array( 'post_type' => 'product', 'order' => 'ASC', 'posts_per_page' => -1, 'facetwp' => true,) );


      if ( $query->have_posts() ) : ?>
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
      <div class="product grid-item card col-6 col-md-4 col-lg-4 mb-3 <?php do_action( 'eboy_woocommerce_current_tags_thumb' ); ?>" data-href="<?php echo get_permalink( $post->ID ); ?>" >
        <div class="grid-item-content">
      <?php if ( has_post_thumbnail() ) {
        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );
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

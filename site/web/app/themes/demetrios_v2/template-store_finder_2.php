<?php
/**
 * Template Name: Store Finder Page 2
 */
?>

<div id="map" style="width:100%; height:400px"></div>
<div class="eboywp-template">
<?php
$custom_query = new WP_Query( array( 'post_type' => 'stores' ) );
$distance = eboywp_get_distance();
if ( false !== $distance ) {
    echo round( $distance, 2 );
}
while($custom_query->have_posts()) : $custom_query->the_post();

		$location = get_field('location');
?>



  <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                <h4>><?php the_title(); ?></h4>
                <p><?php the_content(); ?></p>
              </div>
  <div class="post-item" data-title="<?php the_title(); ?>" data-latitude="<?php echo $location['lat']; ?>" data-longitude="<?php echo $location['lng']; ?>" data-distance="<?php echo $distance; ?>">
    <?php the_title(); ?>
</div>

<?php endwhile; ?>
<?php wp_reset_postdata(); // reset the query ?>
</div>

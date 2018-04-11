<?php do_action( 'custom_store_finder', 'store_finder' ); ?>

			<?php
				// WP_Query arguments
				$args = array(
					"post_type" => "stores",
					"post_status" => "publish",
					"orderby" => "title",
					"order" => "ASC",
					"posts_per_page" => 35,
					'facetwp' => true // Also tried without this and accompanying function in functions.php
				);
				// The Query
				$query = new WP_Query( $args );
				?>
				<div class="eboywp-template container">
          <div class="row">
        	<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
					$email_2 = get_field( "email_2" );
					$street_address = get_field( "street_address" );
					$phone = get_field( "phone" );
					?>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php the_title(); ?></h5>
        <p class="card-text"><?php echo $street_address; ?></p>
				<a class="btn btn-primary" href="https://www.google.com/maps?saddr=My+Location&daddr=<?php $location = get_field('location'); echo $location['lat'] . ',' . $location['lng']; ?>"><?php _e('Get Directions','roots'); ?></a>
      </div>
    </div>
  </div>


				<?php endwhile; ?>
</div>
					<?php // joints_page_navi(); ?>

				<?php else : ?>
                  <?php wp_reset_postdata();?>
					<?php get_template_part( 'parts/content', 'missing' ); ?>

				<?php endif; ?>
				</div>

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
        	<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>




  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php the_title(); ?></h5>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="btn btn-primary">Go somewhere</a>
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

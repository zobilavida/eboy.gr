

<?php $catquery_1 = new WP_Query( 'cat=210&posts_per_page=3' ); ?>
<?php while($catquery_1->have_posts()) : $catquery_1->the_post(); ?>

  <div class="checkhere"  data-href="<?php the_permalink(); ?>"><?php echo get_post_meta( $post->ID, 'incr_number', true ); ?>. <?php the_title(); ?></div>

  <?php the_excerpt(); ?>
      <?php endwhile; wp_reset_postdata();?>

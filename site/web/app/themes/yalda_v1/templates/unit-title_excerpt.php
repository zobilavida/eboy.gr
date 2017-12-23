

<?php $catquery_1 = new WP_Query( 'cat=212&posts_per_page=3' ); ?>
<?php while($catquery_1->have_posts()) : $catquery_1->the_post(); ?>
<div class="row">
  <div class="col-2">
    <?php echo get_post_meta( $post->ID, 'incr_number', true ); ?>.
  </div>
  <div class="col-18 pl-1 checkhere"  data-href="<?php the_permalink(); ?>">
    <?php the_title(); ?>
  </div>
</div>
<div class="row">
  <div class="col-4 pl-1">
  </div>
  <div class="col-18 pl-1">
    <?php the_excerpt(); ?>
  </div>
</div>

      <?php endwhile; wp_reset_postdata();?>

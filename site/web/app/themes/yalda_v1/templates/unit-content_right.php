<?php $catquery_2 = new WP_Query( 'cat=210&posts_per_page=1' ); ?>
<?php while($catquery_2->have_posts()) : $catquery_2->the_post(); ?>
<div class="row">
  <div class="col-24">
<span>
  <?php the_content(); ?>
</span>
</div>
</div>
      <?php endwhile; wp_reset_postdata();?>

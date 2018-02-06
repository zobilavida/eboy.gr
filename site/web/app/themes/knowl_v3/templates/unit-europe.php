<section class="module" id="europe">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Ευρωπαϊκά Έργα' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Ευρωπαϊκά Έργα' ) );
        echo '<a href="'.$link.'">';
        echo $title;
        echo '</a>';
        ?></h2>
        <div class="module-subtitle font-serif">      <?php
        $page = get_page_by_title( 'Ευρωπαϊκά Έργα' );
        $content = apply_filters('the_content', $page->post_excerpt);

        echo $content;
        ?></div>
      </div>
    </div>
    <div class="row ">
      <?php $loop = new WP_Query( array( 'post_type' => 'europians', 'posts_per_page' => -1 ) ); ?>
      <?php
    //  $postNumber = 1;
      while ( $loop->have_posts() ) : $loop->the_post(); ?>
      <div class="single-post">
      <h1 class="post-number"><?php echo get_post_meta($post->ID,'your_post_type',true);  ?>.</h1>
      <h3 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
      <div class="post-content"><?php the_excerpt(); ?> </div>
      <?php edit_post_link(); ?>
      </div>
      <?php endwhile; wp_reset_query();?>          </div>
          </div>
        </section>

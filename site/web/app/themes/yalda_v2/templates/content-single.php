<div class="container-fluid ">
  <div class="row">
    <div class="col-12 d-flex justify-content-center p-0 ">
  <header>
    <h1 class="entry-title"><?php the_title(); ?></h1>

  </header>
  </div>
</div>
  <div class="row">
  <div class="col-2  d-none d-lg-block ">
    <?php
        wp_nav_menu( array(
            'menu'              => 'primary',
            'theme_location'    => 'primary',
            'depth'             => 2,
            'container'         => 'div',
            'container_class'   => '',
            'container_id'      => '',
            'menu_class'        => '',
            'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
            'walker'            => new wp_bootstrap_navwalker())
        );
    ?>
  </div>
  <div class="col-lg-10 p-0 ">
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class(); ?>>

        <div class="entry-content">
          <?php the_content(); ?>
          <?php

          $images = get_field('collection_gallery');

          if( $images ): ?>
              <div class="row">
                  <?php foreach( $images as $image ): ?>
                      <div class="col-12 col-lg-3 d-flex justify-content-center">
                          <a href="<?php echo $image['url']; ?>">
                               <img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo $image['alt']; ?>" />
                               <p class="d-flex justify-content-center"><?php echo $image['caption']; ?></p>
                          </a>

                      </div>
                  <?php endforeach; ?>
              </div>
          <?php endif; ?>
        </div>
    </div>
  </div>

    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>

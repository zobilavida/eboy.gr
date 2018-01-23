<div class="container-fluid ">
  <div class="row">
    <div class="col-12 d-flex justify-content-center p-0 ">

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
    <?php while (have_posts()) : the_post(); ?>
  <?php if (in_category( 'collection_1' )) : ?>
    <div class="col-lg-10 p-0 ">
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
    <?php endif;?>


    <?php if (in_category( 'collection_2' )) : ?>
      <div class="col-lg-10 p-0 ">
          <article <?php post_class(); ?>>
          <div class="entry-content">
            <?php
            $images = get_field('collection_gallery');
            $image_1 = $images[0];
            $image_2 = $images[1];
            $image_3 = $images[2];
            $image_4 = $images[3];
            if( $images ): ?>
                <div class="row">

                        <div class="col-12 col-lg-8 d-flex justify-content-center pt-5 ">
                          <div class="row">
                          <div class="col-12">
                            <a href="<?php echo $image_1['url']; ?>">
                                 <img class="img-fluid" src="<?php echo $image_1['sizes']['large']; ?>" alt="<?php echo $image_1['alt']; ?>" />
                                 <p class="d-flex justify-content-center"><?php echo $image_1['caption']; ?></p>
                            </a>
                          </div>
                          <div class="col-4">
                            <a href="<?php echo $image_4['url']; ?>">
                                 <img class="img-fluid img6" src="<?php echo $image_4['sizes']['large']; ?>" alt="<?php echo $image_4['alt']; ?>" />
                                 <p class="d-flex justify-content-center"><?php echo $image_4['caption']; ?></p>
                            </a>
                          </div>
                          </div>

                        </div>

                        <div class="col-12 col-lg-4 ">
                          <div class="row">
                              <div class="col-12 ">
                            <a href="<?php echo $image_2['url']; ?>">
                                 <img src="<?php echo $image_2['sizes']['medium']; ?>" alt="<?php echo $image_2['alt']; ?>" />
                                 <p class="d-flex justify-content-center"><?php echo $image_2['caption']; ?></p>
                            </a>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-12 ">
                            <a href="<?php echo $image_3['url']; ?>">
                                 <img src="<?php echo $image_3['sizes']['medium']; ?>" alt="<?php echo $image_3['alt']; ?>" />
                                 <p class="d-flex justify-content-center"><?php echo $image_3['caption']; ?></p>
                            </a>
                            </div>
                          </div>
                        </div>

                </div>
            <?php endif; ?>
            <div class="row">
              <div class="col-12 text-center">
                 <?php the_content(); ?>

            </div>
            </div>
          </div>
      </div>
      <?php endif;?>


  </div>

    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>

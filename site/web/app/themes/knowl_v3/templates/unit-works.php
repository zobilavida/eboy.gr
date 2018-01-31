<section class="module pb-0" id="works">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Συνεργασίες' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Συνεργασίες' ) );
        echo '<a href="'.$link.'">';
        echo $title;
        echo '</a>';
        ?></h2>
        <div class="module-subtitle font-serif"></div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">


      <div class="col-sm-12">
        <ul class="filter font-alt" id="filters">
          <li><a class="current wow fadeInUp" href="#" data-filter="*">All</a></li>

          <?php
          $args = array('parent' => 3);
          $categories = get_categories( $args );
          foreach($categories as $category) {
              echo '<li>';
              echo '<a class="wow fadeInUp" href="#" data-filter=".' . $category->slug.'" data-wow-delay="0.2s">' . $category->name.'</a>';
              echo '</li>';
          }
          ?>

          <li><a class="wow fadeInUp" href="#" data-filter=".illustration" data-wow-delay="0.2s">Illustration</a></li>
          <li><a class="wow fadeInUp" href="#" data-filter=".marketing" data-wow-delay="0.4s">Marketing</a></li>
          <li><a class="wow fadeInUp" href="#" data-filter=".photography" data-wow-delay="0.6s">Photography</a></li>
          <li><a class="wow fadeInUp" href="#" data-filter=".webdesign" data-wow-delay="0.6s">Web Design</a></li>
        </ul>
      </div>
    </div>
  </div>
  <ul class="works-grid works-grid-gut works-grid-5 works-hover-w" id="works-grid">

    <?php

    $query = new WP_Query( array( 'post_type' => 'portfolio', 'order' => 'ASC', 'posts_per_page' => 12,) );


    if ( $query->have_posts() ) : ?>
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
      <?php $post_slug = get_post_field( 'post_name', get_post() ); ?>
    <li class="work-item <?php

    $post_slug = get_post_field( 'post_name', get_post() );

    echo $category->slug;


    ?>" data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" >
      <div class="work-image">
    <?php if ( has_post_thumbnail() ) {
      $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
      echo '<a href="'.get_permalink( $post->ID ).'" >';
       echo '<img width="100%" src="' . $image_src_thumbnail[0] . '" alt="Portfolio Item">';
       echo '</a>';
     }

    ?>
  </div>
     </li>
  <?php endwhile; wp_reset_postdata(); ?>
  <?php endif; ?>

    <li class="work-item illustration webdesign"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-1.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Corporate Identity</h3>
          <div class="work-descr">Illustration</div>
        </div></a>
      </li>




    <li class="work-item marketing photography"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-2.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Bag MockUp</h3>
          <div class="work-descr">Marketing</div>
        </div></a></li>
    <li class="work-item illustration photography"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-3.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Disk Cover</h3>
          <div class="work-descr">Illustration</div>
        </div></a></li>
    <li class="work-item marketing photography"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-4.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Business Card</h3>
          <div class="work-descr">Photography</div>
        </div></a></li>
    <li class="work-item illustration webdesign"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-5.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Business Card</h3>
          <div class="work-descr">Webdesign</div>
        </div></a></li>
    <li class="work-item marketing webdesign"><a href="portfolio-single-1.html">
        <div class="work-image"><img src="assets/images/work-6.jpg" alt="Portfolio Item"/></div>
        <div class="work-caption font-alt">
          <h3 class="work-title">Business Cards in paper clip</h3>
          <div class="work-descr">Marketing</div>
        </div></a></li>
  </ul>
</section>

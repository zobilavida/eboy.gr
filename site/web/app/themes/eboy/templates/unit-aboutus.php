<?php
// query for the about page
$your_query = new WP_Query( 'pagename=about-us' );
// "loop" through query (even though it's just one page)
while ( $your_query->have_posts() ) : $your_query->the_post();
$sub = get_post_meta($id, 'subtitle', true);
$title_1 = get_post_meta($id, 'title_1', true);
$text_1 = get_post_meta($id, 'text_1', true);
$title_2 = get_post_meta($id, 'title_2', true);
$text_2 = get_post_meta($id, 'text_2', true);
$title_3 = get_post_meta($id, 'title_3', true);
$text_3 = get_post_meta($id, 'text_3', true);
$subtitle_3 = get_post_meta($id, 'subtitle_3', true);
$text_4 = get_post_meta($id, 'text_4', true);
$subtitle_4 = get_post_meta($id, 'subtitle_4', true);
?>
<section id="slide01" class="content-block">
    <div class="container-fluid no-padding">
      <div class="row legend">
        <header class="mx-auto centering">
            <h1 class="slideInUp slideInUp2"><?php the_title(); ?></h1>
              <h2 class="slideInUp slideInUp2"><?php echo $sub; ?></h2>
        </header>
      </div>
<svg class="uvc-svg-triangle" xmlns="http://www.w3.org/2000/svg" version="1.1" fill="#f8f8f9" width="100%" height="30" viewBox="0 0 0.156661 0.1" style="height: 30px;"><polygon points="0.156661,3.93701e-006 0.156661,0.000429134 0.117665,0.05 0.0783307,0.0999961 0.0389961,0.05 -0,0.000429134 -0,3.93701e-006 0.0783307,3.93701e-006 "></polygon></svg>

<div class="container">
<div class="row slide fs">
  <div class="col-md-6">
    <h3><?php echo $title_1; ?></h3>
    <div class="in_text">
<?php echo $text_1; ?>
    </div>
    <hr class="style3">
    <h3><?php echo $title_2; ?></h3>
    <div class="in_text">
<?php echo $text_2; ?>
    </div>

</div> <!-- miso div 1 -->
<div class="col-md-6">
  <h3><?php echo $title_3; ?></h3>
    <h5><?php echo $subtitle_3; ?></h5>
  <div class="in_text">
<?php echo $text_3; ?>

  <h5><?php echo $subtitle_4; ?></h5>
<div class="in_text">
<?php echo $text_4; ?>
  </div>


</div> <!-- miso div 1 -->
<div class="col-md-6">

</div>
</div>
</div>
</div> <!-- .container -->
<?php

  endwhile;
  // reset post data (important!)
  wp_reset_postdata();
?>
</section>

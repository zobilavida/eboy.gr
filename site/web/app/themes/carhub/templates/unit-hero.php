<section id="home">
  <!-- First Parallax Section -->
  <div class="jumbotron paral paralsec text-center">
    <h1><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h1>

  <div class="container-fluid">
<?php do_action('carhub_product_carousel', 'product_carousel' ); ?>
  </div>

 </section>

<section class="main">
  <div class="container-fluid p-0">
    <div class="row row-eq-height">
      <div class="col p-0">
<?php

        // are there any rows within within our flexible content?
        	if( have_rows('left_column') ):

        		// loop through all the rows of flexible content
        		while ( have_rows('left_column') ) : the_row();

        		// EMAIL UPDATES
        		if( get_row_layout() == 'text_box_left' )
        			get_template_part( 'template-parts/unit', 'text_box' );

        		// NEW PODCAST
        		if( get_row_layout() == 'image_box_left' )
        			get_template_part( 'template-parts/unit', 'image_box' );

        		endwhile; // close the loop of flexible content
        	endif; // close flexible content conditional

?>
    </div>

    <div class="skyline-back">


  </div>

      <div class="col p-0">
    Right

    </div>
  </div>
  </div>
</section>

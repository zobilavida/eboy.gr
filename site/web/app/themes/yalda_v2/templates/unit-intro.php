<section class="intro">
<div class="container-fluid  ">
<div class="row">
<div class="col-2  ">
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
<div class="col-10 p-0  ">
  <?php    // The Query
     $recentposts = get_posts('numberposts=1&category=211');
     foreach ($recentposts as $post) :
         setup_postdata($post); $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'large' ); ?>
         <div class="container-fluid float-left">
         <div class="row p-0  ">


         <?php if( get_field('photo_01') ): ?>
 <div class="col-9 p-0  ">
         	<img class="img-fluid" src="<?php the_field('photo_01'); ?>" />
             <?php endif; ?>
             <div class="row">
             <div class="col-4  ">

               <?php if( get_field('photo_03') ): ?>
               <img class="img-fluid img3" src="<?php the_field('photo_03'); ?>" />
                  <?php endif; ?>
             </div>
             <div class="col-4  ">

             </div>
             <div class="col-4  ">

               <?php if( get_field('photo_04') ): ?>
               <img class="img-fluid img4" src="<?php the_field('photo_04'); ?>" />
                  <?php endif; ?>
             </div>
             </div>
          </div>



   <?php if( get_field('photo_02') ): ?>
<div class="col-3 p-0   ">
    <img class="img-fluid img2" src="<?php the_field('photo_02'); ?>" />
    </div>
<?php endif; ?>


        </div>
        </div>




</div>

</div>
<div class="row  ">
  <?php if( get_field('photo_05') ): ?>
 <div class="col-12 py-5   ">
   <img class="img-fluid img5" src="<?php the_field('photo_05'); ?>" />
   </div>
 <?php endif; ?>

</div>
<?php endforeach; ?>
</div>

</section>

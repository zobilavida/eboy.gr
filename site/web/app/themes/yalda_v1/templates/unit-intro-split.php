<section class="intro-split">
<div class="container-fluid">
  <div class="row">


    <?php    // The Query
       $recentposts = get_posts('numberposts=1&category=38');
       foreach ($recentposts as $post) :
           setup_postdata($post);  $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');  ?>
           <div class="col-lg-12 my-auto p-5">
             <a href="<?php the_permalink() ?>"><h1><?php the_title(); ?></h1></a>
             <?php echo apply_filters( 'the_excerpt', $post->post_excerpt ); ?>
             <div class="social-bar row p-5">
               <div class="col-6">
               Facebook
               </div>
               <div class="col-6">
               Twitter
               </div>
               <div class="col-6">
               Instagram
               </div>
               <div class="col-6">

               </div>
             </div>
           </div>

           <div class="col-lg-12 right-image" style="background-image: url('<?php echo $featured_img_url ?>'); height: 100%; background-repeat: no-repeat;" >
    tyest

           </div>

    <?php endforeach; ?>


</section>

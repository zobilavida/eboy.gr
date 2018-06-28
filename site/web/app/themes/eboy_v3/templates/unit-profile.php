<section id="profile" class="profile pt-5">

<div class="container-fluid p-5">
    <div class="d-flex flex-row flex-wrap align-items-center h-100 test intro">
        <div class="col-md-8 p-0 test">
          <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
            <div class="col-12 px-0 pb-5 test">
          <?= get_post_field('post_content', $post->ID) ?>
            </div>
              <div class="col-12 px-0 pb-4 test">
            <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
            <div class="col-12 p-0 test">
              <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
                <div class="col-2 p-0 test">
                  <h3>Phone:</h3>
                </div>
                <div class="col-10 p-0 test">
                <?= get_post_field('phone', $post->ID) ?>
                </div>
              </div>
            </div>
            <div class="col-12 p-0 test">
              <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
                <div class="col-2 p-0 test">
                  <h3>Address:</h3>
                </div>
                <div class="col-10 p-0 test">
                <?= get_post_field('address', $post->ID) ?>
                </div>
              </div>
            </div>
            </div>
            </div>
            <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
            <div class="col-6 pl-0 pr-2">
          <button type="button" class="btn btn-primary custom-btn btn-lg" data-toggle="modal" data-target="#contactModal">Contact me</button>
          </div>
          <div class="col-6 pl-2 pr-0">
            <a class="btn btn-secondary custom-btn btn-lg" href="https://eboy.gr/app/uploads/2018/06/cv_giannis-sergentakis.pdf" role="button">Download CV</a>

        </div>
        <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="d-flex justify-content-between">
  <div class="p-2"></div>
  <div class="p-2">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>

  </div>
</div>
      <div class="modal-body">
        <?php
if (function_exists('wpcf7')) {
  echo do_shortcode( '[contact-form-7 id="413" title="Contact form 1"]' );
  } else {
    echo "<p class='title'>Please install <a href='http://wordpress.org/plugins/contact-form-7/' rel='nofollow'>Contact Form 7</a> plugin and build some contact form. Then use your shortcode on the contact page.</p>";
  }


?>


      </div>

    </div>
  </div>
</div>
            </div>
        </div>
        </div>
        <?php
    if (has_post_thumbnail( $post->ID ) ):
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
?>
        <div class="col-md-4 p-0 profile_photo"><img src="<?php echo $image[0]; ?>" alt="..." class="rounded-circle mx-auto d-block"></div>
<?php endif; ?>
</div>
</section>

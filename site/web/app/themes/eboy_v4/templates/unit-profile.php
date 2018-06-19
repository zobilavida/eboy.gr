<section id="profile" class="profile pt-5">


    <div class="d-flex flex-row flex-wrap align-items-center h-100 test intro">
        <div class="col-8 p-0 test">
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
          <button type="button" class="btn btn-primary custom-btn btn-lg btn-block" data-toggle="modal" data-target="#contactModal">Contact me</button>
          </div>
          <div class="col-6 pl-2 pr-0">
        <button type="button" class="btn btn-secondary custom-btn btn-lg btn-block">Download CV</button>
        </div>
        <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-body">

        <form action="javascript:void(null);" method="post" id="form_contact">
          <div class="d-flex flex-row">


            <div class="py-5 px-5 col-5 test">
              <h1 class="pb-5">Contact Me<span class="dot p-1">.</span></h1>
              <label for="user_name">Name</label>
              <input type="text" id="user_name" name="name" class="form-control form-control-lg mb-4">
              <label for="user_email">Email</label>
              <input type="email" id="user_email" name="email" class="form-control form-control-lg mb-4">
              <label for="customCheck1">Verification</label>
              <div class="custom-control custom-checkbox">

                <input type="checkbox" class="custom-control-input" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">I' m not a robot</label>
              </div>
            	</div>
            <div class="mt-5 px-0 col-7 message-area">
              <label for="user_comment" class="p-4">Message</label>
              <textarea id="user_comment" name="comment" class="form-control form-control-lg p-4"></textarea>
              <button type="button" class="btn btn-primary btn-lg btn-block custom-btn contact_btn" disabled>Submit</button>

            </div>

          </div>

        </form>
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
        <div class="col-4 p-0 test"><img src="<?php echo $image[0]; ?>" alt="..." class="rounded-circle mx-auto d-block"></div>
<?php endif; ?>

</section>

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
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php gravity_form_enqueue_scripts( 1, true ); ?>
        <?php gravity_form(1, false, false, false, '', true, 12); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
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

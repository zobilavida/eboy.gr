<section id="education" class="education pt-5">
<div class="container-fluid p-5">
  <div class="d-flex flex-row flex-wrap align-items-start h-100 test">
        <div class="col-12 col-md-4 p-0 test"><h1>Education<span class="dot p-1">.</span></h1></div>
        <div class="col-12 col-md-8 p-0 test">
          <?php if( have_rows('educations') ): ?>
              <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
              <?php while( have_rows('educations') ): the_row(); ?>
                  <div class="col-12 p-0 test"><?php the_sub_field('education'); ?></div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>

        </div>
    </div>
</div>
</section>

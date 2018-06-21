<section id="experience" class="experience pt-5">

  <div class="d-flex flex-row flex-wrap align-items-start h-100 test">
        <div class="col-12 col-md-4 pr-5 pl-0 py-0 test"><h1>Experience<span class="dot p-1">.</span></h1><span>I am inspired by creating great work with people who are as passionate as I am about building something awesome.</span></div>
        <div class="col-12 col-md-8 p-0 test">
          <?php if( have_rows('past_jobs') ): ?>
              <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
              <?php while( have_rows('past_jobs') ): the_row(); ?>
                  <div class="col-12 p-0 test"><?php the_sub_field('past_job'); ?></div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>

        </div>

    </div>
</section>

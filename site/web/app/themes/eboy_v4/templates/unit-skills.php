<section id="skills" class="skills pt-5">

  <div class="d-flex flex-row flex-wrap align-items-start h-100 test">
        <div class="col-12 col-md-4 pr-5 pl-0 py-0 test"><h1>Skills<span class="dot p-1">.</span></h1><span>I am inspired by creating great work with people who are as passionate as I am about building something awesome.</span></div>
        <div class="col-12 col-md-8 p-0 test">
          <?php if( have_rows('skills') ): ?>
              <div class="d-flex flex-row flex-wrap align-items-center h-100 test">
              <?php while( have_rows('skills') ): the_row(); ?>
                  <div class="col-12 col-md-6 p-0 test"><?php the_sub_field('skill'); ?></div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>

        </div>

    </div>
</section>

<section id="skills" class="skills pt-5">
<div class="container-fluid p-5">
  <div class="d-flex flex-row flex-wrap align-items-start h-100">
        <div class="col-12 col-md-4 pr-5 pl-0 pt-0 pb-5"><h1>Skills<span class="dot p-1">.</span></h1><span>I enjoy building brands and make them live on paper or on the web, but i can go further than that and deploy super-fast and secure applications on cloud servers.</span></div>
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
        </div>
</section>

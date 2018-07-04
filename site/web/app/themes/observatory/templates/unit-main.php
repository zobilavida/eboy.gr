<section class="main">
  <div class="d-flex justify-content-between flex-wrap">
    <div class="left p-0">
      <div class="d-flex flex-column">
      <div class="text-block p-5 m-3 wow bounceInUp">
        <?php $page = get_page_by_title( 'About' ); ?>
        <?php $title = apply_filters('the_content', $page->post_title);
        echo '<h1>';
          echo $title;
          echo '</h1>';
         ?>
      </div>
      <div class="box img_01 p-2 wow bounceInUp"></div>
      <div class="box img_02 p-2 wow bounceInUp"></div>
      <div class="box img_03 p-2 wow bounceInUp"></div>
    </div>
    </div>
    <div class="d-none d-sm-none d-md-block skyline"></div>
    <div class="right p-0">
      <div class="d-flex flex-column">
      <div class="box img_04 p-2 wow bounceInUp"></div>
      <div class="text-block_2 p-5 wow bounceInUp">
        <h1>23rd  Fl<span class="underline">o</span><span class="underline">o</span>r</h1>
          <h2>Lorem ipsum dolor sit amet</h2>
          <p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in </p>
          <div class="d-flex justify-content-center p-5">
            <button type="button" class="btn btn-outline-secondary btn-lg">Large button</button>
          </div>      </div>
      <div class="box img_05 p-2 wow bounceInUp"></div>
    </div>
    </div>
  </div>
</section>

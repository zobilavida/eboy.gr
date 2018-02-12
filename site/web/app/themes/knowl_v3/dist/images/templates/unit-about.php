<section class="module" id="about">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-8 col-sm-offset-2">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Εταιρική ταυτότητα' );

        $title = apply_filters('the_content', $page->post_title);
        echo $title;
        ?></h2>
        <div class="module-subtitle font-serif large-text">
<?php
$page = get_page_by_title( 'Εταιρική ταυτότητα' );
$content = apply_filters('the_content', $page->post_excerpt);
echo $content;
?>
</div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-sm-2 col-sm-offset-5">
        <div class="large-text align-center"><a class="section-scroll" href="#team"><i class="fa fa-angle-down"></i></a></div>
      </div>
    </div>
  </div>
</section>

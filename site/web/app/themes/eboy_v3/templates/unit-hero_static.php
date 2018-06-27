  <section id="top" class="bg-white top mt-5">
    <div class="header-content container-fluid p-5">
      <div class="row">
        <div class="col-12 p-0">
          <h1 ><?= get_post_field('post_title', $post->ID) ?></h1>

   <?= get_post_field('post_content', $post->ID) ?>

      </div>
      </div>
    </div>
</section>

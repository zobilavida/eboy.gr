  <section id="top" class="bg-white top mt-5">
    <div class="header-content container py-5">
      <blockquote class="blockquote">
  <p class="mb-0"><?= get_post_field('post_title', $post->ID) ?></p>
  <footer class="blockquote-footer">  <?= get_post_field('post_content', $post->ID) ?></footer>
</blockquote>

    </div>
</section>

<div class="container hero">
  <div class="row">
    <div class="col-lg-12 col-xs-12 hero">
      <?
    $intro = get_page_by_path('intro');
$content = $intro->post_content;
echo $content
?>

</div>
</div>
<div class="row">
  <div class="col-12">
  <img src="<?= get_template_directory_uri(); ?>/dist/images/icon_viewcars.svg" class="icon_viewcars wp-post-image" alt="View all cars">
  </div>
</div>


</div>

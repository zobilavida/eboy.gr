<section id="home" >
  <div class="container">
   <div class="row text-left align-items-center">
    <div class="col-12">
  <div class="jumbotron jumbotron-fluid mb-0">
      <?
      $intro = get_page_by_path('intro');
$content = $intro->post_content;
echo $content
?>
     </div>
    </div>
   </div>
  </div>
 </section>

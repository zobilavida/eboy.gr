<section id="home" >

  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">Save changes</button>
        </div>
      </div>
    </div>
  </div>
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

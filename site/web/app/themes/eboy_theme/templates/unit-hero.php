<section id="home" >

  <!-- Modal -->
  <div class="modal fade" id="feedback-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-6 contact-left">
              <h1>  Let’s get in touch </h1>
              <h2>I’m currently accepting new freelance projects,
and willing to hear interesting proposals.</h2>
<h2>Feel free to call, send email or simpy complete
the enquiry form.</h2>
<div class="col-12 no-gutters phone">
<span class="form_contact "> 0030 6987.16.17.60</span>
    </div>
    <div class="col-12">
    <span class="form_contact skype"> Giannis Sergentakis</span>
        </div>

          </div>
          <div class="col-6 contact-right">
            <form class="feedback" name="feedback">
            <strong>Name</strong>
            <br />
            <input type="text" name="name" class="input-xlarge" value="">
            <br /><br /><strong>Email</strong><br />
            <input type="email" name="email" class="input-xlarge" value="">
            <br /><br /><strong>Message</strong><br />
            <textarea name="message" class="input-xlarge"></textarea>
            </form>
              <button class="btn btn-success" id="submit">Send</button>
      </div>
          </div>
          </div>




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

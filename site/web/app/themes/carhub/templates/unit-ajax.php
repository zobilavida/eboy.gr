<section id="forma" class="forma">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center feedback">
        <article id="post-<?php the_ID() ?>" <?php post_class() ?>>

          <div class="entry-content">
            <!-- THIS IS WHERE THE FORM HTML WILL GO -->
            <form id="contact-form">
  <input type="hidden" name="action" value="contact_send" />
  <input type="text" name="name" placeholder="Your name..." />
  <input type="email" name="email" placeholder="Your email..." />
  <textarea name="message" placeholder="Your message..."></textarea>
  <input type="submit" value="Send Message" />
</form>
          </div>
          <!-- THIS IS WHERE WE WILL PLACE THE AJAX -->
        </article>
      </div>
    </div>
  </div>

 </section>

<section class="module" id="alt-features">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Ιστορικό' );

        $title = apply_filters('the_content', $page->post_title);
        echo '<a href="https://eboy.gr/app/uploads/sites/4/2018/01/Alternative_Video_2.mp4">';
        echo $title;
        echo '</a>';
        ?></h2>
        <div class="module-subtitle font-serif"><?php
        $page = get_page_by_title( 'Ιστορικό' );

        $excerpt = apply_filters('the_content', $page->post_excerpt);
        echo $excerpt;
        ?></div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3 col-lg-3">
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-strategy"></span></div>
          <h3 class="alt-features-title font-alt">Branding</h3>Έλαβε διεθνή πιστοποίηση από το International Coach Federation.
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-tools-2"></span></div>
          <h3 class="alt-features-title font-alt">Development</h3>Βραβεύθηκε στα Education Business Awards 2016 για τον «Επιταχυντή Δεξιοτήτων Mellon».
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-target"></span></div>
          <h3 class="alt-features-title font-alt">Marketing</h3>Συμμετέχει ως βασικός εταίρος σε 13 ευρωπαϊκά έργα και με επιστημονική συμβολή σε ακόμα 12+ ευρωπαϊκά έργα.
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-tools"></span></div>
          <h3 class="alt-features-title font-alt">Design</h3>Διεξήγαγε ως επιστημονικός υπεύθυνος μεγάλης κλίμακας έρευνες για τη γυναικεία επιχειρηματικότητα και την επαγγελματική επιτυχία, και συμμετέχει επιστημονικά ως εθνικός εταίρος σε σειρά ερευνών-αναγκών στο πλαίσιο ευρωπαϊκών έργων.
        </div>
      </div>
      <div class="col-md-6 col-lg-6 hidden-xs hidden-sm">
        <div class="alt-services-image align-center">
          <?php
  if ( has_post_thumbnail() ) {
  the_post_thumbnail();
  }  ?>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 col-lg-3">
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-camera"></span></div>
          <h3 class="alt-features-title font-alt">Photography</h3>Επιλέχθηκε ως επίσημος εταίρος της Ψηφιακής Συμμαχίας για τη Γυναικεία Απασχόληση για την αντιμετώπιση της ανεργίας των γυναικών μέσω των ΤΠΕ, μια πρωτοβουλία
της Γενικής Γραμματείας Ισότητας των Φύλων, Υπ. Εσωτερικών.
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-mobile"></span></div>
          <h3 class="alt-features-title font-alt">Mobile</h3>Συν & διοργάνωσε 30+ εκδηλώσεις #edu#startup#skills#VET με 6.000+ συμμετέχοντες.
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-linegraph"></span></div>
          <h3 class="alt-features-title font-alt">Music</h3>A wonderful serenity has taken possession of my entire soul like these sweet mornings.
        </div>
        <div class="alt-features-item">
          <div class="alt-features-icon"><span class="icon-basket"></span></div>
          <h3 class="alt-features-title font-alt">Shop</h3>A wonderful serenity has taken possession of my entire soul like these sweet mornings.
        </div>
      </div>
    </div>
  </div>
</section>

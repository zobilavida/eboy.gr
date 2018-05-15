<footer class="footer">

  <div class="container-fluid content-info">
    <div class="row">
          <div class="col-12">
            <div class="container py-5">
              <div class="row">

    <?php dynamic_sidebar('sidebar-footer'); ?>
    </div>
  <div class="row d-flex justify-content-center">

<?php dynamic_sidebar('sidebar-footer-bottom'); ?>
</div>
        </div>
    </div>
    </div>
  </div>
  <div class="row">
    <div class="container-fluid">
    <?php do_action( 'demetrios_footer', 'demetrios_footer_buttons' ); ?>
      </div>
  </div>
</footer>

<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-sm-6 col-2 h-100 bg-dark text-white py-2 d-flex align-items-center justify-content-center fixed-top" id="left">
          <?php do_action( 'custom_store_finder', 'store_finder' ); ?>
        </div>
        <div class="col-sm-6 invisible col-2"><!--hidden spacer--></div>
        <div class="col offset-2 offset-sm-6 py-2">
            <?php do_action( 'custom_store_spilt_finder', 'store_finder_split' ); ?>
        </div>
    </div>
</div>

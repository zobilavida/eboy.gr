<div class="header bg-light p-0 d-none d-sm-block">

    <div class="container">
        <div class="row">

            <div class="col-3">
                <?php dynamic_sidebar('sidebar-header-left'); ?>
            </div>
            <div class="col-9 text-right">
              <div class="container p-0">
                  <div class="d-flex justify-content-end">

                <?php dynamic_sidebar('sidebar-header-right'); ?>
              </div>
          </div>
            </div>
        </div>
        <!--/row-->
    </div>
    <!--container-->
</div>
<?php do_action('demetrios_custom_header', 'custom_header'); ?>

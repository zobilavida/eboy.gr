<div class="header bg-light sticky-top p-2">
    <div class="container">
        <div class="row">
            <div class="col-6">
                <?php dynamic_sidebar('sidebar-header-left'); ?>
            </div>
            <div class="col-6 text-right">
                <?php dynamic_sidebar('sidebar-header-right'); ?>
            </div>
        </div>
        <!--/row-->
    </div>
    <!--container-->
</div>
<?php do_action('demetrios_custom_header', 'custom_header'); ?>

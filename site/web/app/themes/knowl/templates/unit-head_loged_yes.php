  <div class="col-lg-3 col-md-4 col-xs-12 navbar-text flex-items-lg-right">
<div class="row">
  <span class="navbar_text">
<?php
echo  '' . __('Welcome:', 'sage') . '';
?>
</span>
<div class="item-header-avatar">
	  <a href="<?php echo bp_loggedin_user_domain(); ?>">

		<?php

    echo bp_core_fetch_avatar( array(
    			'item_id' => bp_loggedin_user_id(),
    			'width' => 21,
    			'height' => 21,
    			'class' => 'avatar',
    			)
    		);
   ?>

	</a>
</div><!-- #item-header-avatar -->
<span class="navbar_user">
  <a href="<?php echo bp_loggedin_user_domain(); ?>">
<?php echo bp_core_get_user_displayname( bp_loggedin_user_id() );?>
        </a>

</span>
<div class="navbar_notifications">
        <?php my_bp_adminbar_notifications_menu()?>
        <a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout">Logout</a>


</div>





</div>
</div>

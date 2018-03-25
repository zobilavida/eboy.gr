<?php
/*
Plugin Name: Custom Hooks
Plugin URI: https://facetwp.com/
Description: A container for custom hooks
Version: 1.0
Author: Matt Gibbs
*/



function fwp_load_more() {
?>
<script>

  $('.button-edit').on('click touchend', function () {
        $('.button-edit').animate({ opacity: 0 }, 1000);
      });

</script>
<?php
}
add_action( 'wp_head', 'fwp_load_more', 99 );
add_filter( 'facetwp_template_force_load', '__return_true' );

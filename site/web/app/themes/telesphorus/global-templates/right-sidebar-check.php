<?php
/**
 * Right sidebar check.
 *
 * @package telesphorus
 */
?>

<?php $sidebar_pos = get_theme_mod( 'telesphorus_sidebar_position' ); ?>

<?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>

  <?php get_sidebar( 'right' ); ?>

<?php endif; ?>

<?php
/**
 * Adds the link to permalink.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */
 global $product;
$id = $product->get_id();
?>
<a href="<?php the_permalink(); ?>" class="product-images" aria-label="<?php the_title_attribute(); ?>">

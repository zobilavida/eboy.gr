<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package observatory_giannis
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site h-100">

	<header class="banner">
	  <nav class="navbar fixed-top navbar-dark bg-trans">
	    <button class="hamburger hamburger--elastic" type="button">
	      <span class="hamburger-box">
	        <span class="hamburger-inner"></span>
	      </span>
	    </button>
	      <div class="navbar-collapse collapse pl-3" id="collapsingNavbar">
	          <ul class="navbar-nav">
	              <li class="nav-item active">
	                  <a class="nav-link" href="#">Home <span class="sr-only">Home</span></a>
	              </li>
	              <li class="nav-item">
	                  <a class="nav-link" href="#features">Features</a>
	              </li>
	              <li class="nav-item">
	                  <a class="nav-link" href="#myAlert" data-toggle="collapse">Wow</a>
	              </li>
	          </ul>
	          <ul class="navbar-nav ml-auto">
	              <li class="nav-item">
	                  <a class="nav-link" href="" data-target="#myModal" data-toggle="modal">About</a>
	              </li>
	          </ul>
	      </div>
	  </nav>
	</header>

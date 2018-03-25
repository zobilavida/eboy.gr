<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
?>
<script type="text/javascript">
	/* Impreza elements customizations */

	(function($){
		"use strict";

		$.fn.wSearch = function(){
			return this.each(function(){
				var $this = $(this),
					$input = $this.find('input[name="s"]'),
					focusTimer = null;

				var show = function(){
					$this.addClass('active');
					focusTimer = setTimeout(function(){
						$input.focus();
					}, 300);
				};

				var hide = function(){
					clearTimeout(focusTimer);
					$this.removeClass('active');
					$input.blur();
				};

				$this.find('.w-search-open').click(show);
				$this.find('.w-search-close').click(hide);
				$input.keyup(function(e){
					if (e.keyCode == 27) hide();
				});

			});
		};

		$(function(){
			jQuery('.w-search').wSearch();
		});
	})(jQuery);

	jQuery('.w-tabs').wTabs();

	jQuery(function($){
		$('.w-blog').wBlog();
	});

	jQuery(function($){
		$('.w-portfolio').wPortfolio();
	});
</script>
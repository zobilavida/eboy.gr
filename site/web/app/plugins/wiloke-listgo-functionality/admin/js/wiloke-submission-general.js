/**
 * Created by Wiloke on 6/13/17.
 */
(function ($) {
	'use strict';
	function simpleUpload($this) {
		// Set all variables to be used in scope
		let frame;
		// ADD IMAGE LINK
		$this.on( 'click', function( event ){

			event.preventDefault();
			let $target = $(this);

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create a new media frame
			frame = wp.media({
				title: 'Select or Upload Media Of Your Chosen Persuasion',
				button: {
					text: 'Use this media'
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});


			// When an image is selected in the media frame...
			frame.on( 'select', function() {

				// Get media attachment details from the frame state
				let attachment = frame.state().get('selection').first().toJSON();

				// Send the attachment URL to our custom image input field.
				$target.css('background-image', 'url('+attachment.url+')');

				// Send the attachment id to our hidden input
				$target.prev().val(attachment.url);
			});

			// Finally, open the modal on click
			frame.open();
		});
	}
	
	function importDemo() {
		let $setup = $('.wiloke-setup');

		$setup.on('submit', ((event)=>{
			event.preventDefault();
			let $this = $(event.currentTarget),
				$msg = $this.find('.message'),
				$btn = $this.find('.button');

			$msg.removeClass('error').addClass('success');
			$msg.find('.error').remove();
			$btn.addClass('loading');
			$msg.addClass('visible');

			ajaxImportDemo($this);
		}));
	}

	function ajaxImportDemo($this, didit) {
		let canUnzip = (typeof didit !== 'undefined');
		let $msg = $this.find('.message');

		$.ajax({
			type: 'POST',
			data: {action: 'wiloke_listgo_importing_demo', data: $this.serialize(), security: $('#wiloke-setup-nonce-field').val(), didit: didit, canUnzip: canUnzip},
			url: ajaxurl,
			success: function(response){
				if ( response.success ){
					if ( typeof response.data.done === 'undefined' ){
						$msg.find('.list').append('<li>'+response.data.msg+'</li>');
						let nextProgress = setTimeout(function () {
							ajaxImportDemo($this, response.data.didit);
							clearTimeout(nextProgress);
						}, 500);
					}else{
						if ( typeof response.data.item_error !== 'undefined' && response.data.item_error ){
							$msg.append('<p class="error">'+response.data.msg+'</p>');
						}else{
							$msg.append('<p>'+response.data.msg+'</p>');
						}

						$msg.find('.system-running').remove();
						$this.find('.button').removeClass('loading');
					}
				}else{
					$msg.removeClass('success').addClass('error').html('<p class="error">'+response.data.msg+'</p>');
					$this.find('.button').removeClass('loading');
				}
			}
		})
	}
	
	$(document).ready(function () {
		if ( $().accordion ){
			$('.ui.accordion').accordion();
		}

		if ( $().dropdown ){
			$('.ui.dropdown').each(function(){
				$(this).dropdown({
					forceSelection: false
				});
			});

			$('.ui.selection').on('click', 'a.ui.label', function (event) {
				event.preventDefault();
			})
		}

		if ( $().tab ){
			$('.menu .item').tab();
		}

		$(document).on('click', '.selection .ui.label', function (event) {
			event.preventDefault();
		});

		$('.wiloke-js-upload-badge').each(function () {
			simpleUpload($(this));
		});

		if ( $().magnificPopup ){
			$('.single-popup').magnificPopup({
				type: 'image',
				closeOnContentClick: true,
				closeBtnInside: false,
				fixedContentPos: true,
				mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
				image: {
					verticalFit: true
				},
				zoom: {
					enabled: true,
					duration: 300 // don't foget to change the duration also in CSS
				}
			});
			$('.gallery-popup').magnificPopup({
				delegate: 'a',
				type: 'image',
				tLoading: 'Loading image #%curr%...',
				mainClass: 'mfp-img-mobile',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0,1] // Will preload 0 - before current, and 1 after the current image
				},
				zoom: {
					enabled: true,
					duration: 300 // don't foget to change the duration also in CSS
				}
			});
		}

		if ( $().popup ){
			$('.single-popup, .gallery-popup a').each(function () {
				$(this).popup({
					position: 'bottom center',
					content: 'Click to show me on a popup'
				});
			});

		}

		importDemo();
	});
})(jQuery);
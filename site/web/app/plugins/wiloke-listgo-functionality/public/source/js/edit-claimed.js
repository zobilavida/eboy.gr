;(function ($) {
	'use strict';
	class WilokeEditClaimedListing{
		constructor(){
			this.$app = $('#wiloke-form-claimed-listing');
			this.editListing();
			this.removeGallery();
		}

		getContent(){
			if ($("#wp-listing_content-wrap").hasClass("tmce-active")){
				return tinyMCE.activeEditor.getContent();
			}else{
				return $('#listing_content').val();
			}
		}

		removeGallery(){
			this.$app.on('click', '.wil-addlisting-gallery__list-remove', function () {
				if ( WILOKE_GLOBAL.isLoggedIn === 'no' ){
					$(this).closest('#wiloke-show-gallery').empty();
				}else{
					$(this).closest('.gallery-item').remove();
				}
			});
		}

		requestGallery(){
			let $galleryID = this.$app.find('#wiloke-preview-gallery'),
				galleryIDs = [];

			$galleryID.find('.gallery-item').each(function () {
				if ( typeof $(this).data('id') !=='undefined' ){
					galleryIDs.push($(this).data('id'));
				}
			});

			let convertGalleryToString = galleryIDs.join(',');
			this.$app.find('#listing_gallery').val(convertGalleryToString);
		}

		editListing(){
			let $submitEditBtn = $('#wiloke-listgo-edit-listing-claimed');

			if ( !$submitEditBtn.length ){
				return false;
			}

			this.$printMessage = this.$app.find('#wiloke-print-msg-here');

			this.$app.on('submit', ((event)=>{
				event.preventDefault();

				let $target = $(event.target);

				$submitEditBtn.addClass('loading');

				let xhr = null;

				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}

				let content = this.getContent();

				this.requestGallery();


				xhr = $.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'wiloke_edit_listing_claimed',  data: $target.serialize(), content: content, security: WILOKE_GLOBAL.wiloke_nonce},
					success: (response=>{
						if ( response.success ){
							window.location.href = this.$app.data('url');
						}else{
							let messages = '';
							_.forEach(response.data, ((message, id)=>{
								let $target = $('#'+id);
								if ($target.length ){
									$target.html(message).removeClass('hidden');
									$target.parent().removeClass('hidden').addClass('validate-required');
								}else{
									messages += '<p class="update-status error-msg">'+message+'</p>';
								}
							}));

							this.$printMessage.html(messages);
						}

						$submitEditBtn.removeClass('loading');
					})
				})
			}));
		}
	}

	$(window).load(function () {
		new WilokeEditClaimedListing();
	});
})(jQuery);